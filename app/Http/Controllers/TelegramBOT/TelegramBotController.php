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
                case '/fullReport':

                    if ($command === '/todayReport') {
                        $report = self::Report(true);
                        $text = '<b>Отчёт за сегодня:</b>';
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
                    $telegram->sendMessage($message);
                    break;
            }
        } catch (\Exception $e) {
            \Log::error($e);
        }
    }

    public function TodayReportRequest()
    {
        return self::Report(true);
    }

    public function ReportRequest()
    {
        return self::Report();
    }

    public static function Report($today = false)
    {
        if ($today) {
            $today = now()->format('Y-m-d');
            $orders = OrdersController::OrdersByDate($today, $today, true);
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
            'countOrder' => number_format($ordersCount, 2, ',', "'"),
            'sum' => number_format($sum, 2, ',', "'"),
            'sumBank' => number_format($sumBank, 2, ',', "'"),
            'sumCash' => number_format($sumCash, 2, ',', "'"),
            'averageCheck' => number_format(($ordersCount !== 0 ? ($sum / $ordersCount) : 0), 2, ',', "'"),
            'amountCancelled' => number_format($amountCancelled, 2, ',', "'"),
            'costPrice' => number_format($costPrice, 2, ',', "'"),
        ];
    }
}
