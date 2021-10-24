<?php


namespace App\Http\Controllers\ARM;

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
        $orders = OrdersController::AllOrders();
        return view('arm.management.orders.index', [
            'orders' => $orders
        ]);
    }

    public function Order()
    {
        $orderId = request()->orderId;
        $order = OrdersController::Order($orderId);
        $productsModificationsInOrder = OrdersController::OrderProductsModifications($order);
        $orderStatuses = OrdersController::Statuses($order);
        $clientInfo = json_decode($order->client_raw_data);
        return view('arm.management.orders.order', [
            'order' => $order,
            'orderStatuses' => $orderStatuses,
            'productsModificationsInOrder' => $productsModificationsInOrder,
            'clientInfo' => $clientInfo,
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

    public function ChangeStatusOrderToProcessing()
    {
        $orderId = request()->orderId;
        $order = OrdersController::Order($orderId);
        return OrdersController::ChangeStatus($order, Orders::STATUS_TEXT['processing']);
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
}
