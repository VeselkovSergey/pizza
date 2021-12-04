<?php


namespace App\Http\Controllers\Catalog;

use App\Helpers\ArrayHelper;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Products\ProductsController;
use App\Models\Products;

class CatalogController extends Controller
{
    public function __construct()
    {
    }

    public function Index()
    {
        $allProducts = new ProductsController();
        $allProducts = $allProducts->GetAllProducts();
        return view('catalog.index', [
            'allProducts' => json_decode($allProducts),
            'footer' => true,
        ]);
    }
}
