<?php


namespace App\Http\Controllers\Suppliers;

use App\Helpers\ResultGenerate;
use App\Http\Controllers\Controller;
use App\Models\Ingredients;
use App\Models\ProductModifications;
use App\Models\Products;
use App\Models\Modifications;
use App\Models\Suppliers;
use Illuminate\Http\Request;

class SuppliersController extends Controller
{
    public function Create()
    {
        $suppliers = Suppliers::all();
        return view('arm.suppliers.createOrUpdate', compact('suppliers'));
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
