<?php


namespace App\Http\Controllers\Products;

use App\Helpers\ResultGenerate;
use App\Http\Controllers\Controller;
use App\Models\Ingredients;
use App\Models\ProductModifications;
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
        if (empty($title)) {
            return ResultGenerate::Error('Пустое название');
        }

        Products::create([
            'title' => $title
        ]);
        return ResultGenerate::Success();
    }
}
