<?php

use App\Http\Controllers\Orders\OrdersController;
use App\Services\Telegram\Telegram;
use Illuminate\Support\Facades\Cache;
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

Route::get('/plug', function () {
    return view('plug');
})->name('plug-page');

Route::get('/', function () {
    return redirect(\route('plug-page'));
})->name('home-page');

Route::get('/cache-clear', function () {
    Cache::flush();
    return redirect(\route('home-page'));
})->name('cache-clear');

Route::get('/resources/{directory}/{fileName}', [Controllers\Resources\ResourceController::class, 'GetResources']);
Route::get('/files/{fileId}', [\App\Helpers\Files::class, 'GetFileHTTP'])->name('files');

Route::group(['prefix' => 'catalog'], function () {

    Route::get('/', [Controllers\Catalog\CatalogController::class, 'Index'])->name('catalog');

});
Route::group(['prefix' => 'review'], function () {

    Route::get('/', [Controllers\Reviews\ReviewsController::class, 'Index'])->name('review');

    Route::post('/create', [Controllers\Reviews\ReviewsController::class, 'Create'])->name('review-create');

});

Route::group(['prefix' => 'profile'], function () {

    Route::get('/', [Controllers\Profile\ProfileController::class, 'Index'])->name('profile');
    Route::get('/orders', [Controllers\Profile\ProfileController::class, 'Orders'])->name('profile-orders');

});

Route::group(['prefix' => 'orders'], function () {

    Route::post('/create', [Controllers\Orders\OrdersController::class, 'Create'])->name('order-create');
    Route::get('/order-info', [Controllers\Orders\OrdersController::class, 'OrderByIdForKitchenInterface'])->name('order-info');

    Route::post('/order-update-geo-yandex', [Controllers\Orders\OrdersController::class, 'UpdateYandexGeo'])->name('order-update-geo-yandex');

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
    Route::get('/all-sessions', [Controllers\Auth\AuthController::class, 'AllSessions'])->name('all-sessions-page');
    Route::get('/logout-all-devices', [Controllers\Auth\AuthController::class, 'LogoutAllDevices'])->name('logout-all-devices');

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

        Route::get('/', [Controllers\TypesModifications\TypesModificationsController::class, 'Index'])->name('types-modifications-page');
        Route::get('/create', [Controllers\TypesModifications\TypesModificationsController::class, 'Create'])->name('type-modification-create-page');
        Route::post('/save', [Controllers\TypesModifications\TypesModificationsController::class, 'Save'])->name('type-modification-save');

    });

    Route::group(['prefix' => 'ingredients'], function () {

        Route::get('/create', [Controllers\Ingredients\IngredientsController::class, 'Create'])->name('ingredients-create-page');
        Route::post('/save', [Controllers\Ingredients\IngredientsController::class, 'Save'])->name('ingredients-save');
        Route::get('/all', [Controllers\Ingredients\IngredientsController::class, 'AllIngredients'])->name('all-ingredients');
        Route::get('/all-to-csv', [Controllers\Ingredients\IngredientsController::class, 'AllIngredientsToCSV'])->name('all-ingredients-to-csv');

        Route::get('/products-used-ingredient/{ingredientId?}', [Controllers\Ingredients\IngredientsController::class, 'ProductsUsedIngredient'])->name('products-used-ingredient');

        Route::get('/ingredient-supply/{ingredientId?}', [Controllers\Ingredients\IngredientsController::class, 'IngredientSupply'])->name('ingredient-supply');

    });

    Route::group(['prefix' => 'supplies'], function () {

        Route::get('/', [Controllers\Supply\SupplyController::class, 'Index'])->name('supplies-page');
        Route::get('/create', [Controllers\Supply\SupplyController::class, 'Create'])->name('supply-create-page');
        Route::get('/detail/{supplyId?}', [Controllers\Supply\SupplyController::class, 'Detail'])->name('supply-detail-page');
        Route::get('/edit/{supplyId}', [Controllers\Supply\SupplyController::class, 'Edit'])->name('supply-edit-page');
        Route::post('/save', [Controllers\Supply\SupplyController::class, 'Save'])->name('supply-save');

    });

    Route::group(['prefix' => 'write-off'], function () {

        Route::get('/', [Controllers\WriteOff\WriteOffController::class, 'Index'])->name('write-offs-page');
        Route::get('/create', [Controllers\WriteOff\WriteOffController::class, 'Create'])->name('write-off-create-page');
        Route::get('/detail/{writeOffId}', [Controllers\WriteOff\WriteOffController::class, 'Detail'])->name('write-off-detail-page');
        Route::get('/edit/{writeOffId}', [Controllers\WriteOff\WriteOffController::class, 'Edit'])->name('write-off-edit-page');
        Route::post('/save', [Controllers\WriteOff\WriteOffController::class, 'Save'])->name('write-off-save');

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
        Route::get('/save/{employeeId}', [Controllers\Salary\SalaryController::class, 'Save'])->name('employee-card-save-page');

        Route::group(['prefix' => 'calendar'], function () {

            Route::get('/', [Controllers\Salary\SalaryController::class, 'Calendar'])->name('calendar-page');
            Route::post('/add-shift', [Controllers\Salary\SalaryController::class, 'AddShift'])->name('add-shift');
            Route::post('/delete-shift', [Controllers\Salary\SalaryController::class, 'DeleteShift'])->name('delete-shift');
            Route::post('/day-detail', [Controllers\Salary\SalaryController::class, 'DayDetail'])->name('day-detail-page');

        });

    });

    Route::group(['prefix' => 'administration'], function () {

        Route::get('/', [Controllers\ARM\AdministratorARMController::class, 'Index'])->name('administrator-arm-page');
        Route::get('/users', [Controllers\ARM\AdministratorARMController::class, 'Users'])->name('administrator-arm-users-page');
        Route::get('/user/{userId}/orders', [Controllers\ARM\AdministratorARMController::class, 'UserOrders'])->name('administrator-arm-user-orders-page');
        Route::post('/user-save-changes', [Controllers\ARM\AdministratorARMController::class, 'UserSaveChanges'])->name('administrator-arm-user-save-changes');

        Route::get('/orders', [Controllers\ARM\AdministratorARMController::class, 'Orders'])->name('administrator-arm-orders-page');
        Route::get('/orders-old', [Controllers\ARM\AdministratorARMController::class, 'OrdersOld'])->name('administrator-arm-orders-old-page');
        Route::get('/orders-addresses', [Controllers\ARM\AdministratorARMController::class, 'OrdersAddresses'])->name('administrator-arm-orders-addresses-page');

        Route::group(['prefix' => 'products'], function () {
            Route::get('/all', [Controllers\ARM\AdministratorARMController::class, 'Products'])->name('administrator-arm-products-page');
            Route::post('/product-save-changes', [Controllers\ARM\AdministratorARMController::class, 'ProductSaveChanges'])->name('administrator-arm-product-save-changes');
        });

        Route::group(['prefix' => 'ingredients'], function () {
            Route::get('/spent', [Controllers\ARM\AdministratorARMController::class, 'SpentIngredients'])->name('administrator-arm-spent-ingredients-page');
            Route::post('/ingredient-save-changes', [Controllers\ARM\AdministratorARMController::class, 'IngredientSaveChanges'])->name('administrator-arm-ingredient-save-changes');
            Route::post('/ingredient-in-supply-save-changes', [Controllers\ARM\AdministratorARMController::class, 'IngredientInSupplySaveChanges'])->name('administrator-arm-ingredient-in-supply-save-changes');
        });

        Route::get('/products-modifications', [Controllers\ARM\AdministratorARMController::class, 'ProductsModification'])->name('administrator-arm-products-modifications-page');

        Route::get('/device-used', [Controllers\ARM\AdministratorARMController::class, 'DeviceUsed'])->name('administrator-arm-device-used-page');

    });

    Route::group(['prefix' => 'management'], function () {

        Route::get('/', [Controllers\ARM\ManagerARMController::class, 'Index'])->name('manager-arm-page');

        Route::get('/orders', [Controllers\ARM\ManagerARMController::class, 'Orders'])->name('manager-arm-orders-page');

        Route::get('/order/{orderId?}', [Controllers\ARM\ManagerARMController::class, 'Order'])->name('manager-arm-order-page');
        Route::post('/order/search-by-phone', [Controllers\ARM\ManagerARMController::class, 'SearchByPhone'])->name('manager-arm-order-search-by-phone');
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

        Route::post('/delete-order-status', [Controllers\ARM\ManagerARMController::class, 'DeleteOrderStatus'])->name('manager-arm-delete-order-status');

        Route::post('/check-order-status-change', [Controllers\ARM\ManagerARMController::class, 'CheckOrderStatusChange'])->name('manager-arm-check-order-status-change');

        Route::post('/client-last-address', [Controllers\ARM\ManagerARMController::class, 'ClientLastAddress'])->name('manager-arm-client-last-address');

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

        Route::get('/orders-kitchen', [Controllers\ARM\ChefARMController::class, 'OrdersKitchenInterface'])->name('chef-arm-orders-kitchen-page');

        Route::get('/order/{orderId?}', [Controllers\ARM\ChefARMController::class, 'Order'])->name('chef-arm-order-page');

        Route::post('/change-status-order-to-cooked', [Controllers\ARM\ChefARMController::class, 'ChangeStatusOrderToCooked'])->name('chef-arm-change-status-order-to-cooked');

        Route::post('/change-status-product-to-new', [Controllers\ARM\ChefARMController::class, 'ChangeStatusProductToNew'])->name('chef-arm-change-status-product-to-new');
        Route::post('/change-status-product-to-chef-processes', [Controllers\ARM\ChefARMController::class, 'ChangeStatusProductToChefProcesses'])->name('chef-arm-change-status-product-to-chef-processes');
        Route::post('/change-status-product-to-cooked', [Controllers\ARM\ChefARMController::class, 'ChangeStatusProductToCooKed'])->name('chef-arm-change-status-product-to-cooked');

    });

    Route::group(['prefix' => 'send-sms'], function () {

        Route::get('/', [Controllers\SMSSender\SMSSenderController::class, 'Index'])->name('send-sms-index-page');

        Route::post('/send', [Controllers\SMSSender\SMSSenderController::class, 'SendSMS'])->name('send-sms');

    });

    Route::group(['prefix' => 'customer-returns'], function () {

        Route::get('/', [Controllers\CustomerReturns\CustomerReturnsController::class, 'Index'])->name('customer-returns-index');
        Route::get('/send', [Controllers\CustomerReturns\CustomerReturnsController::class, 'SendSms'])->name('customer-returns-send');

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
//    return OrdersController::OrderByIdForKitchenInterface();
//    event(new \App\Services\Pusher\NewOrderForKitchen(request()->orderId));
//});

Route::get('/debug-view', function () {
    return view('debug.index');
});
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

Route::get('/test-queue', function () {
    $job = \App\Jobs\ProcessPodcast::dispatch(now()->format('Y-m-d H:i:s'))->delay(now()->addMinute());
    dd('Good job');
});

// ToDO!!! закреп сообщение в телеграмм!!!

//Route::get('/test-pin-message', function () {//
//
//    $bitCoin = file_get_contents('http://api.bitcoincharts.com/v1/markets.json');
//    $json = json_decode($bitCoin);
//
//    $text = '';
//
//    $i = 0;
//    foreach ($json as $item) {
//
//        $text .= 'Валюта: ' . $item->currency . PHP_EOL;
//        $text .= 'Что-то: ' . $item->latest_trade . PHP_EOL;
//        $text .= 'Еще что-то: ' . $item->close . PHP_EOL;
//
//        if ($i > 10) {
//            break;
//        }
//
//        $i++;
//    }
//
//
//    $tg = new Telegram();
//    $res = $tg->sendMessage($text, env('TELEGRAM_BOT_ORDERS_CHAT'));
//
//    file_get_contents('https://api.telegram.org/bot1114911874:AAFWbIL-e3yBb61RvwVs2A_FsqNsZteG8A0/pinChatMessage?chat_id=267236435&message_id=' . json_decode($res)->result->message_id);
//
////    $res = $tg->pinChatMessage(, '');
//    dd($tg, json_decode($res));
//    dd('Good job');
//});
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


