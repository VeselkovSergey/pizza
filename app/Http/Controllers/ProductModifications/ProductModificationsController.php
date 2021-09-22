<?php


namespace App\Http\Controllers\ProductModifications;

use App\Helpers\ResultGenerate;
use App\Http\Controllers\Controller;
use App\Models\ProductModifications;
use App\Models\Products;
use Illuminate\Http\Request;

class ProductModificationsController extends Controller
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
