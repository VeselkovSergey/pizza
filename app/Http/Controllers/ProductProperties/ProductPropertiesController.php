<?php


namespace App\Http\Controllers\ProductProperties;

use App\Helpers\ResultGenerate;
use App\Http\Controllers\Controller;
use App\Models\ProductProperties;
use App\Models\Products;
use Illuminate\Http\Request;

class ProductPropertiesController extends Controller
{
    public function __construct()
    {

    }

    public function Create()
    {
        return view('products.createOrUpdate');
    }

    public function Save(Request $request)
    {
        return ResultGenerate::Error();
    }
}
