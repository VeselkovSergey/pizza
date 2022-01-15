<?php


namespace App\Http\Controllers\ARM;

use App\Helpers\ResultGenerate;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Orders\OrdersController;
use App\Http\Controllers\Products\ProductsController;
use App\Models\Orders;
use App\Models\PromoCodes;
use App\Models\Supply;
use App\Models\User;
use App\Services\Pusher\NewOrderForKitchen;
use App\Services\Telegram\Telegram;

class ManagerARMController extends Controller
{
    public function Index()
    {
        return view('arm.management.index');
    }

    public function Orders()
    {
        $today = now()->format('Y-m-d');
        $allOrders = !empty(request()->get('all-orders'));
        $orders = Orders::ByDate($today, $today, $allOrders);
        $supplySum = Supply::SuppliesSumByDate($today, $today);
        return view('arm.management.orders.index', compact('orders', 'supplySum'));
    }

    public function Order()
    {
        $orderId = request()->orderId;
        $order = Orders::find($orderId);
        if ($order->status_id === Orders::STATUS_TEXT['newOrder'] && !auth()->user()->IsAdmin()) {
            self::ChangeStatusOrderToManagerProcesses($order);
        }
        $productsModificationsInOrder = $order->ProductsModifications;
        $orderStatuses = $order->Statuses;
        $clientInfo = json_decode($order->client_raw_data);
        $rawData = json_decode($order->all_information_raw_data);
        $promoCode = null;
        if ($clientInfo->clientPromoCode) {
            $promoCode = PromoCodes::where('title', $clientInfo->clientPromoCode)->first();
            if ($promoCode) {
                $promoCode->conditions = json_decode($promoCode->conditions);
            }
        }

        $allProducts = ProductsController::GetAllProducts();

        $couriers = User::Couriers();
        return view('arm.management.orders.order', [
            'order' => $order,
            'orderStatuses' => $orderStatuses,
            'productsModificationsInOrder' => $productsModificationsInOrder,
            'clientInfo' => $clientInfo,
            'rawData' => $rawData,
            'couriers' => $couriers,
            'allProducts' => $allProducts,
            'promoCode' => $promoCode,
        ]);
    }

    public function SearchByPhone()
    {
        $phone = request()->phone;
        $orders = OrdersController::SearchByPhone($phone);
        return ['orders' => $orders, 'statuses' => Orders::STATUS];
    }

    public function InvoicePage()
    {
        $orderId = request()->orderId;
        $order = Orders::find($orderId);
        $clientInfo = json_decode($order->client_raw_data);
        $productsModificationsInOrder = $order->ProductsModifications;
        $rawData = json_decode($order->all_information_raw_data);

        return view('arm.management.orders.invoice.invoice', compact('order', 'clientInfo', 'productsModificationsInOrder', 'rawData'));
    }

    public function InvoiceChefPage()
    {
        $orderId = request()->orderId;
        $order = Orders::find($orderId);
        $productsModificationsInOrder = $order->ProductsModifications;

        return view('arm.management.orders.invoice.invoice-chef', compact('order', 'productsModificationsInOrder'));
    }

    public function ChangeStatusOrderToNewOrder()
    {
        $orderId = request()->orderId;
        $order = Orders::find($orderId);
        return OrdersController::ChangeStatus($order, Orders::STATUS_TEXT['newOrder']);
    }

    public function ChangeStatusOrderToManagerProcessesRequest()
    {
        $orderId = request()->orderId;
        $order = Orders::find($orderId);
        return self::ChangeStatusOrderToManagerProcesses($order);
    }

    public static function ChangeStatusOrderToManagerProcesses(Orders $order): bool
    {
        return OrdersController::ChangeStatus($order, Orders::STATUS_TEXT['managerProcesses']);
    }

    public function TransferOrderToKitchen()
    {
        $orderId = request()->orderId;
        $order = Orders::find($orderId);
        OrdersController::ChangeStatus($order, Orders::STATUS_TEXT['kitchen']);
        event(new NewOrderForKitchen($order->id));
        return true;

    }

    public function TransferOrderToDelivery()
    {
        $orderId = request()->orderId;
        $courierId = (int)request()->courierId;

        $order = Orders::find($orderId);

        if (empty($order->courier_id)) {
            OrdersController::ChangeStatus($order, Orders::STATUS_TEXT['courier']);
        }

        self::ChangeCourierInOrder($courierId, $order);

        return true;
    }

    public function ChangeCourierInOrderRequest()
    {
        $this->TransferOrderToDelivery();
    }

    public static function ChangeCourierInOrder($courierId, Orders $order)
    {
        if ($courierId !== 0) {
            $user = User::find($courierId);
            $courierId = $user->id;
        }

        if (isset($order->courier_id) && $order->courier_id !== 0 && isset($order->courier_telegram_message_id)) {
            $telegram = new Telegram();
            $telegram->deleteMessage($order->Courier->telegram_chat_id, $order->courier_telegram_message_id);
        }

        if ($courierId !== 0) {
            $result = self::SendTelegram($user, $order);
            $result = json_decode($result);
            if ($result && $result->ok === true) {
                $order->courier_telegram_message_id = $result->result->message_id;
            }
        }

        $order->courier_id = $courierId;
        $order->save();
    }

    private static function SendTelegram(User $user, Orders $order)
    {
        $chatId = $user->telegram_chat_id;

        if (!empty($chatId)) {

            $clientData = json_decode($order->client_raw_data);

            $clientName = $clientData->clientName;
            $clientPhone = $clientData->clientPhone;
            $typePayment = ($clientData->typePayment[0] === true ? 'Карта' : 'Наличные') ;
            $clientAddressDelivery = $clientData->clientAddressDelivery;
            $clientComment = $clientData->clientComment;

            $message = '<b>Курьер: </b>' . PHP_EOL;
            $message .= '<i>Имя:</i> ' . $user->name . PHP_EOL;
            $message .= '<i>Телефон:</i> +' . $user->phone . PHP_EOL;

            $message .= '<b>Клиент:</b>' . PHP_EOL;
            $message .= '<i>Имя:</i> ' . $clientName . PHP_EOL;
            $message .= '<i>Телефон:</i> +' . $clientPhone . PHP_EOL;
            $message .= '<i>Оплата:</i> ' . $typePayment . PHP_EOL;
            $message .= '<i>Адрес:</i> ' . $clientAddressDelivery . PHP_EOL;
            $message .= '<i>Комментарий:</i> ' . $clientComment . PHP_EOL;
            $message .= '<i>Итого:</i> ' . $order->order_amount . ' ₽' . PHP_EOL;

            $telegram = new Telegram();

            $telegram->addButton('Доставлен', 'Delivered');
            $telegram->addButton('Отказ', 'Refused');
            $telegram->addButton('Ошибка', 'Error');
            return $telegram->sendMessage($message, $chatId);
        }
        return false;
    }

    public function ChangeStatusOrderToCompleted()
    {
        $orderId = request()->orderId;
        $order = Orders::find($orderId);
        return OrdersController::ChangeStatus($order, Orders::STATUS_TEXT['completed']);
    }

    public function ChangeStatusOrderToCanceled()
    {
        $orderId = request()->orderId;
        $order = Orders::find($orderId);
        return OrdersController::ChangeStatus($order, Orders::STATUS_TEXT['cancelled']);
    }

    public function ChangeStatusOrderToDelivered()
    {
        $orderId = request()->orderId;
        $order = Orders::find($orderId);
        return OrdersController::ChangeStatus($order, Orders::STATUS_TEXT['delivered']);
    }

    public function CheckOrderStatusChange()
    {
        $orderStatuses = request()->post('orderStatuses');
        $orderStatuses = json_decode(json_decode($orderStatuses));
        if (empty($orderStatuses)) {
            return ResultGenerate::Error();
        }
        $orders = new Orders();
        $orders = $orders->query()->select(['id as orderId', 'status_id as newStatus']);
        $orders = $orders->whereNotIn('status_id', [8, 9]);
        foreach ($orderStatuses as $orderStatus) {
            $orders = $orders->orWhere('id', $orderStatus->orderId);
            $orders = $orders->where('status_id', '!=', $orderStatus->oldStatus);  #todo изменить = на !=
        }

        return ResultGenerate::Success('', $orders->get());
    }

    public function OrderChangePaymentType()
    {
        $orderId = request()->post('orderId');
        $paymentType = request()->post('typePayment');
        $paymentType = json_decode($paymentType);
        $order = Orders::find($orderId);
        OrdersController::ChangePaymentType($order, $paymentType);
        return ResultGenerate::Success();
    }
}
