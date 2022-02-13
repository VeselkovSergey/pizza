<?php


namespace App\Http\Controllers\Ingredients;

use App\Helpers\ResultGenerate;
use App\Http\Controllers\Controller;
use App\Models\Ingredients;
use App\Models\IngredientsInSupply;
use App\Models\ProductModifications;
use App\Models\ProductModificationsIngredients;
use App\Models\Products;
use App\Models\Modifications;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class IngredientsController extends Controller
{
    public function Create()
    {
        return view('arm.ingredients.createOrUpdate');
    }

    public function Save(Request $request)
    {
        $title = $request->title;
        $description = $request->description;
        if (empty($title)) {
            return ResultGenerate::Error('Пустое название');
        }

        Ingredients::create([
            'title' => $title,
            'description' => $description,
        ]);
        return ResultGenerate::Success();
    }

    public static function AllIngredients()
    {
        return Ingredients::select('ingredients.*', 'ingredients_in_supply.price_ingredient as last_price_ingredient', 'ingredients_in_supply.amount_ingredient as last_amount_ingredient')
            ->leftJoin('ingredients_in_supply', function ($query) {
                $query->on('ingredients.id', '=', 'ingredients_in_supply.ingredient_id')
                    ->whereRaw('ingredients_in_supply.id IN (select MAX(iis2.id) from ingredients_in_supply as iis2 join ingredients as i2 on i2.id = iis2.ingredient_id group by i2.id)');
            })
            ->orderBy('title')
            ->get();
    }

    public function AllIngredientsToCSV()
    {
        $ingredientsModels = Ingredients::select('id', 'title')->get();
        $output = '';
        $output .= chr(239) . chr(187) . chr(191);
        $output .= 'ID; Название; Комментарий' . PHP_EOL;

        foreach ($ingredientsModels as $row) {
            $output.=  $row->id . ';' . $row->title . PHP_EOL;
        }

        return \response($output)
            ->header('Content-Type', 'text/csv; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="Ингредиенты.csv');
    }

    public static function SaveChanges(Ingredients $ingredient, array|object $data)
    {
        foreach ($data as $title => $value) {
            $ingredient->$title = $value;
        }
        $ingredient->save();
        return $ingredient;
    }

    public static function ProductsUsedIngredient()
    {
        $ingredientId = (int)\request()->ingredientId;
        $relationsProductsModifications = ProductModificationsIngredients::where('ingredient_id', $ingredientId)->get();
        $products = [];
        foreach ($relationsProductsModifications as $relationProductModifications) {
            $productModification = $relationProductModifications->ProductModification;
            $modification = $productModification->Modification;
            $productTitle = $productModification->Product->title . ' ' . $modification->title . ' ' . $modification->value;
            $ingredientUsageAmount = $relationProductModifications->ingredient_amount;
            $products[] = [
                'productTitle' => $productTitle,
                'ingredientUsageAmount' => $ingredientUsageAmount
            ];
        }

        return $products;
    }

    public static function IngredientSupply()
    {
        $ingredientId = (int)\request()->ingredientId;
        $supplyRaw = IngredientsInSupply::selectRaw('ingredients_in_supply.amount_ingredient as amount')
            ->selectRaw('ingredients_in_supply.price_ingredient as price')
            ->selectRaw('ingredients_in_supply.supply_id as supply_id')
            ->selectRaw('ingredients_in_supply.id as id')
            ->selectRaw('ingredients.title as title')
            ->selectRaw('supply.supply_date as date')
            ->leftJoin('supply', 'supply.id', '=', 'ingredients_in_supply.supply_id')
            ->leftJoin('ingredients', 'ingredients.id', '=', 'ingredients_in_supply.ingredient_id')
            ->orderByDesc('date')
            ->where('ingredient_id', $ingredientId)->get();

        $supply = [];
        foreach ($supplyRaw as $s) {
            $supply[] = [
                'amount' => $s->amount,
                'price' => $s->price,
                'date' => $s->date,
                'supplyId' => $s->supply_id,
                'id' => $s->id,
                'title' => $s->title,
            ];
        }
        return $supply;
    }
}
