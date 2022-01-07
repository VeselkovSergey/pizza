<?php


namespace App\Http\Controllers\ARM;

use App\Helpers\ResultGenerate;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Ingredients\IngredientsController;
use App\Http\Controllers\Orders\OrdersController;
use App\Http\Controllers\Products\ProductsController;
use App\Models\Orders;
use App\Models\ProductModificationsIngredients;
use App\Models\ProductsModificationsInOrders;
use App\Models\Supply;
use App\Models\User;

class AdministratorARMController extends Controller
{
    public function Index()
    {
        return view('arm.administration.index');
    }

    public function Users()
    {
        $users = User::all();
        return view('arm.administration.users.index', compact('users'));
    }

    public function UserOrders($userId)
    {
        $user = User::find($userId);
        $orders = $user->Orders;
        return view('arm.administration.users.orders', compact('user', 'orders'));
    }

    public function Orders()
    {
        $startDate = (request()->get('start-date') === null) ? date('Y-m-d', time()) : request()->get('start-date');
        $endDate = (request()->get('end-date') === null) ? date('Y-m-d', time()) : request()->get('end-date');
        if (request()->get('all')) {
            $orders = OrdersController::AllOrders('asc');
            $supplySum = Supply::SuppliesSum();
        } else {
            $orders = OrdersController::OrdersByDate($startDate, $endDate, true, 'asc');
            $supplySum = Supply::SuppliesSumByDate($startDate, $endDate);
        }

        return view('arm.administration.orders.index', compact('orders', 'supplySum', 'startDate', 'endDate'));
    }

    public function Products()
    {
        $products = ProductsController::ALlProducts();
        return view('arm.administration.products.index', compact('products'));
    }

    public function ProductSaveChanges()
    {
        $productId = request()->productId;
        $data = json_decode(request()->data);
        $product = ProductsController::GetProductById($productId);
        ProductsController::SaveChanges($product, $data);
        return ResultGenerate::Success();
    }

    public function UserSaveChanges()
    {
        $userId = request()->userId;
        $data = json_decode(request()->data);
        $user = AuthController::GetUserById($userId);
        AuthController::SaveChanges($user, $data);
        return ResultGenerate::Success();
    }

    public function ProductsModification()
    {
        $orders = Orders::all();

        $productsModifications = [];
        $sumOrders = 0;
        $costOrders = 0;

        foreach($orders as $order) {
            if ($order->IsCancelled()) {
                continue;
            }

            $sumOrders += $order->order_amount;

            $productsModificationsInOrder = OrdersController::OrderProductsModifications($order);
            foreach($productsModificationsInOrder as $productModificationInOrder) {
                /** @var ProductsModificationsInOrders $productModificationInOrder */

                $title = $productModificationInOrder->ProductModifications->Product->title . ' ' . $productModificationInOrder->ProductModifications->Modification->title . ' ' . $productModificationInOrder->ProductModifications->Modification->value;
                $price = $productModificationInOrder->ProductModifications->selling_price;
                $amount = $productModificationInOrder->product_modification_amount;
                $categoryTitle = $productModificationInOrder->ProductModifications->Product->Category->title;

                $costPrice = 0;
                $modificationIngredients = $productModificationInOrder->ProductModifications->Ingredients;
                foreach($modificationIngredients as $ingredient) {
                    /** @var ProductModificationsIngredients $ingredient */
                    $ingredientAmount = $ingredient->ingredient_amount;
                    $ingredientPrice = $ingredient->Ingredient->CurrentPrice();
                    $sumIngredient = $ingredientAmount * $ingredientPrice;
                    $costPrice += $sumIngredient;
                }

                $costOrders += ($amount * $costPrice);

                if (isset($productsModifications[$productModificationInOrder->product_modification_id])) {
                    $productsModifications[$productModificationInOrder->product_modification_id]->amount += $amount;
                } else {
                    $productsModifications[$productModificationInOrder->product_modification_id] = (object)[
                        'title' => $title,
                        'amount' => $amount,
                        'price' => $price,
                        'costPrice' => $costPrice,
                        'categoryTitle' => $categoryTitle,
                    ];
                }
            }
        }

        return view('arm.administration.products-modifications.index', compact('productsModifications', 'sumOrders', 'costOrders'));
    }

    public function DeviceUsed()
    {
        $orders = OrdersController::AllOrders();
        $devicesInfo = [];
        $typeDevice = [
            'iphone' => 0,
            'android' => 0,
            'pc' => 0,
        ];
        foreach ($orders as $order) {
            $clientRawData = json_decode($order->client_raw_data);

            $user = User::where('phone', $clientRawData->clientPhone)->first();
            if ($user->UserIsManager()) {
                continue;
            }

            $allInformationRawData = json_decode($order->all_information_raw_data);
            $userAgent = $allInformationRawData->userAgent ?? '-';
            $screenWidth = $allInformationRawData->screenWidth ?? '-';
            $screenHeight = $allInformationRawData->screenHeight ?? '-';
            $typeDeviceName = 'Unknown';

            if ($userAgent !== '-') {
                if ($screenWidth < 900) {
                    if (!str_contains($userAgent, 'iPhone')) {
                        $typeDevice['android']++;
                        $typeDeviceName = 'Android';
                    } else {
                        $typeDevice['iphone']++;
                        $typeDeviceName = 'iPhone';
                    }
                } else {
                    $typeDevice['pc']++;
                    $typeDeviceName = 'PC';
                }
            }

            $devicesInfo[] = (object)[
                'userAgent' => $userAgent,
                'screenWidth' => $screenWidth,
                'screenHeight' => $screenHeight,
                'typeDeviceName' => $typeDeviceName,
            ];

        }
        return view('arm.administration.device-used.index', compact('devicesInfo', 'typeDevice'));
    }

    public function Ingredients()
    {
        $ingredients = IngredientsController::AllIngredients();
        return view('arm.administration.ingredients.index', compact('ingredients'));
    }

    public function IngredientSaveChanges()
    {
        $ingredientId = request()->ingredientId;
        $data = json_decode(request()->data);
        $ingredient = IngredientsController::GetIngredientById($ingredientId);
        IngredientsController::SaveChanges($ingredient, $data);
        return ResultGenerate::Success();
    }

    public function SpentIngredients()
    {
        $startDate = (request()->get('start-date') === null) ? date('Y-m-d', time()) : request()->get('start-date');
        $endDate = (request()->get('end-date') === null) ? date('Y-m-d', time()) : request()->get('end-date');

        $ingredientsRaw = IngredientsController::AllIngredients();
        $ingredients = [];
        foreach ($ingredientsRaw as $ingredient) {
            $ingredient->sent = 0;
            $ingredients[$ingredient->id] = $ingredient;
        }

        $orders = OrdersController::OrdersByDate($startDate, $endDate, true, 'ASC');

        $amountSpent = 0;
        foreach($orders as $order) {
            if ($order->IsCancelled()) {
                continue;
            }

            $productsModificationsInOrder = OrdersController::OrderProductsModifications($order);
            foreach($productsModificationsInOrder as $productModificationInOrder) {
                /** @var ProductsModificationsInOrders $productModificationInOrder */

                $amount = $productModificationInOrder->product_modification_amount;

                $modificationIngredients = $productModificationInOrder->ProductModifications->Ingredients;
                foreach($modificationIngredients as $ingredient) {
                    /** @var ProductModificationsIngredients $ingredient */

                    $ingredientAmount = $ingredient->ingredient_amount;
                    $priceIngredient = $ingredientAmount * $amount;
                    $amountSpent += $priceIngredient * $ingredients[$ingredient->ingredient_id]->last_price_ingredient;
                    $ingredients[$ingredient->ingredient_id]->sent += $priceIngredient;
                }
            }
        }
        return view('arm.administration.ingredients.spent', compact('ingredients', 'amountSpent', 'startDate', 'endDate'));
    }
}
