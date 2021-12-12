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

    public function Edit()
    {
        $productsModifications = ProductModifications::all();
        return view('products.modifications.index', compact('productsModifications'));
    }

    public function Save(Request $request)
    {
        $modifications = $request->stopList;
        foreach ($modifications as $id => $stop) {
            ProductModifications::find($id)->update([
                'stop_list' => $stop === 'true' ? 1 : 0,
            ]);
        }
        return ResultGenerate::Success();
    }
}
