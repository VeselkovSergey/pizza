<?php


namespace App\Http\Controllers\ARM;

use App\Helpers\ResultGenerate;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Orders\OrdersController;
use App\Models\Orders;

class ManagerARMController extends Controller
{
    public function Index()
    {
        return view('arm.management.index');
    }

    public function Orders()
    {
        $orders = OrdersController::TodayOrders();
//        $orders = OrdersController::AllOrders();
        return view('arm.management.orders.index', [
            'orders' => $orders
        ]);
    }

    public function Order()
    {
        $orderId = request()->orderId;
        $order = OrdersController::Order($orderId);
        $productsModificationsInOrder = OrdersController::OrderProductsModifications($order);
        $orderStatuses = OrdersController::OrderStatuses($order);
        $clientInfo = json_decode($order->client_raw_data);
        $rawData = json_decode($order->all_information_raw_data);
        return view('arm.management.orders.order', [
            'order' => $order,
            'orderStatuses' => $orderStatuses,
            'productsModificationsInOrder' => $productsModificationsInOrder,
            'clientInfo' => $clientInfo,
            'rawData' => $rawData,
        ]);
    }

    public function SearchByPhone()
    {
        $phone = request()->phone;
        $orders = OrdersController::SearchByPhone($phone);
        return ['orders' => $orders, 'statuses' => Orders::STATUS];
    }

    public function ChangeStatusOrderToNewOrder()
    {
        $orderId = request()->orderId;
        $order = OrdersController::Order($orderId);
        return OrdersController::ChangeStatus($order, Orders::STATUS_TEXT['newOrder']);
    }

    public function ChangeStatusOrderToManagerProcesses()
    {
        $orderId = request()->orderId;
        $order = OrdersController::Order($orderId);
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
        $order = OrdersController::Order($orderId);
        return OrdersController::ChangeStatus($order, Orders::STATUS_TEXT['courier']);
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
