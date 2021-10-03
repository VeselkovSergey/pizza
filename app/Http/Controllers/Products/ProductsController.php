<?php


namespace App\Http\Controllers\Products;

use App\Helpers\ArrayHelper;
use App\Helpers\Files;
use App\Models\Files as FilesDB;
use App\Helpers\ResultGenerate;
use App\Http\Controllers\Controller;
use App\Models\Ingredients;
use App\Models\ProductModifications;
use App\Models\ProductModificationsIngredients;
use App\Models\Products;
use App\Models\Modifications;
use Illuminate\Http\Request;
use function GuzzleHttp\Promise\all;

class ProductsController extends Controller
{

    public function GetAllProducts()
    {
        $allProductsJSON = Files::GetFile('allProduct');
        if ($allProductsJSON !== false) {
            return $allProductsJSON->contentFile;
        } else {
            return self::UpdateFileAllProducts()->contentFile;
        }

    }

    public static function UpdateFileAllProducts()
    {
        $allProductsDB = Products::all();
        $allProducts = [];
        foreach ($allProductsDB as $product) {

            $allProducts['product-' . $product->id] = [
                'id' => $product->id,
                'title' => $product->title,
                'minimumPrice' => $product->MinimumPrice(),
                'modifications' => [],
            ];

            $arrModifications = [];

            foreach ($product->Modifications as $modification) {

                if (!in_array($modification->Modification->type_id, $arrModifications)) {
                    $arrModifications[] = $modification->Modification->type_id;  // добавляем тип модификации который еще не добавляли

                    $allProducts['product-' . $product->id]['modifications']['modification-type-' . $modification->Modification->type_id] = [];

                }

                $allProducts['product-' . $product->id]['modifications']['modification-type-' . $modification->Modification->type_id]['modification-' . $modification->id] = [
                    'id' => $modification->id,
                    'title' => $modification->Modification->title,
                    'value' => $modification->Modification->value,
                    'sellingPrice' => $modification->selling_price,
                    'modificationTypeCount' => sizeof($product->Modifications),
                    'ingredients' => [],
                ];

                foreach ($modification->Ingredients as $ingredient) {

                    $allProducts['product-' . $product->id]['modifications']['modification-type-' . $modification->Modification->type_id]['modification-' . $modification->id]['ingredients']['ingredient-' . $ingredient->Ingredient->id] = [
                        'id' => $ingredient->Ingredient->id,
                        'title' => $ingredient->Ingredient->title,
                    ];
                }
            }
        }

        $oldFileAllProducts = Files::GetFile('allProduct');

        $fileAllProducts = Files::MakeFile(ArrayHelper::ArrayToObject($allProducts), 'allProduct', 'json');

        if (!empty($oldFileAllProducts) && $fileAllProducts) {
            Files::DeleteFiles($oldFileAllProducts->modelFile->id);
        }

        return $fileAllProducts;
    }

    public function IndexAdmin()
    {
        $allProducts = Products::all();
        return view('products.indexAdmin', [
            'allProducts' => $allProducts,
        ]);
    }

    public function Create()
    {
        $modifications = Modifications::all();
        $ingredients = Ingredients::all();
        return view('products.createOrUpdate', [
            'modifications' => $modifications,
            'ingredients' => $ingredients,
        ]);
    }

    public function Save(Request $request)
    {
        $title = $request->title;
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
            'title' => $title
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
                ProductModificationsIngredients::create([
                    'product_modification_id' => $productModification->id,
                    'ingredient_id' => $ingredientId,
                    'ingredient_amount' => $ingredientAmount,
                ]);
                // создаем связь ингредиентов с модификатором
            }
        }

        ProductsController::UpdateFileAllProducts();

        return ResultGenerate::Success();
    }
}
