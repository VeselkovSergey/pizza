<?php


namespace App\Http\Controllers\Catalog;

use App\Helpers\ArrayHelper;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Products\ProductsController;
use App\Models\Categories;
use App\Models\Products;

class CatalogController extends Controller
{
    public function __construct()
    {
    }

    public function Index()
    {
        $forceUpdate = request()->get('force-update') ?? false;
        $allProducts = new ProductsController();
        $allProducts = $allProducts->GetAllProducts($forceUpdate);
        $allCategory = Categories::all();
        return view('catalog.index', [
            'allProducts' => json_decode($allProducts),
            'allCategory' => $allCategory,
            'footer' => true,
        ]);
    }
}
