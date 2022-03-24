<?php

namespace App\Http\Controllers\WriteOff;

use App\Helpers\ResultGenerate;
use App\Helpers\StringHelper;
use App\Http\Controllers\Controller;
use App\Models\Ingredients;
use App\Models\IngredientsInWriteOff;
use App\Models\WriteOff;
use Illuminate\Support\Facades\Cache;

class WriteOffController extends Controller
{
    public function Index()
    {
        $ingredientFilter = (int)\request()->ingredient ?? 0;
        $ingredients = Ingredients::all();
        if ($ingredientFilter) {
            $ingredientsInWriteOff = IngredientsInWriteOff::select('write_off_id')->where('ingredient_id', $ingredientFilter)->groupBy('write_off_id')->get();
            $writeOffsId = [];
            if ($ingredientsInWriteOff) {
                foreach ($ingredientsInWriteOff as $ingredientInWriteOff) {
                    $writeOffsId[] = $ingredientInWriteOff->write_off_id;
                }
            }
            $writeOffs = WriteOff::whereIn('id', $writeOffsId)->orderByDesc('id')->get();
        } else {
            $writeOffs = WriteOff::orderByDesc('id')->get();
        }
        return view('arm.write-off.index', compact('writeOffs', 'ingredients'));
    }

    public function Detail($writeOffId)
    {
        $writeOff = WriteOff::findOrFail($writeOffId);
        return view('arm.write-off.detail', compact('writeOff'));
    }

    public function Create()
    {
        $ingredients = Ingredients::all();
        return view('arm.write-off.createOrUpdate', compact('ingredients'));
    }

    public function Edit($writeOffId)
    {
        $writeOff = WriteOff::findOrFail($writeOffId);
        $ingredientsInWriteOff = $writeOff->Ingredients;
        $ingredients = Ingredients::all();
        return view('arm.write-off.createOrUpdate', compact('ingredients', 'writeOff', 'ingredientsInWriteOff'));
    }

    public function Save()
    {
        $writeOffId = request()->writeOffId ? (int)request()->writeOffId : null;
        $date = request()->date;
        $description = request()->description;
        $allIngredientsData = StringHelper::JsonDecode(request()->allIngredientsData);

        if ($writeOffId) {
            $writeOff = WriteOff::find($writeOffId);
        }


        if (isset($writeOff)) {
            $writeOff->update([
                'supplier_id' => $writeOffId,
                'date' => $date,
                'description' => $description,
                'creator_id' => auth()->user()->id,
            ]);
            IngredientsInWriteOff::where('write_off_id', $writeOff->id)->delete();
        } else {
            $writeOff = WriteOff::create([
                'date' => $date,
                'description' => $description,
                'creator_id' => auth()->user()->id,
            ]);
        }

        $ingredientInWriteOff = [];
        foreach ($allIngredientsData as $ingredientData) {
            $ingredientInWriteOff[] = [
                'write_off_id' => $writeOff->id,
                'ingredient_id' => $ingredientData->id,
                'amount_ingredient' => number_format($ingredientData->amount, 2, ',', ''),
            ];
        }
        IngredientsInWriteOff::insert($ingredientInWriteOff);
        Cache::forget('allProducts');

        return ResultGenerate::Success();
    }
}