<?php


namespace App\Http\Controllers\Products;

use App\Helpers\ResultGenerate;
use App\Http\Controllers\Controller;
use App\Models\Ingredients;
use App\Models\ProductModifications;
use App\Models\ProductModificationsIngredients;
use App\Models\Products;
use App\Models\Modifications;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    public function __construct()
    {

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

        return ResultGenerate::Success();
    }
}
