<?php

use App\Http\Controllers\Orders\OrdersController;
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
|   Использование доступов в рутах
| ->middleware('permissions:UsersAndRoles, Users, permission')
| Route::group(['middleware' => 'permissions:UsersAndRoles, Users, permission'], function () {
|
*/

//Route::get('/', function () {
//    //return redirect('all-routes-page');
//})->name('home-page');

Route::get('/', [Controllers\Catalog\CatalogController::class, 'Index'])->name('home-page');

Route::get('/resources/{directory}/{fileName}', [Controllers\Resources\ResourceController::class, 'GetResources']);
Route::get('/files/{fileId}', [\App\Helpers\Files::class, 'GetFileHTTP'])->name('files');

Route::group(['prefix' => 'catalog'], function () {

    Route::get('/', [Controllers\Catalog\CatalogController::class, 'Index'])->name('catalog');

});

Route::group(['prefix' => 'orders'], function () {

    Route::post('/create', [Controllers\Orders\OrdersController::class, 'Create'])->name('order-create');

});

Route::group(['prefix' => 'settings'], function () {

    Route::get('/', [Controllers\Settings\SettingsController::class, 'Index'])->name('settings-page');

    Route::group(['prefix' => 'closed-message'], function () {

        Route::get('/', [Controllers\Settings\SettingsController::class, 'ClosedMessage'])->name('settings-closed-message-page');
        Route::post('/save', [Controllers\Settings\SettingsController::class, 'ClosedMessageSave'])->name('settings-closed-message-save');

    });

});

Route::group(['prefix' => 'promo-codes'], function () {

    Route::post('/check', [Controllers\PromoCodes\PromoCodesController::class, 'CheckPromoCodeRequest'])->name('check-promo-code');

});

Route::group(['prefix' => 'auth'], function () {

    Route::post('/phone-validation', [Controllers\Auth\AuthController::class, 'PhoneValidation'])->name('phone-validation');
    Route::post('/check-confirmation-code', [Controllers\Auth\AuthController::class, 'CheckConfirmationCode'])->name('check-confirmation-code');

    Route::get('/logout', [Controllers\Auth\AuthController::class, 'Logout'])->name('logout');

});

Route::group(['prefix' => 'arm', 'middleware' => 'permission:ARM'], function () {

    //Route::get('all-routes', [Controllers\ARM\ARMController::class, 'AllRoutesPage'])->name('all-routes-page');

    Route::group(['prefix' => 'products'], function () {

        Route::get('/all-admin', [Controllers\Products\ProductsController::class, 'IndexAdmin'])->name('all-products-admin-page');
        Route::get('/all-products', [Controllers\Products\ProductsController::class, 'GetAllProducts'])->name('all-products');
        Route::get('/create-page', [Controllers\Products\ProductsController::class, 'CreatePage'])->name('product-create-page');
        Route::post('/create', [Controllers\Products\ProductsController::class, 'Create'])->name('product-create');

    });

    Route::group(['prefix' => 'modifications'], function () {

        Route::get('/create', [Controllers\Modifications\ModificationsController::class, 'Create'])->name('modification-create-page');
        Route::post('/save', [Controllers\Modifications\ModificationsController::class, 'Save'])->name('modification-save');
        Route::get('/all', [Controllers\Modifications\ModificationsController::class, 'GetAllModifications'])->name('all-modifications');

    });

    Route::group(['prefix' => 'types-modifications'], function () {

        Route::get('/create', [Controllers\TypesModifications\TypesModificationsController::class, 'Create'])->name('modification-type-create-page');
        Route::post('/save', [Controllers\TypesModifications\TypesModificationsController::class, 'Save'])->name('modification-type-save');

    });

    Route::group(['prefix' => 'ingredients'], function () {

        Route::get('/create', [Controllers\Ingredients\IngredientsController::class, 'Create'])->name('ingredients-create-page');
        Route::post('/save', [Controllers\Ingredients\IngredientsController::class, 'Save'])->name('ingredients-save');
        Route::get('/all', [Controllers\Ingredients\IngredientsController::class, 'AllIngredients'])->name('all-ingredients');

    });

    Route::group(['prefix' => 'supplies'], function () {

        Route::get('/', [Controllers\Supply\SupplyController::class, 'Index'])->name('supplies-page');
        Route::get('/create', [Controllers\Supply\SupplyController::class, 'Create'])->name('supply-create-page');
        Route::get('/detail/{supplyId}', [Controllers\Supply\SupplyController::class, 'Detail'])->name('supply-detail-page');
        Route::get('/edit/{supplyId}', [Controllers\Supply\SupplyController::class, 'Edit'])->name('supply-edit-page');
        Route::post('/save', [Controllers\Supply\SupplyController::class, 'Save'])->name('supply-save');

    });

    Route::group(['prefix' => 'suppliers'], function () {

        Route::get('/create', [Controllers\Suppliers\SuppliersController::class, 'Create'])->name('supplier-create-page');
        Route::post('/save', [Controllers\Suppliers\SuppliersController::class, 'Save'])->name('supplier-save');

    });

    Route::group(['prefix' => 'promo-codes'], function () {

        Route::get('/', [Controllers\PromoCodes\PromoCodesController::class, 'AllPromoCodesPage'])->name('all-promo-codes-page');
        Route::get('/create-page', [Controllers\PromoCodes\PromoCodesController::class, 'CreatePromoCodePage'])->name('create-promo-code-page');
        Route::post('/create', [Controllers\PromoCodes\PromoCodesController::class, 'CreatePromoCodeRequest'])->name('create-promo-code');
        Route::post('/change-active-promo-code', [Controllers\PromoCodes\PromoCodesController::class, 'ChangeActivePromoCode'])->name('change-active-promo-code');

    });

    Route::group(['prefix' => 'salary'], function () {

        Route::get('/', [Controllers\Salary\SalaryController::class, 'Index'])->name('salary-page');
        Route::get('/employees', [Controllers\Salary\SalaryController::class, 'Employees'])->name('employees-page');
        Route::get('/employee-card/{employeeId}', [Controllers\Salary\SalaryController::class, 'EmployeeCard'])->name('employee-card-page');

    });

    Route::group(['prefix' => 'administration'], function () {

        Route::get('/', [Controllers\ARM\AdministratorARMController::class, 'Index'])->name('administrator-arm-page');
        Route::get('/users', [Controllers\ARM\AdministratorARMController::class, 'Users'])->name('administrator-arm-users-page');
        Route::get('/user/{userId}/orders', [Controllers\ARM\AdministratorARMController::class, 'UserOrders'])->name('administrator-arm-user-orders-page');
        Route::post('/user-save-changes', [Controllers\ARM\AdministratorARMController::class, 'UserSaveChanges'])->name('administrator-arm-user-save-changes');

        Route::get('/orders', [Controllers\ARM\AdministratorARMController::class, 'Orders'])->name('administrator-arm-orders-page');

        Route::group(['prefix' => 'products'], function () {
            Route::get('/all', [Controllers\ARM\AdministratorARMController::class, 'Products'])->name('administrator-arm-products-page');
            Route::post('/product-save-changes', [Controllers\ARM\AdministratorARMController::class, 'ProductSaveChanges'])->name('administrator-arm-product-save-changes');
        });

        Route::group(['prefix' => 'ingredients'], function () {
            Route::get('/all', [Controllers\ARM\AdministratorARMController::class, 'Ingredients'])->name('administrator-arm-ingredients-page');
            Route::get('/spent', [Controllers\ARM\AdministratorARMController::class, 'SpentIngredients'])->name('administrator-arm-spent-ingredients-page');
            Route::post('/ingredient-save-changes', [Controllers\ARM\AdministratorARMController::class, 'IngredientSaveChanges'])->name('administrator-arm-ingredient-save-changes');
        });

        Route::get('/products-modifications', [Controllers\ARM\AdministratorARMController::class, 'ProductsModification'])->name('administrator-arm-products-modifications-page');

        Route::get('/device-used', [Controllers\ARM\AdministratorARMController::class, 'DeviceUsed'])->name('administrator-arm-device-used-page');

    });

    Route::group(['prefix' => 'management'], function () {

        Route::get('/', [Controllers\ARM\ManagerARMController::class, 'Index'])->name('manager-arm-page');

        Route::get('/orders', [Controllers\ARM\ManagerARMController::class, 'Orders'])->name('manager-arm-orders-page');

        Route::get('/order/{orderId?}', [Controllers\ARM\ManagerARMController::class, 'Order'])->name('manager-arm-order-page');
        Route::post('/order/search-by-phone', [Controllers\ARM\ManagerARMController::class, 'SearchByPhone'])->name('manager-arm-order-search-bu-phone');
        Route::post('/order/change-payment-type', [Controllers\ARM\ManagerARMController::class, 'OrderChangePaymentType'])->name('manager-arm-order-change-payment-type');

        Route::get('/order/{orderId}/invoice', [Controllers\ARM\ManagerARMController::class, 'InvoicePage'])->name('manager-arm-order-invoice-page');
        Route::get('/order/{orderId}/invoice-chef', [Controllers\ARM\ManagerARMController::class, 'InvoiceChefPage'])->name('manager-arm-order-invoice-chef-page');

        Route::post('/change-status-order-to-new-order', [Controllers\ARM\ManagerARMController::class, 'ChangeStatusOrderToNewOrder'])->name('manager-arm-change-status-order-to-new-order-page');
        Route::post('/change-status-order-to-manager-processes', [Controllers\ARM\ManagerARMController::class, 'ChangeStatusOrderToManagerProcessesRequest'])->name('manager-arm-change-status-order-to-manager-processes-page');
        Route::post('/transfer-order-to-kitchen', [Controllers\ARM\ManagerARMController::class, 'TransferOrderToKitchen'])->name('manager-arm-transfer-order-to-kitchen-page');
        Route::post('/transfer-order-to-delivery', [Controllers\ARM\ManagerARMController::class, 'TransferOrderToDelivery'])->name('manager-arm-transfer-order-to-delivery-page');
        Route::post('/change-courier-in-order', [Controllers\ARM\ManagerARMController::class, 'ChangeCourierInOrderRequest'])->name('manager-arm-change-courier-in-order');
        Route::post('/change-status-order-to-completed', [Controllers\ARM\ManagerARMController::class, 'ChangeStatusOrderToCompleted'])->name('manager-arm-change-status-order-to-completed-page');
        Route::post('/change-status-order-to-canceled', [Controllers\ARM\ManagerARMController::class, 'ChangeStatusOrderToCanceled'])->name('manager-arm-change-status-order-to-canceled-page');

        Route::post('/check-order-status-change', [Controllers\ARM\ManagerARMController::class, 'CheckOrderStatusChange'])->name('manager-arm-check-order-status-change');

        #todo на курьера
        Route::post('/change-status-order-to-delivered', [Controllers\ARM\ManagerARMController::class, 'ChangeStatusOrderToDelivered'])->name('manager-arm-change-status-order-to-delivered');

        Route::group(['prefix' => 'modifications'], function () {

            Route::get('/', [Controllers\ProductModifications\ProductModificationsController::class, 'Edit'])->name('manager-arm-products-modifications-page');
            Route::post('/save', [Controllers\ProductModifications\ProductModificationsController::class, 'Save'])->name('manager-arm-products-modifications-save');

        });


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

//Route::get('/payment-paid', [Controllers\Payments\PaymentsController::class, 'PaymentPaidRequest'])->name('payment-paid');
//Route::get('/payment-error', [Controllers\Payments\PaymentsController::class, 'PaymentErrorRequest'])->name('payment-error');
//Route::get('/payment-status', [Controllers\Payments\PaymentsController::class, 'PaymentStatusRequest'])->name('payment-status');
//Route::get('/payment-refund', [Controllers\Payments\PaymentsController::class, 'PaymentRefundRequest'])->name('payment-refund');
//
//Route::get('/today-report', [Controllers\TelegramBOT\TelegramBotController::class, 'TodayReportRequest']);
//Route::get('/yesterday-report', [Controllers\TelegramBOT\TelegramBotController::class, 'YesterdayReportRequest']);
//Route::get('/week-report', [Controllers\TelegramBOT\TelegramBotController::class, 'WeekReportRequest']);
//Route::get('/last-week-report', [Controllers\TelegramBOT\TelegramBotController::class, 'LastWeekReportRequest']);
//Route::get('/month-report', [Controllers\TelegramBOT\TelegramBotController::class, 'MonthReportRequest']);
//Route::get('/last-month-report', [Controllers\TelegramBOT\TelegramBotController::class, 'LastMonthReportRequest']);
//Route::get('/report', [Controllers\TelegramBOT\TelegramBotController::class, 'ReportRequest']);
//
//Route::view('/pusher', 'arm.test-view.pusher');
//
//Route::get('/test-pusher-event', function () {
//    event(new App\Services\Pusher\Pusher(1, 1, 1));
//});
//
//
///*
// *  test routes
// */
//Route::get('/test-ucaller', function () {
//    $ucaller = new App\Services\Ucaller\Ucaller();
//    $balance = $ucaller->GetBalance();
//    $info = $ucaller->GetInfo();
//    $initCall = $ucaller->InitCall();
//    dd($balance, $info, $initCall);
//});
//

//Route::get('/location-view', function () {
//    return view('debug.location');
//});
//
//Route::get('/location-hook', function () {
//    event(new \App\Services\Pusher\Location(request()->get('lat'), request()->get('lon')));
//});

Route::get('/ucaller-balance', function () {
//    return;
    $ucaller = new App\Services\Ucaller\Ucaller();
    $balance = $ucaller->GetBalance();
    dd($balance);
});
//
//Route::get('/test-bot', function () {
////    return ;
//    $message = 'Приветствуем. Вы сможете получать уведомления о статусе вашего заказа в этом боте. Осталось только поделиться номером телефона для синхронизации ваших заказов' . PHP_EOL;
//    $message = 'Отправьте номер для связывания аккаунта на сайте и в телеграм' . PHP_EOL;
////    $telegram = new Telegram('2081173182:AAEuKyhCNybjJTiZD-NQAxbhUj0YBNmopXk');
//    $telegram = new Telegram('1114911874:AAFWbIL-e3yBb61RvwVs2A_FsqNsZteG8A0');
//    $telegram->RequestContact();
////    $telegram->sendMessage($message, '267236435');
//    $telegram->sendMessage($message, '267236435');
//});
//
//Route::get('/test-parse', function () {
//
//    $adresses = [
//        'Дубна, Московская область, Россия, улица Вернова, 9',
//        'улица Попова, 3, Дубна, Московская область, Россия',
//        'улица Понтекорво, 2, Дубна, Московская область, Россия',
//        'Дубна, Московская область, Россия, улица Вернова, 9',
//    ];
//
////    dd(implode(' ~ ', $adresses));
//
////    $cookiesRaw = \App\Helpers\ArrayHelper::ObjectToArray(json_decode(file_get_contents('./cookie.json')));
////    $cookies = '';
////    if ($cookiesRaw !== null) {
////        foreach ($cookiesRaw as $key => $cookie) {
////            $lastSymbol = array_key_last($cookiesRaw) === $key ? '' : ';';
////            $cookies .= $key . '=' . $cookie . $lastSymbol;
////        }
////    }
//
//    $param = http_build_query([
//        'll' => '37.15875'.rand(6, 8).',56.73777'.rand(6, 8).'',
//        'mode' => 'routes',
//        'rtext' => 'Дубна, Московская область, Россия, улица Вернова, 9  ~ улица Попова, 3, Дубна, Московская область, Россия ~ улица Понтекорво, 2, Дубна, Московская область, Россия ~ Дубна, Московская область, Россия, улица Вернова, 9',
//        'rtt' => 'auto',
//        'z' => ''.rand(10, 20),
//    ]);
//
////    $url = 'https://yandex.ru/maps/?' . $param;
//    $url = 'https://yandex.ru/maps/215/dubna/?' . $param;
//
//    $headers = [
//        'accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
//        'accept-encoding' => 'gzip, deflate, br',
//        'accept-language' => 'ru',
//        'cache-control' => 'no-cache',
//        'pragma' => 'no-cache',
//        'preferanonymous' => '1',
//        'sec-ch-ua' => '"Chromium";v="94", "Microsoft Edge";v="94", ";Not A Brand";v="99"',
//        'sec-ch-ua-mobile' => '?0',
//        'sec-ch-ua-platform' => '"Windows"',
//        'sec-fetch-dest' => 'document',
//        'sec-fetch-mode' => 'navigate',
//        'sec-fetch-site' => 'none',
//        'sec-fetch-user' => '?1',
//        'upgrade-insecure-requests' => '1',
//        'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/94.0.4606.81 Safari/537.36 Edg/94.0.992.50',
//        'viewport-width' => '2560',
////        'cookie' => $cookies,
////        'postman-token' => Hash::make(\Illuminate\Support\Str::random()),
//    ]; // создаем заголовки
//
//    $curl = curl_init(); // создаем экземпляр curl
//
////    curl_setopt($curl, CURLOPT_HEADER, 1);
//    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
//    curl_setopt($curl, CURLOPT_POST, false); //
//    curl_setopt($curl, CURLOPT_URL, $url);
//
//    curl_setopt($curl, CURLOPT_AUTOREFERER, true);
//    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
//    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
//    curl_setopt($curl, CURLOPT_VERBOSE, 1);
//    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 1);
//
//    $result = curl_exec($curl);
//
////    preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $result, $matches);
////    $cookies = [];
////    foreach($matches[1] as $item) {
////        parse_str($item, $cookie);
////        $cookies = array_merge($cookies, $cookie);
////    }
////
////    file_put_contents('./cookie.json', json_encode($cookies));
//
//    preg_match('({"appVersion.+})', $result, $matches);
//    if (!sizeof($matches)) {
//        $result = str_replace('href="/captcha', 'href="https://yandex.ru/captcha', $result);
//        $result = str_replace('action="/checkcaptcha', 'action="https://yandex.ru/checkcaptcha', $result);
//        $result = str_replace('src="/captcha', 'src="https://yandex.ru/captcha', $result);
//        echo $result;
//        exit;
//    } else {
//        $resultYa = json_decode($matches[0]);
//        $distance = (float)(str_replace(',', '.', $resultYa->routerResponse->routes[0]->distance->text));
//        $startTime = $resultYa->routerResponse->routes[0]->paths[array_key_first($resultYa->routerResponse->routes[0]->paths)]->beginTime->value;
//        $endTime = $resultYa->routerResponse->routes[0]->paths[array_key_last($resultYa->routerResponse->routes[0]->paths)]->endTime->value;
//        $deliveryTime = ($endTime - $startTime) / 60;
//
//
//        $data = (object)[
//            'distance' => $distance,
//            'deliveryTime' => $deliveryTime,
//        ];
//        dd($data, $resultYa);
//    }
//});


