<?php


namespace App\Http\Controllers\TelegramBOT;


use App\Http\Controllers\ARM\CourierARMController;
use App\Http\Controllers\ARM\ManagerARMController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Orders\OrdersController;
use App\Http\Controllers\PromoCodes\PromoCodesController;
use App\Models\Ingredients;
use App\Models\Orders;
use App\Models\ProductModificationsIngredients;
use App\Models\ProductsModificationsInOrders;
use App\Models\User;
use App\Services\Telegram\Telegram;

class TelegramBotController extends Controller
{
    // 267236435
    function Index()
    {
        try {
            $telegram = new Telegram('1114911874:AAFWbIL-e3yBb61RvwVs2A_FsqNsZteG8A0');

            $command = $telegram->incomingMessage();

            $checkContact = $telegram->checkContact();
            if (!empty($checkContact)) {
                $telegram->inlineKeyboard['remove_keyboard'] = true;
                $telegram->sendMessage('Успешно! '. PHP_EOL .'Номер: ' . $checkContact->phone . PHP_EOL . 'ID чата: ' . $checkContact->chatId);

                $user = AuthController::FastRegistrationUserByPhone($checkContact->phone);
                $user->telegram_chat_id = $checkContact->chatId;
                $user->save();
            }

            switch ($command) {

                case '/start':
                    // текст сообщения
                    $telegram->sendMessage('BROпицца!');
                    $telegram->sendMessage('Твой id чата: ' . $telegram->ChatId());

                    $user = User::where('telegram_chat_id', $telegram->ChatId())->first();
                    if (!$user) {
                        $telegram->RequestContact();
                        $telegram->sendMessage('Отправьте номер для связывания аккаунта на сайте и в телеграм');
                    }

                    break;

                case '/this_chat_id':
                    // текст сообщения
                    $telegram->sendMessage('Прилетело из чата: ' . $telegram->SenderChat(), $telegram->SenderChat());

                    break;

                case 'Delivered':
                    // Одна кнопка
                    $messageId = $telegram->MessageId();
                    $telegram->deleteMessage();
                    $telegram->sendMessage('Отлично! Ты молодец!');
                    CourierARMController::ChangeStatusOrderToDelivered($messageId);
                    break;

                case 'Refused':
                    // Одна кнопка
                    $messageId = $telegram->MessageId();
                    $telegram->deleteMessage();
                    $telegram->sendMessage('Жаль! Надеюсь ты старался ;)');
                    CourierARMController::ChangeStatusOrderToCanceled($messageId);
                    break;

                case 'Error':
                    $messageId = $telegram->MessageId();
                    $telegram->deleteMessage();
                    $telegram->sendMessage('Все мы люди.. И все мы можем ошибиться...');
                    CourierARMController::CourierError($messageId);
                    break;

                case '/chatId':
                    $telegram->sendMessage('Твой id чата: ' . $telegram->ChatId());
                    break;

                case '/all':
                    $message = '<b>Команды:</b>' . PHP_EOL;
                    $message .= 'Показать все команды: /all' . PHP_EOL;
                    $message .= 'Получить ID чата /chatId' . PHP_EOL;

                    $user = User::where('telegram_chat_id', $telegram->ChatId())->first();
                    if ($user && $user->UserIsAdmin()) {
                        $message .= 'Отчёт /todayReport' . PHP_EOL;
                        $message .= 'Отчёт /yesterdayReport' . PHP_EOL;
                        $message .= 'Отчёт /weekReport' . PHP_EOL;
                        $message .= 'Отчёт /lastWeekReport' . PHP_EOL;
                        $message .= 'Отчёт /monthReport' . PHP_EOL;
                        $message .= 'Отчёт /lastMonthReport' . PHP_EOL;
                        $message .= 'Отчёт /fullReport' . PHP_EOL;
                    }

                    $telegram->sendMessage($message);
                    break;

                case '/sale25':
                case '/sale50':
                    $message = '';

                    $user = User::where('telegram_chat_id', $telegram->ChatId())->first();
                    if ($user && $user->UserIsAdmin() && false) {
                        #todo доделать для комбо
                        if ($command === '/sale25') {
                            $message = self::PromoCodes(25, 'Промо 25%');
                        } else if ($command === '/sale50') {
                            $message = self::PromoCodes(50, 'Промо 50%');
                        }
                    } else {
                        $message .= 'Братан. Ты что-то попутал ;)' . PHP_EOL;
                    }

                    $telegram->sendMessage($message);
                    break;

                case '/todayReport':
                case '/yesterdayReport':
                case '/weekReport':
                case '/lastWeekReport':
                case '/monthReport':
                case '/lastMonthReport':
                case '/fullReport':

                    $user = User::where('telegram_chat_id', $telegram->ChatId())->first();
                    if (!($user && $user->UserIsAdmin())) {
                        $telegram->sendMessage('Неа ;) не прокатит! Ты слишком мал;)');
                        break;
                    }

                    if ($command === '/todayReport') {

                        $report = self::TodayReportRequest();
                        $text = '<b>Отчёт за сегодня:</b>';

                    } elseif ($command === '/yesterdayReport') {

                        $report = self::YesterdayReportRequest();
                        $text = '<b>Отчёт за вчера:</b>';

                    } elseif ($command === '/weekReport') {

                        $report = self::WeekReportRequest();
                        $text = '<b>Отчёт за неделю:</b>';

                    } elseif ($command === '/lastWeekReport') {

                        $report = self::LastWeekReportRequest();
                        $text = '<b>Отчёт за прошлую неделю:</b>';

                    } elseif ($command === '/monthReport') {

                        $report = self::MonthReportRequest();
                        $text = '<b>Отчёт за месяц:</b>';

                    } elseif ($command === '/lastMonthReport') {

                        $report = self::LastMonthReportRequest();
                        $text = '<b>Отчёт за прошлый месяц:</b>';

                    } else {

                        $report = self::Report();
                        $text = '<b>Отчёт за всё время:</b>';

                    }

                    $message = $text . PHP_EOL;
                    $message .= 'Кол-во заказов: ' . $report->countOrder . '(отказов: ' . $report->amountCancelled . ')' . PHP_EOL;
                    $message .= 'Сумма: ' . $report->sum . ' ₽' . PHP_EOL;
                    $message .= 'Сумма банк: ' . $report->sumBank . ' ₽' . PHP_EOL;
                    $message .= 'Сумма нал: ' . $report->sumCash . ' ₽' . PHP_EOL;
                    $message .= 'Средний чек: ' . $report->averageCheck . ' ₽' . PHP_EOL;
                    $message .= 'Себестоимость: ' . $report->costPrice . ' ₽' . PHP_EOL;
                    $message .= 'Прибыль: ' . $report->profit . ' ₽' . PHP_EOL;
                    $telegram->sendMessage($message);
                    break;
            }
        } catch (\Exception $e) {
            \Log::error($e);
        }
    }

    public static function PromoCodes($percent, $description)
    {
        $message = '';
        $res = PromoCodesController::GenerateSale($percent, $description);
        if ($res !== false) {
            $message .= $res . PHP_EOL;
        } else {
            $message .= 'Попробуй еще раз!' . PHP_EOL;
        }
        return $message;
    }

    public static function TodayReportRequest()
    {
        return self::Report(now());
    }

    public static function YesterdayReportRequest()
    {
        return self::Report(now()->addDays(-1));
    }

    public static function WeekReportRequest()
    {
        return self::Report(now()->startOfWeek(), now()->endOfWeek());
    }

    public static function LastWeekReportRequest()
    {
        return self::Report(now()->addWeek(-1)->startOfWeek(), now()->addWeek(-1)->endOfWeek());
    }

    public static function MonthReportRequest()
    {
        return self::Report(now()->startOfMonth(), now()->endOfMonth());
    }

    public static function LastMonthReportRequest()
    {
        return self::Report(now()->addMonth(-1)->startOfMonth(), now()->addMonth(-1)->endOfMonth());
    }

    public static function ReportRequest()
    {
        return self::Report();
    }

    public static function Report($startDate = null, $endDate = null)
    {
        if ($startDate) {
            $endDate = $endDate ?: $startDate;
            $orders = Orders::ByDate($startDate, $endDate, true);
        } else {
            $orders = Orders::AllOrders('ASC');
        }

        $ordersCount = $orders->count();

        $sum = 0;
        $sumCash = 0;
        $sumBank = 0;
        $amountCancelled = 0;
        $costPrice = 0;

        foreach ($orders as $order) {
            $clientInfo = json_decode($order->client_raw_data);

            $productsModifications = $order->ProductsModifications;

            $orderCostPrice = 0;

            foreach ($productsModifications as $productModification) {
                /** @var ProductsModificationsInOrders $productModification */

                $productModificationCostPrice = 0;

                $ingredientsInModification = $productModification->ProductModifications->Ingredients;
                foreach ($ingredientsInModification as $ingredientInModification) {
                    /** @var ProductModificationsIngredients $ingredientInModification */
                    $amountIngredient = $ingredientInModification->ingredient_amount;
                    $ingredient = $ingredientInModification->Ingredient;
                    /** @var Ingredients $ingredient */
                    $ingredientCurrentPrice = $ingredient->CurrentPrice();
                    $productModificationCostPrice += $amountIngredient * $ingredientCurrentPrice;
                }

                $orderCostPrice += $productModificationCostPrice;
            }

            if ($order->IsCancelled()) {
                $amountCancelled++;
            } else {

                $costPrice += $orderCostPrice;

                $sum += $order->order_amount;

                if ($clientInfo->typePayment[0] === true) {
                    $sumBank +=  $order->order_amount;
                } else {
                    $sumCash += $order->order_amount;
                }
            }

        }

        return (object)[
            'countOrder' => $ordersCount,
            'sum' => number_format($sum, 2, ',', "'"),
            'sumBank' => number_format($sumBank, 2, ',', "'"),
            'sumCash' => number_format($sumCash, 2, ',', "'"),
            'averageCheck' => number_format(($ordersCount !== 0 ? ($sum / $ordersCount) : 0), 2, ',', "'"),
            'amountCancelled' => $amountCancelled,
            'costPrice' => number_format($costPrice, 2, ',', "'"),
            'profit' => number_format(($sum - $costPrice), 2, ',', "'"),
        ];
    }
}
