<?php


namespace App\Http\Controllers\ARM;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Orders\OrdersController;
use App\Models\Orders;
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
        $requiredDate = (request()->get('required-date') === null) ? date('Y-m-d', time()) : request()->get('required-date');
        if (request()->get('all')) {
            $orders = OrdersController::AllOrders('asc');
        } else {
            $orders = OrdersController::OrdersByDate($requiredDate, true, 'asc');
        }
        return view('arm.administration.orders.index', compact('orders', 'requiredDate'));
    }

    public function ProductsModification()
    {
        $orders = Orders::all();

        $productsModifications = [];

        foreach($orders as $order) {
//        $clientInfo = json_decode($order->client_raw_data);
//        $rawData = json_decode($order->all_information_raw_data);
            $productsModificationsInOrder = \App\Http\Controllers\Orders\OrdersController::OrderProductsModifications($order);
            foreach($productsModificationsInOrder as $productModificationInOrder) {

                $costPrice = 0;
                $modificationIngredients = $productModificationInOrder->ProductModifications->Ingredients;
                foreach($modificationIngredients as $ingredient) {
                    $sumIngredient = $ingredient->ingredient_amount * $ingredient->Ingredient->CurrentPrice();
                    $costPrice += $sumIngredient;
                }

                $title = $productModificationInOrder->ProductModifications->Product->title . ' ' . $productModificationInOrder->ProductModifications->Modification->title . ' ' . $productModificationInOrder->ProductModifications->Modification->value;
                $price = $productModificationInOrder->ProductModifications->selling_price;
                $amount = $productModificationInOrder->product_modification_amount;
                $categoryTitle = $productModificationInOrder->ProductModifications->Product->Category->title;

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

        return view('arm.administration.products.index', compact('productsModifications'));
    }
}
