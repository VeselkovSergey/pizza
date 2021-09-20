<?php


namespace App\Http\Controllers\Products;

use App\Helpers\ResultGenerate;
use App\Http\Controllers\Controller;
use App\Models\ProductProperties;
use App\Models\Products;
use App\Models\PropertiesForProducts;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    public function __construct()
    {

    }

    public function Create()
    {
        $propertiesForProducts = PropertiesForProducts::all();
        return view('products.createOrUpdate', [
            'propertiesForProducts' => $propertiesForProducts
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
