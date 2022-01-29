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
use Illuminate\Http\Request;
use function GuzzleHttp\Promise\all;

class ProductsController extends Controller
{
    public static function GetAllProducts()
    {
        $forceUpdate = request()->get('force-update') ?? false;
        $allProducts = \Cache::get('allProducts');
        if (empty($allProducts) || $forceUpdate) {
            $allProducts = self::UpdateFileAllProducts();
            \Cache::put('allProducts', $allProducts, now()->addMinutes(3600));
        }
        return $allProducts;
    }

    public static function UpdateFileAllProducts()
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

        $allProductsDB = Products::orderBy('category_id')->orderBy('sort')->get();
        $allProducts = [];
        foreach ($allProductsDB as $product) {

            $allProducts['product-' . $product->id] = [
                'id' => $product->id,
                'title' => $product->title,
                'description' => $product->description,
                'is_additional_sales' => $product->is_additional_sales,
                'additional_sales_sort' => $product->additional_sales_sort,
                'is_popular' => in_array($product->id, $popularPositionsByCategory[$product->category_id]),
                'is_new' => $product->is_new,
                'categoryId' => $product->category_id,
                'categoryTitle' => $product->Category->title,
                'minimumPrice' => $product->MinimumPrice(),
                'modifications' => [],
            ];

            $arrModifications = [];

            $modificationCount = 0;

            foreach ($product->Modifications as $modification) {
                /** @var ProductModifications $modification */

                if (!in_array($modification->Modification->type_id, $arrModifications)) {
                    $arrModifications[] = $modification->Modification->type_id;  // добавляем тип модификации который еще не добавляли

                    $allProducts['product-' . $product->id]['modifications']['modification-type-' . $modification->Modification->type_id] = [];

                }

                $modificationCount++;

                $allProducts['product-' . $product->id]['modifications']['modification-type-' . $modification->Modification->type_id]['modification-' . $modification->id] = [
                    'id' => $modification->id,
                    'title' => $modification->Modification->title,
                    'value' => $modification->Modification->value,
                    'stop_list' => $modification->stop_list,
                    'modificationTypeId' => $modification->Modification->id,
                    'modificationTypeDiscountPrice' => !in_array($product->id, [24, 31, 32]) ? self::DiscountSale($modification->Modification->id) : false,
                    'sellingPrice' => $modification->selling_price,
                    'modificationTypeCount' => sizeof($product->Modifications),
                    'ingredients' => [],
                ];

                foreach ($modification->Ingredients as $ingredient) {
                    $allProducts['product-' . $product->id]['modifications']['modification-type-' . $modification->Modification->type_id]['modification-' . $modification->id]['ingredients']['ingredient-' . $ingredient->Ingredient->id] = [
                        'id' => $ingredient->Ingredient->id,
                        'title' => $ingredient->Ingredient->title,
                        'visible' => $ingredient->visible,
                    ];
                }
            }

            $allProducts['product-' . $product->id]['modificationCount'] = $modificationCount;
        }

        return ArrayHelper::ArrayToObject($allProducts);
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

        \Cache::delete('allProducts');

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
