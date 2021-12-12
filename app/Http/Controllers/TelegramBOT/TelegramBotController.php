<?php


namespace App\Http\Controllers\TelegramBOT;


use App\Http\Controllers\Controller;
use App\Http\Controllers\Orders\OrdersController;
use App\Services\Telegram\Telegram;

class TelegramBotController extends Controller
{
    function Index()
    {
        $telegram = new Telegram('1114911874:AAFWbIL-e3yBb61RvwVs2A_FsqNsZteG8A0');

        switch ($telegram->incomingMessage()) {

            case '/start':

                // Несколько кнопок в ряд
                $telegram->addButton([
                    'Доставлен' => 'Delivered',
                    'Отказ' => 'Refused',
                ]);

                // текст сообщения
                $telegram->sendMessage('Тестовое сообщение!');
                break;

            case 'Delivered':
                // Одна кнопка

                $telegram->sendMessage('Отлично! Ты молодец!');
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

                $report = self::TodayReport();

                $message = '<b>Отчёт:</b>' . PHP_EOL;
                $message .= 'Кол-во заказов: ' . $report->countOrder . '(отказов: ' . $report->amountCancelled . ')' . PHP_EOL;
                $message .= 'Сумма: ' . $report->sum . PHP_EOL;
                $message .= 'Сумма банк: ' . $report->sumBank . PHP_EOL;
                $message .= 'Сумма нал: ' . $report->sumCash . PHP_EOL;
                $message .= 'Средний чек: ' . $report->averageCheck . PHP_EOL;
                $telegram->sendMessage($message);
                break;
        }
    }

    public function TodayReportRequest()
    {
        return self::TodayReport();
    }

    public static function TodayReport()
    {
        $orders = OrdersController::OrdersByDate(date('Y-m-d', time()), true);
        $ordersCount = $orders->count();

        $sum = 0;
        $sumCash = 0;
        $sumBank = 0;
        $amountCancelled = 0;

        foreach ($orders as $order) {
            $rawData = json_decode($order->all_information_raw_data);
            $clientInfo = json_decode($order->client_raw_data);

            $sum += $rawData->orderSum;

            if ($clientInfo->typePayment[0] === true) {
                $sumBank +=  $rawData->orderSum;
            } else {
                $sumCash += $rawData->orderSum;
            }

            if ($order->IsCancelled()) {
                $amountCancelled++;
            }

        }

        return (object)[
            'countOrder' => $ordersCount,
            'sum' => $sum,
            'sumBank' => $sumBank,
            'sumCash' => $sumCash,
            'averageCheck' => ($sum / $ordersCount),
            'amountCancelled' => $amountCancelled,
        ];
    }
}
