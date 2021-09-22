<?php


namespace App\Http\Controllers\Suppliers;

use App\Helpers\ResultGenerate;
use App\Http\Controllers\Controller;
use App\Models\Ingredients;
use App\Models\ProductProperties;
use App\Models\Products;
use App\Models\PropertiesForProducts;
use App\Models\Suppliers;
use Illuminate\Http\Request;

class SuppliersController extends Controller
{
    public function __construct()
    {

    }

    public function Create()
    {
        return view('suppliers.createOrUpdate');
    }

    public function Save(Request $request)
    {
        $title = $request->title;
        if (empty($title)) {
            return ResultGenerate::Error('Пустое название');
        }

        Suppliers::create([
            'title' => $title
        ]);
        return ResultGenerate::Success();
    }
}
