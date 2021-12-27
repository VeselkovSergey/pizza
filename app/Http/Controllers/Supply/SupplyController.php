<?php


namespace App\Http\Controllers\Supply;

use App\Helpers\ResultGenerate;
use App\Helpers\StringHelper;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Products\ProductsController;
use App\Models\Ingredients;
use App\Models\IngredientsInSupply;
use App\Models\Suppliers;
use App\Models\Supply;
use Illuminate\Http\Request;

class SupplyController extends Controller
{
    public function __construct()
    {

    }

    public function Create()
    {
        $suppliers = Suppliers::all();
        $ingredients = Ingredients::all();
        return view('arm.supply.createOrUpdate', [
            'suppliers' => $suppliers,
            'ingredients' => $ingredients,
        ]);
    }

    public function Save(Request $request)
    {
        $supplierId = $request->supplierId;
        $dateSupply = $request->dateSupply;
        $paymentType = $request->paymentType;
        $allIngredientsInSupplyData = StringHelper::JsonDecode($request->allIngredientsInSupplyData);

        $newSupply = Supply::create([
            'supplier_id' => $supplierId,
            'supply_date' => $dateSupply,
            'payment_type' => $paymentType,
        ]);

        $ingredientInSupply = [];
        foreach ($allIngredientsInSupplyData as $ingredientInSupplyData) {
            $ingredientInSupply[] = [
                'supply_id' => $newSupply->id,
                'ingredient_id' => $ingredientInSupplyData->id,
                'amount_ingredient' => $ingredientInSupplyData->amount,
                'price_ingredient' => $ingredientInSupplyData->price,
            ];
        }
        $newIngredientInSupply = IngredientsInSupply::insert($ingredientInSupply);
        \Cache::delete('allProducts');

        return ResultGenerate::Success();
    }
}
