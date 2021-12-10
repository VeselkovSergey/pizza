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

//Route::get('/', function () {
//    //return redirect('all-routes');
//})->name('home-page');

Route::get('/', [Controllers\Catalog\CatalogController::class, 'Index'])->name('home-page');

Route::get('all-routes', [Controllers\ARM\ARMController::class, 'AllRoutes'])->name('all-routes');

Route::get('resources/{directory}/{fileName}', [Controllers\Resources\ResourceController::class, 'GetResources']);

Route::group(['prefix' => 'catalog'], function () {

    Route::get('/', [Controllers\Catalog\CatalogController::class, 'Index'])->name('catalog');

});

Route::group(['prefix' => 'products'], function () {

    Route::get('/all-admin', [Controllers\Products\ProductsController::class, 'IndexAdmin'])->name('all-products-admin-page');
    Route::get('/all-products', [Controllers\Products\ProductsController::class, 'GetAllProducts'])->name('all-products');
    Route::get('/create', [Controllers\Products\ProductsController::class, 'Create'])->name('product-create');
    Route::post('/save', [Controllers\Products\ProductsController::class, 'Save'])->name('product-save');

});

Route::group(['prefix' => 'modifications'], function () {

    Route::get('/create', [Controllers\Modifications\ModificationsController::class, 'Create'])->name('modification-create');
    Route::post('/save', [Controllers\Modifications\ModificationsController::class, 'Save'])->name('modification-save');
    Route::get('/all', [Controllers\Modifications\ModificationsController::class, 'GetAllModifications'])->name('all-modifications');

});

Route::group(['prefix' => 'types-modifications'], function () {

    Route::get('/create', [Controllers\TypesModifications\TypesModificationsController::class, 'Create'])->name('modification-type-create');
    Route::post('/save', [Controllers\TypesModifications\TypesModificationsController::class, 'Save'])->name('modification-type-save');

});

Route::group(['prefix' => 'ingredients'], function () {

    Route::get('/create', [Controllers\Ingredients\IngredientsController::class, 'Create'])->name('ingredients-create');
    Route::post('/save', [Controllers\Ingredients\IngredientsController::class, 'Save'])->name('ingredients-save');
    Route::get('/all', [Controllers\Ingredients\IngredientsController::class, 'GetAllIngredients'])->name('all-ingredients');

});

Route::group(['prefix' => 'supply'], function () {

    Route::get('/create', [Controllers\Supply\SupplyController::class, 'Create'])->name('supply-create');
    Route::post('/save', [Controllers\Supply\SupplyController::class, 'Save'])->name('supply-save');

});

Route::group(['prefix' => 'suppliers'], function () {

    Route::get('/create', [Controllers\Suppliers\SuppliersController::class, 'Create'])->name('supplier-create');
    Route::post('/save', [Controllers\Suppliers\SuppliersController::class, 'Save'])->name('supplier-save');

});

Route::group(['prefix' => 'order'], function () {

    Route::post('/create', [Controllers\Orders\OrdersController::class, 'Create'])->name('order-create');

});

Route::group(['prefix' => 'auth'], function () {

    Route::post('/phone-validation', [Controllers\Auth\AuthController::class, 'PhoneValidation'])->name('phone-validation');
    Route::post('/check-confirmation-code', [Controllers\Auth\AuthController::class, 'CheckConfirmationCode'])->name('check-confirmation-code');

    Route::get('/logout', [Controllers\Auth\AuthController::class, 'Logout'])->name('logout');

});

Route::group(['prefix' => 'arm'], function () {


    Route::group(['prefix' => 'administration'], function () {

        Route::get('/', [Controllers\ARM\AdministratorARMController::class, 'Index'])->name('administrator-arm-page');

    });

    Route::group(['prefix' => 'management'], function () {

        Route::get('/', [Controllers\ARM\ManagerARMController::class, 'Index'])->name('manager-arm-page');

        Route::get('/orders', [Controllers\ARM\ManagerARMController::class, 'Orders'])->name('manager-arm-orders-page');

        Route::get('/order/{orderId?}', [Controllers\ARM\ManagerARMController::class, 'Order'])->name('manager-arm-order-page');
        Route::post('/order/search-by-phone', [Controllers\ARM\ManagerARMController::class, 'SearchByPhone'])->name('manager-arm-order-search-bu-phone');

        Route::post('/change-status-order-to-new-order', [Controllers\ARM\ManagerARMController::class, 'ChangeStatusOrderToNewOrder'])->name('manager-arm-change-status-order-to-new-order-page');
        Route::post('/change-status-order-to-manager-processes', [Controllers\ARM\ManagerARMController::class, 'ChangeStatusOrderToManagerProcessesRequest'])->name('manager-arm-change-status-order-to-manager-processes-page');
        Route::post('/transfer-order-to-kitchen', [Controllers\ARM\ManagerARMController::class, 'TransferOrderToKitchen'])->name('manager-arm-transfer-order-to-kitchen-page');
        Route::post('/transfer-order-to-delivery', [Controllers\ARM\ManagerARMController::class, 'TransferOrderToDelivery'])->name('manager-arm-transfer-order-to-delivery-page');
        Route::post('/change-status-order-to-completed', [Controllers\ARM\ManagerARMController::class, 'ChangeStatusOrderToCompleted'])->name('manager-arm-change-status-order-to-completed-page');
        Route::post('/change-status-order-to-canceled', [Controllers\ARM\ManagerARMController::class, 'ChangeStatusOrderToCanceled'])->name('manager-arm-change-status-order-to-canceled-page');

        Route::post('/check-order-status-change', [Controllers\ARM\ManagerARMController::class, 'CheckOrderStatusChange'])->name('manager-arm-check-order-status-change');

        #todo на курьера
        Route::post('/change-status-order-to-delivered', [Controllers\ARM\ManagerARMController::class, 'ChangeStatusOrderToDelivered'])->name('manager-arm-change-status-order-to-delivered');

    });

    Route::group(['prefix' => 'chef'], function () {

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
    return;
//    $ucaller = new App\Services\Ucaller\Ucaller();
//    $balance = $ucaller->GetBalance();
//    $info = $ucaller->GetInfo();
//    $initCall = $ucaller->InitCall();
//    dd($balance, $info, $initCall);
});

Route::get('/ucaller-balance', function () {
//    return;
    $ucaller = new App\Services\Ucaller\Ucaller();
    $balance = $ucaller->GetBalance();
    dd($balance);
});

Route::get('/test-bot', function () {
//    return ;
//    $message = 'Приветствуем. Вы сможете получать уведомления о статусе вашего заказа в этом боте. Осталось только поделиться номером телефона для синхронизации ваших заказов' . PHP_EOL;
    $telegram = new Telegram('2081173182:AAEuKyhCNybjJTiZD-NQAxbhUj0YBNmopXk');
//    $telegram->RequestContact();
//    $telegram->sendMessage($message, '267236435');
});

Route::get('/test-parse', function () {

    $adresses = [
        'Дубна, Московская область, Россия, улица Вернова, 9',
        'улица Попова, 3, Дубна, Московская область, Россия',
        'улица Понтекорво, 2, Дубна, Московская область, Россия',
        'Дубна, Московская область, Россия, улица Вернова, 9',
    ];

//    dd(implode(' ~ ', $adresses));

//    $cookiesRaw = \App\Helpers\ArrayHelper::ObjectToArray(json_decode(file_get_contents('./cookie.json')));
//    $cookies = '';
//    if ($cookiesRaw !== null) {
//        foreach ($cookiesRaw as $key => $cookie) {
//            $lastSymbol = array_key_last($cookiesRaw) === $key ? '' : ';';
//            $cookies .= $key . '=' . $cookie . $lastSymbol;
//        }
//    }

    $param = http_build_query([
        'll' => '37.15875'.rand(6, 8).',56.73777'.rand(6, 8).'',
        'mode' => 'routes',
        'rtext' => 'Дубна, Московская область, Россия, улица Вернова, 9  ~ улица Попова, 3, Дубна, Московская область, Россия ~ улица Понтекорво, 2, Дубна, Московская область, Россия ~ Дубна, Московская область, Россия, улица Вернова, 9',
        'rtt' => 'auto',
        'z' => ''.rand(10, 20),
    ]);

//    $url = 'https://yandex.ru/maps/?' . $param;
    $url = 'https://yandex.ru/maps/215/dubna/?' . $param;

    $headers = [
        'accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
        'accept-encoding' => 'gzip, deflate, br',
        'accept-language' => 'ru',
        'cache-control' => 'no-cache',
        'pragma' => 'no-cache',
        'preferanonymous' => '1',
        'sec-ch-ua' => '"Chromium";v="94", "Microsoft Edge";v="94", ";Not A Brand";v="99"',
        'sec-ch-ua-mobile' => '?0',
        'sec-ch-ua-platform' => '"Windows"',
        'sec-fetch-dest' => 'document',
        'sec-fetch-mode' => 'navigate',
        'sec-fetch-site' => 'none',
        'sec-fetch-user' => '?1',
        'upgrade-insecure-requests' => '1',
        'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/94.0.4606.81 Safari/537.36 Edg/94.0.992.50',
        'viewport-width' => '2560',
//        'cookie' => $cookies,
//        'postman-token' => Hash::make(\Illuminate\Support\Str::random()),
    ]; // создаем заголовки

    $curl = curl_init(); // создаем экземпляр curl

//    curl_setopt($curl, CURLOPT_HEADER, 1);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_POST, false); //
    curl_setopt($curl, CURLOPT_URL, $url);

    curl_setopt($curl, CURLOPT_AUTOREFERER, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_VERBOSE, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 1);

    $result = curl_exec($curl);

//    preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $result, $matches);
//    $cookies = [];
//    foreach($matches[1] as $item) {
//        parse_str($item, $cookie);
//        $cookies = array_merge($cookies, $cookie);
//    }
//
//    file_put_contents('./cookie.json', json_encode($cookies));

    preg_match('({"appVersion.+})', $result, $matches);
    if (!sizeof($matches)) {
        $result = str_replace('href="/captcha', 'href="https://yandex.ru/captcha', $result);
        $result = str_replace('action="/checkcaptcha', 'action="https://yandex.ru/checkcaptcha', $result);
        $result = str_replace('src="/captcha', 'src="https://yandex.ru/captcha', $result);
        echo $result;
        exit;
    } else {
        $resultYa = json_decode($matches[0]);
        $distance = (float)(str_replace(',', '.', $resultYa->routerResponse->routes[0]->distance->text));
        $startTime = $resultYa->routerResponse->routes[0]->paths[array_key_first($resultYa->routerResponse->routes[0]->paths)]->beginTime->value;
        $endTime = $resultYa->routerResponse->routes[0]->paths[array_key_last($resultYa->routerResponse->routes[0]->paths)]->endTime->value;
        $deliveryTime = ($endTime - $startTime) / 60;


        $data = (object)[
            'distance' => $distance,
            'deliveryTime' => $deliveryTime,
        ];
        dd($data, $resultYa);
    }
});

Route::view('test-maps', 'debag.test');
