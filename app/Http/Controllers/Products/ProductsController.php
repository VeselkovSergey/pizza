<?php


namespace App\Http\Controllers\Products;

use App\Helpers\ArrayHelper;
use App\Helpers\Files;
use App\Models\Categories;
use App\Models\Files as FilesDB;
use App\Helpers\ResultGenerate;
use App\Http\Controllers\Controller;
use App\Models\Ingredients;
use App\Models\ProductModifications;
use App\Models\ProductModificationsIngredients;
use App\Models\Products;
use App\Models\Modifications;
use App\Models\ProductsModificationsInOrders;
use App\Services\Orders\OrdersService;
use Illuminate\Http\Request;
use function GuzzleHttp\Promise\all;

class ProductsController extends Controller
{
    private Products $productModel;
    private \stdClass $product;

    public static function GetAllProducts()
    {
        if (request()->get('force-update')) {
            cache()->delete('allProducts');
        }
        return cache()->remember('allProducts', 3600, function () {

            $pro = new self();
            $prod = $pro->AllProducts();
            return $prod;

        });
    }

    public static function GetCombos()
    {

        $modifications25 = [];
        $modifications33 = [];
        $modifications40 = [];

        $modificationsSoups = [];

        $modificationsPastas = [];

        $modificationsSalads = [];

        $modificationsDrinks = [];

        $pizzas = Products::whereNotIn('id', [31, 32, 65])->where('category_id', 1)->get();
        foreach ($pizzas as $pizza) {
            $modification25 = $pizza->Modifications()->where('modification_id', 2)->where('stop_list', 0)->first(); // 25
            $modification33 = $pizza->Modifications()->where('modification_id', 1)->where('stop_list', 0)->first(); // 33
            $modification40 = $pizza->Modifications()->where('modification_id', 3)->where('stop_list', 0)->first(); // 40

            if (isset($modification25)) {
                $modifications25[] = (object)[
                    'productId' => $pizza->id,
                    'modificationId' => $modification25->id,
                ];
            }

            if (isset($modification33)) {
                $modifications33[] = (object)[
                    'productId' => $pizza->id,
                    'modificationId' => $modification33->id,
                ];
            }

            if (isset($modification40)) {
                $modifications40[] = (object)[
                    'productId' => $pizza->id,
                    'modificationId' => $modification40->id,
                ];
            }
        }

        $soups = Products::whereIn('id', [66])->get();
        foreach ($soups as $soup) {
            $modification = $soup->Modifications()->where('stop_list', 0)->first();
            if (isset($modification)) {
                $modificationsSoups[] = (object)[
                    'productId' => $soup->id,
                    'modificationId' => $modification->id,
                ];
            }
        }

        $pastas = Products::whereIn('id', [33, 34, 38])->get();
        foreach ($pastas as $pasta) {
            $modification = $pasta->Modifications()->where('stop_list', 0)->first();
            if (isset($modification)) {
                $modificationsPastas[] = (object)[
                    'productId' => $pasta->id,
                    'modificationId' => $modification->id,
                ];
            }
        }

        $salads = Products::whereNotIn('id', [43])->where('category_id', 3)->get();
        foreach ($salads as $salad) {
            $modification = $salad->Modifications()->where('stop_list', 0)->first();
            if (isset($modification)) {
                $modificationsSalads[] = (object)[
                    'productId' => $salad->id,
                    'modificationId' => $modification->id,
                ];
            }
        }

        $drinks = Products::where('category_id', 5)->get();
        foreach ($drinks as $drink) {
            $modification = $drink->Modifications()->where('modification_id', 8)->where('stop_list', 0)->first();
            if (isset($modification)) {
                $modificationsDrinks[] = (object)[
                    'productId' => $drink->id,
                    'modificationId' => $modification->id,
                ];
            }
        }

        return [
            (object)[
                'id' => 1,
                'title' => 'Комбо - 25',
                'price' => '555',
                'sections' => [
                    $modifications25,
                    $modifications25,
                ],
            ],
            (object)[
                'id' => 2,
                'title' => 'Комбо - 33',
                'price' => '685',
                'sections' => [
                    $modifications33,
                    $modifications33,
                ],
            ],
            (object)[
                'id' => 3,
                'title' => 'Комбо - 40',
                'price' => '849',
                'sections' => [
                    $modifications40,
                    $modifications40,
                ],
            ],
            (object)[
                'id' => 4,
                'title' => 'Комбо на двоих',
                'price' => '849',
                'sections' => [
                    $modifications33,
                    array_merge($modificationsSoups, $modificationsPastas, $modificationsSalads),
                    array_merge($modificationsSoups, $modificationsPastas, $modificationsSalads),
                    $modificationsDrinks,
                ],
            ],
            (object)[
                'id' => 5,
                'title' => 'Комбо на одного',
                'price' => '599',
                'sections' => [
                    $modifications25,
                    array_merge($modificationsSoups, $modificationsPastas, $modificationsSalads),
                    $modificationsDrinks,
                ],
            ],
            (object)[
                'id' => 6,
                'title' => 'Комбо ТРИ',
                'price' => '890',
                'sections' => [
                    $modifications25,
                    $modifications25,
                    array_merge($modificationsPastas, $modificationsSalads, $modificationsSoups, $modifications25, $modificationsDrinks),
                ],
            ],
        ];
    }

    public function AllProducts()
    {
        $categories = Categories::all();
        $popularPositionsByCategory = [];
        foreach ($categories as $category) {
            $popularPositions = ProductsModificationsInOrders::selectRaw('product_modifications.product_id as product_id, products.title as product_title, sum(products_modifications_in_orders.product_modification_amount) as amount')
                ->where('products_modifications_in_orders.created_at', '>=', now()->addDays(-10)->format('Y-m-d ') . '00:00:00')
                ->where('products.category_id', $category->id)
                ->leftJoin('product_modifications', 'products_modifications_in_orders.product_modification_id', '=', 'product_modifications.id')
                ->leftJoin('products', 'products.id', '=', 'product_modifications.product_id')
                ->groupBy('product_id')
                ->orderBy('amount', 'DESC')
                ->limit(2)
                ->get();

            $pp = [];
            foreach ($popularPositions as $popularPosition) {
                $pp[] = $popularPosition->product_id;
            }

            $popularPositionsByCategory[$category->id] = $pp;
        }

        $productsModels = Products::select('products.*')->leftJoin('categories', 'categories.id', '=', 'products.category_id')->orderBy('categories.sort')->orderBy('categories.id')->orderBy('products.sort')->get();
        $products = (object)[];
        foreach ($productsModels as $productModel) {

            $this->productModel = $productModel;
            $this->product = new \stdClass();

            $id = $productModel->id;
            $this->product->id = $id;
            $this->product->title = $productModel->title;
            $this->product->description = $productModel->description;
            $this->product->minimumPrice = $productModel->MinimumPrice();
            $this->product->showInCatalog = $productModel->show_in_catalog;
            $this->product->isAdditionalSales = $productModel->is_additional_sales;
            $this->product->additionalSalesSort = $productModel->additional_sales_sort;
            $this->product->isNew = $productModel->is_new;
            $this->product->isSpicy = $productModel->is_spicy;
            $this->product->isPopular = in_array($productModel->id, $popularPositionsByCategory[$productModel->category_id]);

            $this->product->categoryId = $productModel->category_id;
            $this->product->categoryTitle = $productModel->Category->title;

            $this->product->modifications = $this->Modifications();
            $this->product->modificationCount = sizeof($this->product->modifications);

            $this->product->imgUrl = asset('img/png/' . $id . '.png');
            $this->product->imgWebpUrl = asset('img/webp/' . $id . '.webp');

            $products->$id = $this->product;
        }

        return $products;
    }

    public function Modifications()
    {
        $productModifications = [];
        $productModificationsModels = $this->productModel->Modifications;
        /** @var ProductModifications $productModificationModel*/
        foreach ($productModificationsModels as $productModificationModel) {

            $modification = $productModificationModel->Modification;
            $id = $productModificationModel->id;

            $ingredients = OrdersService::ModificationIngredients($productModificationModel, now());

            $modificationTitle = $modification->title === 'Соло-продукт' ? '' : ' ' . $modification->title . ' ' . $modification->value;
            $title = $this->product->title . $modificationTitle;

            $productModifications[] = (object)[
                'id' => $id,
                'title' => $title,
                'modificationTitle' => $modification->title,
                'modificationValue' => $modification->value,
                'modificationTypeId' => $modification->type_id,
                'modificationTypeDiscountPrice' => !in_array($this->productModel->id, [24, 31, 32, 65]) ? self::DiscountSale($modification->id) : false,
                'price' => $productModificationModel->selling_price,
                'stopList' => (bool)$productModificationModel->stop_list,
                'ingredients' => $ingredients->ingredients,
                'cost' => $ingredients->productModificationCost,
                'weight' => $this->productModel->category_id !== 5 ? (integer)($ingredients->productModificationWeight * 1000) : 0,
            ];
        }
        return $productModifications;
    }

    public function Ingredients(ProductModifications $modification)
    {
        $modificationIngredients = [];
        $modificationIngredientsModels = $modification->Ingredients;
        /** @var  ProductModificationsIngredients $modificationIngredientsModel */
        foreach ($modificationIngredientsModels as $modificationIngredientsModel) {
            $ingredient = $modificationIngredientsModel->Ingredient;

            $modificationIngredients[] = (object)[
                'id' => $ingredient->id,
                'title' => $ingredient->title,
                'amount' => $modificationIngredientsModel->ingredient_amount,
                'visible' => $modificationIngredientsModel->visible,
            ];

        }

        dd($modificationIngredients);
    }

    private static function DiscountSale($type)
    {
        return match ($type) {
            2 => 535,
            1 => 645,
            3 => 799,
            default => false,
        };
    }

    public function IndexAdmin()
    {
        $allProducts = Products::all();
        return view('arm.products.indexAdmin', [
            'allProducts' => $allProducts,
        ]);
    }

    public function CreatePage()
    {
        $modifications = Modifications::all();
        $ingredients = Ingredients::all();
        $categories = Categories::all();
        return view('arm.products.createOrUpdate', compact('modifications', 'ingredients', 'categories'));
    }

    public function Create(Request $request)
    {
        $title = $request->title;
        $category = (int)$request->category;
        $showInCatalog = $request->show_in_catalog === 'true' ? 1 : 0;
        $modifications = $request->modifications;
        if (empty($title)) {
            return ResultGenerate::Error('Пустое название');
        }
        if (empty($modifications)) {
            return ResultGenerate::Error('Нет модификаторов');
        }

        // проверям модификаторы на корректность
        foreach ($modifications as $modification) {

            $modification = (object)$modification;
            $modificationId = $modification->id;
            $modificationPrice = $modification->price;

            if (empty($modificationId)) {
                return ResultGenerate::Error('Не выбран модификатор');
            }
            if (empty($modificationPrice)) {
                return ResultGenerate::Error('Не заполнена цена');
            }
            if (empty($modification->ingredients)) {
                return ResultGenerate::Error('Нет ингредиентов');
            }

            $ingredients = (object)$modification->ingredients;
            foreach ($ingredients->id as $key => $ingredientId) {
                $ingredientAmount = $ingredients->amount[$key];
                if (empty($ingredientId)) {
                    return ResultGenerate::Error('Не выбран ингредиент');
                }
                if (empty($ingredientAmount)) {
                    return ResultGenerate::Error('Не указано колличество ингредиентов');
                }
            }
        }

        // создаем продукт и его модификаторы
        $createdProduct = Products::create([
            'title' => $title,
            'category_id' => $category,
            'show_in_catalog' => $showInCatalog,
        ]);

        foreach ($modifications as $modification) {

            $modification = (object)$modification;
            $modificationId = $modification->id;
            $modificationPrice = $modification->price;
            // создаем модификатор продукта

            $productModification = ProductModifications::create([
                'product_id' => $createdProduct->id,
                'modification_id' => $modificationId,
                'selling_price' => $modificationPrice,
            ]);

            $ingredients = (object)$modification->ingredients;
            foreach ($ingredients->id as $key => $ingredientId) {
                $ingredientAmount = $ingredients->amount[$key];
                $ingredientVisible = $ingredients->visible[$key];
                ProductModificationsIngredients::create([
                    'product_modification_id' => $productModification->id,
                    'ingredient_id' => $ingredientId,
                    'ingredient_amount' => $ingredientAmount,
                    'visible' => $ingredientVisible === 'true' ? 1 : 0,
                ]);
                // создаем связь ингредиентов с модификатором
            }
        }

        return ResultGenerate::Success();
    }

    public static function SaveChanges(Products $product, array|object $data)
    {
        foreach ($data as $title => $value) {
            $product->$title = $value;
        }
        $product->save();
        return $product;
    }
}
