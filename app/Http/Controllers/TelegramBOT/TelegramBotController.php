<?php


namespace App\Http\Controllers\TelegramBOT;


use App\Http\Controllers\ARM\CourierARMController;
use App\Http\Controllers\ARM\ManagerARMController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Orders\OrdersController;
use App\Models\Ingredients;
use App\Models\ProductModificationsIngredients;
use App\Models\ProductsModificationsInOrders;
use App\Services\Telegram\Telegram;

class TelegramBotController extends Controller
{
    // 267236435
    function Index()
    {
        try {
            $telegram = new Telegram('1114911874:AAFWbIL-e3yBb61RvwVs2A_FsqNsZteG8A0');

            $command = $telegram->incomingMessage();

            switch ($command) {

                case '/start':
                    // текст сообщения
                    $telegram->sendMessage('BROпицца!');
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

                case '/chatId':
                    $telegram->sendMessage('Твой id чата: ' . $telegram->ChatId());
                    break;

                case '/all':
                    $message = '<b>Команды:</b>' . PHP_EOL;
                    $message .= 'Показать все команды: /all' . PHP_EOL;
                    $message .= 'Получить ID чата /chatId' . PHP_EOL;
                    $telegram->sendMessage($message);
                    break;

                case '/todayReport':
                case '/yesterdayReport':
                case '/weekReport':
                case '/lastWeekReport':
                case '/monthReport':
                case '/lastMonthReport':
                case '/fullReport':

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

    public static function TodayReportRequest()
    {
        return self::Report(now());
    }

    public static function YesterdayReportRequest()
    {
        return self::Report(date('Y-m-d',strtotime(now() . '-1 days')));
    }

    public static function WeekReportRequest()
    {
        $startDate = date('Y-m-d',strtotime('monday this week'));
        $endDate = date('Y-m-d',strtotime('sunday this week'));
        return self::Report($startDate, $endDate);
    }

    public static function LastWeekReportRequest()
    {
        $startDate = date('Y-m-d',strtotime('monday this week -1 week'));
        $endDate = date('Y-m-d',strtotime('sunday this week -1 week'));
        return self::Report($startDate, $endDate);
    }

    public static function MonthReportRequest()
    {
        $startDate = date('Y-m-01',strtotime(now()));
        $endDate = date('Y-m-t',strtotime(now()));
        return self::Report($startDate, $endDate);
    }

    public static function LastMonthReportRequest()
    {
        $startDate = date('Y-m-01',strtotime(now() . '-1 month'));
        $endDate = date('Y-m-t',strtotime(now() . '-1 month'));
        return self::Report($startDate, $endDate);
    }

    public static function ReportRequest()
    {
        return self::Report();
    }

    public static function Report($startDate = null, $endDate = null)
    {
        if ($startDate) {
            $endDate = $endDate ?: $startDate;
            $orders = OrdersController::OrdersByDate($startDate, $endDate, true);
        } else {
            $orders = OrdersController::AllOrders('ASC');
        }

        $ordersCount = $orders->count();

        $sum = 0;
        $sumCash = 0;
        $sumBank = 0;
        $amountCancelled = 0;
        $costPrice = 0;

        foreach ($orders as $order) {
            $clientInfo = json_decode($order->client_raw_data);

            $productsModifications = OrdersController::OrderProductsModifications($order);

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
