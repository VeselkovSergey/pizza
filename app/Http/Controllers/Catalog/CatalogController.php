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
        $allProducts = \Cache::get('allProducts');
        if (empty($allProducts) || $forceUpdate) {
            $allProducts = new ProductsController();
            $allProducts = $allProducts->GetAllProducts($forceUpdate);
            \Cache::put('allProducts', $allProducts, now()->addMinutes(10));
        }
        $allCategory = Categories::all();
        return view('catalog.index', [
            'allProducts' => json_decode($allProducts),
            'allCategory' => $allCategory,
            'footer' => true,
        ]);
    }
}
