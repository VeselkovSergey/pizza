<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('home.index')
        /*->middleware('permissions:home-page,index')
        ->name('home-page')*/
        ;
});

Route::get('resources/{directory}/{fileName}', [Controllers\Resources\ResourceController::class, 'GetResources']);

Route::group(['prefix' => 'products'], function() {

    Route::get('/create', [Controllers\Products\ProductsController::class, 'Create'])->name('product-create');
    Route::post('/save', [Controllers\Products\ProductsController::class, 'Save'])->name('product-save');

});

Route::group(['prefix' => 'properties'], function() {

    Route::get('/create', [Controllers\PropertiesForProducts\PropertiesForProductsController::class, 'Create'])->name('property-create');
    Route::post('/save', [Controllers\PropertiesForProducts\PropertiesForProductsController::class, 'Save'])->name('property-save');

});

Route::group(['prefix' => 'type-for-properties'], function() {

    Route::get('/create', [Controllers\TypeForProperties\TypeForPropertiesController::class, 'Create'])->name('type-for-properties-create');
    Route::post('/save', [Controllers\TypeForProperties\TypeForPropertiesController::class, 'Save'])->name('type-for-properties-save');

});
