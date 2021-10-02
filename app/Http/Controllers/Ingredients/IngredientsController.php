<?php


namespace App\Http\Controllers\Ingredients;

use App\Helpers\ResultGenerate;
use App\Http\Controllers\Controller;
use App\Models\Ingredients;
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
        return ResultGenerate::Success('', Ingredients::all());
    }
}
