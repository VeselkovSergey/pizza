<?php


namespace App\Http\Controllers\Ingredients;

use App\Helpers\ResultGenerate;
use App\Http\Controllers\Controller;
use App\Models\Ingredients;
use App\Models\IngredientsInSupply;
use App\Models\ProductModifications;
use App\Models\Products;
use App\Models\Modifications;
use Illuminate\Http\Request;

class IngredientsController extends Controller
{
    public function __construct()
    {

    }

    public function Create()
    {
        return view('ingredients.createOrUpdate');
    }

    public function Save(Request $request)
    {
        $title = $request->title;
        if (empty($title)) {
            return ResultGenerate::Error('Пустое название');
        }

        Ingredients::create([
            'title' => $title
        ]);
        return ResultGenerate::Success();
    }

    public function GetAllIngredients()
    {

        $allIngredients = Ingredients::query()->select('ingredients.*', 'ingredients_in_supply.price_ingredient as last_price_ingredient')
            ->leftJoin('ingredients_in_supply', function($query) {
                $query->on('ingredients.id','=','ingredients_in_supply.ingredient_id')
                    ->whereRaw('ingredients_in_supply.id IN (select MAX(iis2.id) from ingredients_in_supply as iis2 join ingredients as i2 on i2.id = iis2.ingredient_id group by i2.id)');
            })
            ->get();



        return ResultGenerate::Success('', $allIngredients);
    }
}
