<?php


namespace App\Http\Controllers\Catalog;

use App\Helpers\ArrayHelper;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Products\ProductsController;
use App\Models\Categories;
use App\Models\Products;

class CatalogController extends Controller
{
    public function Index()
    {
        $allProducts = ProductsController::GetAllProducts();

        $combos = cache()->remember('combos', 3600, function () {
            return ProductsController::GetCombos();
        });

        $allCategory = cache()->remember('allCategory', 3600, function () {
            return Categories::all();
        });

        return view('catalog.index', [
            'allProducts' => $allProducts,
            'allCategory' => $allCategory,
            'combos' => $combos,
            'footer' => true,
        ]);
    }
}
