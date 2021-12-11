<?php


namespace App\Http\Controllers\ARM;

use App\Helpers\ResultGenerate;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Orders\OrdersController;
use App\Http\Controllers\Products\ProductsController;
use App\Models\Orders;
use App\Models\User;
use App\Services\Telegram\Telegram;

class ManagerARMController extends Controller
{
    public function Index()
    {
        return view('arm.management.index');
    }

    public function Orders()
    {
        $requiredDate = (request()->get('required-date') === null) ? date('Y-m-d', time()) : request()->get('required-date');
        $allOrders = !empty(request()->get('all-orders'));
        $orders = OrdersController::OrdersByDate($requiredDate, $allOrders);
        return view('arm.management.orders.index', [
            'orders' => $orders,
            'requiredDate' => $requiredDate,
        ]);
    }

    public function Order()
    {
        $orderId = request()->orderId;
        $order = OrdersController::Order($orderId);
        if ($order->status_id === Orders::STATUS_TEXT['newOrder']) {
            self::ChangeStatusOrderToManagerProcesses($order);
        }
        $productsModificationsInOrder = OrdersController::OrderProductsModifications($order);
        $orderStatuses = OrdersController::OrderStatuses($order);
        $clientInfo = json_decode($order->client_raw_data);
        $rawData = json_decode($order->all_information_raw_data);

        $allProducts = new ProductsController();
        $allProducts = $allProducts->GetAllProducts();

        $couriers = User::where('role_id', 111)->get();
        return view('arm.management.orders.order', [
            'order' => $order,
            'orderStatuses' => $orderStatuses,
            'productsModificationsInOrder' => $productsModificationsInOrder,
            'clientInfo' => $clientInfo,
            'rawData' => $rawData,
            'couriers' => $couriers,
            'allProducts' => json_decode($allProducts),
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
        $order = OrdersController::Order($orderId);
        $clientInfo = json_decode($order->client_raw_data);
        $productsModificationsInOrder = OrdersController::OrderProductsModifications($order);
        $rawData = json_decode($order->all_information_raw_data);

        return view('arm.management.orders.invoice.invoice', compact('order', 'clientInfo', 'productsModificationsInOrder', 'rawData'));
    }

    public function InvoiceChefPage()
    {
        $orderId = request()->orderId;
        $order = OrdersController::Order($orderId);
        $productsModificationsInOrder = OrdersController::OrderProductsModifications($order);

        return view('arm.management.orders.invoice.invoice-chef', compact('productsModificationsInOrder'));
    }

    public function ChangeStatusOrderToNewOrder()
    {
        $orderId = request()->orderId;
        $order = OrdersController::Order($orderId);
        return OrdersController::ChangeStatus($order, Orders::STATUS_TEXT['newOrder']);
    }

    public function ChangeStatusOrderToManagerProcessesRequest()
    {
        $orderId = request()->orderId;
        $order = OrdersController::Order($orderId);
        return self::ChangeStatusOrderToManagerProcesses($order);
    }

    public static function ChangeStatusOrderToManagerProcesses(Orders $order): bool
    {
        return OrdersController::ChangeStatus($order, Orders::STATUS_TEXT['managerProcesses']);
    }

    public function TransferOrderToKitchen()
    {
        $orderId = request()->orderId;
        $order = OrdersController::Order($orderId);
        return OrdersController::ChangeStatus($order, Orders::STATUS_TEXT['kitchen']);
    }

    public function TransferOrderToDelivery()
    {
        $orderId = request()->orderId;
        $courierId = (int)request()->courierId;

        if ($courierId !== 0) {
            $user = User::find($courierId);
            $courierId = $user->id;
        }

        $order = OrdersController::Order($orderId);
        $order->courier_id = $courierId;
        $order->save();

        if ($courierId !== 0) {
//            $this->SendTelegram($user, $order);
        }
        return OrdersController::ChangeStatus($order, Orders::STATUS_TEXT['courier']);
    }

    private function SendTelegram(User $user, Orders $order)
    {
        $chatId = $user->telegram_chat_id;

        $clientData = json_decode($order->client_raw_data);

        $orderSum = json_decode($order->all_information_raw_data)->orderSum;

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
        $message .= '<i>Итого:</i> ' . $orderSum . ' ₽' . PHP_EOL;

        $telegram = new Telegram();
        $telegram->sendMessage($message, '-1001606894202');
        if (!empty($chatId)) {
            $telegram->addButton('Доставлен', 'Delivered');
            $telegram->addButton('Отказ', 'Refused');
            $telegram->sendMessage($message, $chatId);
        }
    }

    public function ChangeStatusOrderToCompleted()
    {
        $orderId = request()->orderId;
        $order = OrdersController::Order($orderId);
        return OrdersController::ChangeStatus($order, Orders::STATUS_TEXT['completed']);
    }

    public function ChangeStatusOrderToCanceled()
    {
        $orderId = request()->orderId;
        $order = OrdersController::Order($orderId);
        return OrdersController::ChangeStatus($order, Orders::STATUS_TEXT['cancelled']);
    }

    public function ChangeStatusOrderToDelivered()
    {
        $orderId = request()->orderId;
        $order = OrdersController::Order($orderId);
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
}
