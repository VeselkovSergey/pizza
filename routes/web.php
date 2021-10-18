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
    return redirect('all-routes');
//    return view('home.index')
//        /*->middleware('permissions:home-page,index')
//        ->name('home-page')*/
//        ;
})->name('home-page');

Route::view('all-routes', 'debag.all-routes');

Route::get('resources/{directory}/{fileName}', [Controllers\Resources\ResourceController::class, 'GetResources']);

Route::group(['prefix' => 'catalog'], function() {

    Route::get('/', [Controllers\Catalog\CatalogController::class, 'Index'])->name('catalog');

});

Route::group(['prefix' => 'products'], function() {

    Route::get('/all-admin', [Controllers\Products\ProductsController::class, 'IndexAdmin'])->name('all-products-admin-page');
    Route::get('/all-products', [Controllers\Products\ProductsController::class, 'GetAllProducts'])->name('all-products');
    Route::get('/create', [Controllers\Products\ProductsController::class, 'Create'])->name('product-create');
    Route::post('/save', [Controllers\Products\ProductsController::class, 'Save'])->name('product-save');

});

Route::group(['prefix' => 'modifications'], function() {

    Route::get('/create', [Controllers\Modifications\ModificationsController::class, 'Create'])->name('modification-create');
    Route::post('/save', [Controllers\Modifications\ModificationsController::class, 'Save'])->name('modification-save');
    Route::get('/all', [Controllers\Modifications\ModificationsController::class, 'GetAllModifications'])->name('all-modifications');

});

Route::group(['prefix' => 'types-modifications'], function() {

    Route::get('/create', [Controllers\TypesModifications\TypesModificationsController::class, 'Create'])->name('modification-type-create');
    Route::post('/save', [Controllers\TypesModifications\TypesModificationsController::class, 'Save'])->name('modification-type-save');

});

Route::group(['prefix' => 'ingredients'], function() {

    Route::get('/create', [Controllers\Ingredients\IngredientsController::class, 'Create'])->name('ingredients-create');
    Route::post('/save', [Controllers\Ingredients\IngredientsController::class, 'Save'])->name('ingredients-save');
    Route::get('/all', [Controllers\Ingredients\IngredientsController::class, 'GetAllIngredients'])->name('all-ingredients');

});

Route::group(['prefix' => 'supply'], function() {

    Route::get('/create', [Controllers\Supply\SupplyController::class, 'Create'])->name('supply-create');
    Route::post('/save', [Controllers\Supply\SupplyController::class, 'Save'])->name('supply-save');

});

Route::group(['prefix' => 'suppliers'], function() {

    Route::get('/create', [Controllers\Suppliers\SuppliersController::class, 'Create'])->name('supplier-create');
    Route::post('/save', [Controllers\Suppliers\SuppliersController::class, 'Save'])->name('supplier-save');

});

Route::group(['prefix' => 'order'], function() {

    Route::post('/create', [Controllers\Orders\OrdersController::class, 'Create'])->name('order-create');

});

Route::group(['prefix' => 'auth'], function() {

    Route::post('/phone-validation', [Controllers\Auth\AuthController::class, 'PhoneValidation'])->name('phone-validation');
    Route::post('/check-confirmation-code', [Controllers\Auth\AuthController::class, 'CheckConfirmationCode'])->name('check-confirmation-code');

    Route::get('/logout', [Controllers\Auth\AuthController::class, 'Logout'])->name('logout');

});


/*
 *  test routes
 */
Route::get('/test-ucaller', function () {
    return ;
    $ucaller = new App\Services\Ucaller\Ucaller();
    $balance = $ucaller->GetBalance();
    $info = $ucaller->GetInfo();
    $initCall = $ucaller->InitCall();
    dd($balance, $info, $initCall);
});
