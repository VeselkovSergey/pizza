<?php


namespace App\Http\Controllers\Supply;

use App\Helpers\Files;
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
    public function Index()
    {
        $ingredientFilter = (int)\request()->ingredient ?? 0;
        $ingredients = Ingredients::all();
        if ($ingredientFilter) {
            $ingredientsInSupply = IngredientsInSupply::select('supply_id')->where('ingredient_id', $ingredientFilter)->groupBy('supply_id')->get();
            $suppliesId = [];
            if ($ingredientsInSupply) {
                foreach ($ingredientsInSupply as $supplyId) {
                    $suppliesId[] = $supplyId->supply_id;
                }
            }
            $supplies = Supply::whereIn('id', $suppliesId)->get();
        } else {
            $supplies = Supply::all();
        }
        return view('arm.supply.index', compact('supplies', 'ingredients'));
    }

    public function Detail()
    {
        $supplyId = (int)\request()->supplyId;
        $supply = Supply::find($supplyId);
        $supplyFiles = $supply->files;
        $supplyFiles = json_decode($supplyFiles);

        $files = [];
        if (isset($supplyFiles)) {
            foreach ($supplyFiles as $fileId) {
                $files[] = Files::GetFile($fileId);
            }
        }

        return view('arm.supply.detail', compact('supply', 'files'));
    }

    public function Create()
    {
        $suppliers = Suppliers::all();
        $ingredients = Ingredients::all();
        return view('arm.supply.createOrUpdate', compact('ingredients', 'suppliers'));
    }

    public function Edit()
    {
        $supplyId = \request()->supplyId;
        $supply = Supply::find($supplyId);
        $ingredientsInSupply = $supply->Ingredients;
        $suppliers = Suppliers::all();
        $ingredients = Ingredients::all();
        return view('arm.supply.createOrUpdate', compact('ingredients', 'suppliers', 'supply', 'ingredientsInSupply'));
    }

    public function Save(Request $request)
    {
        $supplyId = $request->supplyId ? (int)$request->supplyId : null;
        $supplierId = $request->supplierId;
        $dateSupply = $request->dateSupply;
        $paymentType = $request->paymentType;
        $file = $request->file !== 'undefined' ? $request->file : null;
        $allIngredientsInSupplyData = StringHelper::JsonDecode($request->allIngredientsInSupplyData);

        if ($supplyId) {
            $supply = Supply::find($supplyId);
        }

        if (isset($file) && isset($supply)) {
            if (isset($supply->files)) {
                $filesDB = json_decode($supply->files);
                foreach ($filesDB as $fileDB) {
                    Files::DeleteFiles($fileDB);
                }
            }
            $fileDB = Files::SaveFile($file, 'invoice');
        } else if (empty($supply)) {
            $fileDB = Files::SaveFile($file, 'invoice');
        }


        if (isset($supply)) {
            $supply->update([
                'supplier_id' => $supplierId,
                'supply_date' => $dateSupply,
                'payment_type' => $paymentType,
                'creator_id' => auth()->user()->id,
                'files' => isset($file) ? json_encode([$fileDB->id]) : $supply->files,
            ]);
            foreach ($supply->Ingredients as $ingredient) {
                $ingredient->delete();
            }
            $newSupply = $supply;
        } else {
            $newSupply = Supply::create([
                'supplier_id' => $supplierId,
                'supply_date' => $dateSupply,
                'payment_type' => $paymentType,
                'creator_id' => auth()->user()->id,
                'files' => json_encode([$fileDB->id]),
            ]);
        }

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
