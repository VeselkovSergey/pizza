<?php


namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Models\Products;

class CatalogController extends Controller
{
    public function __construct()
    {
    }

    public function Index()
    {
        $allProducts = Products::all();
        return view('catalog.index', [
            'allProducts' => $allProducts,
        ]);
    }
}
