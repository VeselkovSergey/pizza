<?php

use App\Services\Telegram\Telegram;
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

Route::group(['prefix' => 'arm'], function() {


    Route::group(['prefix' => 'administration'], function() {

        Route::get('/', [Controllers\ARM\AdministratorARMController::class, 'Index'])->name('administrator-arm-page');

    });

    Route::group(['prefix' => 'management'], function() {

        Route::get('/', [Controllers\ARM\ManagerARMController::class, 'Index'])->name('manager-arm-page');

        Route::get('/orders', [Controllers\ARM\ManagerARMController::class, 'Orders'])->name('manager-arm-orders-page');

        Route::get('/order/{orderId?}', [Controllers\ARM\ManagerARMController::class, 'Order'])->name('manager-arm-order-page');
        Route::post('/order/search-by-phone', [Controllers\ARM\ManagerARMController::class, 'SearchByPhone'])->name('manager-arm-order-search-bu-phone');

        Route::post('/change-status-order-to-new-order', [Controllers\ARM\ManagerARMController::class, 'ChangeStatusOrderToNewOrder'])->name('manager-arm-change-status-order-to-new-order-page');
        Route::post('/change-status-order-to-manager-processes', [Controllers\ARM\ManagerARMController::class, 'ChangeStatusOrderToManagerProcesses'])->name('manager-arm-change-status-order-to-manager-processes-page');
        Route::post('/transfer-order-to-kitchen', [Controllers\ARM\ManagerARMController::class, 'TransferOrderToKitchen'])->name('manager-arm-transfer-order-to-kitchen-page');
        Route::post('/transfer-order-to-delivery', [Controllers\ARM\ManagerARMController::class, 'TransferOrderToDelivery'])->name('manager-arm-transfer-order-to-delivery-page');
        Route::post('/change-status-order-to-completed', [Controllers\ARM\ManagerARMController::class, 'ChangeStatusOrderToCompleted'])->name('manager-arm-change-status-order-to-completed-page');
        Route::post('/change-status-order-to-canceled', [Controllers\ARM\ManagerARMController::class, 'ChangeStatusOrderToCanceled'])->name('manager-arm-change-status-order-to-canceled-page');

        #todo на курьера
        Route::post('/change-status-order-to-delivered', [Controllers\ARM\ManagerARMController::class, 'ChangeStatusOrderToDelivered'])->name('manager-arm-change-status-order-to-delivered');

    });

    Route::group(['prefix' => 'chef'], function() {

        Route::get('/', [Controllers\ARM\ChefARMController::class, 'Index'])->name('chef-arm-page');

        Route::get('/orders', [Controllers\ARM\ChefARMController::class, 'Orders'])->name('chef-arm-orders-page');

        Route::get('/order/{orderId?}', [Controllers\ARM\ChefARMController::class, 'Order'])->name('chef-arm-order-page');

        Route::post('/change-status-order-to-cooked', [Controllers\ARM\ChefARMController::class, 'ChangeStatusOrderToCooked'])->name('chef-arm-change-status-order-to-cooked');

        Route::post('/change-status-product-to-new', [Controllers\ARM\ChefARMController::class, 'ChangeStatusProductToNew'])->name('chef-arm-change-status-product-to-new');
        Route::post('/change-status-product-to-chef-processes', [Controllers\ARM\ChefARMController::class, 'ChangeStatusProductToChefProcesses'])->name('chef-arm-change-status-product-to-chef-processes');
        Route::post('/change-status-product-to-cooked', [Controllers\ARM\ChefARMController::class, 'ChangeStatusProductToCooKed'])->name('chef-arm-change-status-product-to-cooked');

    });

});


/*
 *  test routes
 */
Route::get('/test-ucaller', function () {
    return ;
//    $ucaller = new App\Services\Ucaller\Ucaller();
//    $balance = $ucaller->GetBalance();
//    $info = $ucaller->GetInfo();
//    $initCall = $ucaller->InitCall();
//    dd($balance, $info, $initCall);
});

Route::get('/test-bot', function () {
//    return ;
//    $message = 'Приветствуем. Вы сможете получать уведомления о статусе вашего заказа в этом боте. Осталось только поделиться номером телефона для синхронизации ваших заказов' . PHP_EOL;
//    $telegram = new Telegram('2081173182:AAEuKyhCNybjJTiZD-NQAxbhUj0YBNmopXk');
//    $telegram->RequestContact();
//    $telegram->sendMessage($message, '267236435');
});

Route::view('test-maps', 'debag.test');
