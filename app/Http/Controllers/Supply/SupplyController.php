<?php


namespace App\Http\Controllers\Supply;

use App\Helpers\Files;
use App\Helpers\ResultGenerate;
use App\Helpers\StringHelper;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Products\ProductsController;
use App\Models\Ingredients;
use App\Models\IngredientsInSupply;
use App\Models\ProductModificationsIngredients;
use App\Models\ProductsModificationsInOrders;
use App\Models\Suppliers;
use App\Models\Supply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

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
            $supplies = Supply::whereIn('id', $suppliesId)->orderByDesc('supply_date')->get();
        } else {
            $supplies = Supply::orderByDesc('supply_date')->get();
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

        $ingredientsIds = [];
        $ingredientInSupply = [];
        foreach ($allIngredientsInSupplyData as $ingredientInSupplyData) {
            $ingredientsIds[] = $ingredientInSupplyData->id;
            $ingredientInSupply[] = [
                'supply_id' => $newSupply->id,
                'ingredient_id' => $ingredientInSupplyData->id,
                'amount_ingredient' => $ingredientInSupplyData->amount,
                'price_ingredient' => $ingredientInSupplyData->price,
            ];
        }

        $productsModifications = ProductModificationsIngredients::select('product_modification_id as id')->whereIn('ingredient_id', $ingredientsIds)->get()->toArray();
        $productsModificationsIds = [];
        foreach ($productsModifications as $productsModification) {
            $productsModificationsIds[] = $productsModification['id'];
        }
        $orders = ProductsModificationsInOrders::select('orders.id')
            ->whereIn('product_modification_id', $productsModificationsIds)
            ->where('orders.created_at', '>=', $dateSupply)
            ->leftJoin('orders', 'orders.id', '=', 'products_modifications_in_orders.order_id')
            ->groupBy('orders.id')
            ->get()->toArray();

        foreach ($orders as $order) {
            Cache::forget('order-' . $order['id']);
        }

        $newIngredientInSupply = IngredientsInSupply::insert($ingredientInSupply);
        Cache::forget('allProducts');

        return ResultGenerate::Success();
    }

    public static function SaveChanges(IngredientsInSupply $ingredientInSupply, array|object $data)
    {
        foreach ($data as $title => $value) {
            $ingredientInSupply->$title = $value;
        }
        $ingredientInSupply->save();
        return $ingredientInSupply;
    }
}
