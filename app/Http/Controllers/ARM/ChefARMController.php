<?php


namespace App\Http\Controllers\ARM;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Orders\OrdersController;
use App\Models\Orders;
use App\Models\ProductsModificationsInOrders;

class ChefARMController extends Controller
{
    public function Index()
    {
        return view('arm.chef.index');
    }

    public function Orders()
    {
        $orders = OrdersController::KitchenOrdersOnly();
        return view('arm.chef.orders.index', [
            'orders' => $orders
        ]);
    }

    public function Order()
    {
        $orderId = request()->orderId;
        $order = OrdersController::Order($orderId);
        $productsModificationsInOrder = OrdersController::OrderProductsModifications($order);
        return view('arm.chef.orders.order', [
            'order' => $order,
            'productsModificationsInOrder' => $productsModificationsInOrder,
        ]);
    }

    public function ChangeStatusOrderToCooked()
    {
        $orderId = request()->orderId;
        $order = OrdersController::Order($orderId);
        return OrdersController::ChangeStatus($order, Orders::STATUS_TEXT['cooked']);
    }

    public function ChangeStatusProductToNew()
    {
        $productIdInOrder = request()->productIdInOrder;
        $product = OrdersController::OrderProduct($productIdInOrder);
        return OrdersController::OrderProductChangeStatus($product, ProductsModificationsInOrders::STATUS_TEXT['new']);
    }

    public function ChangeStatusProductToChefProcesses()
    {
        $productIdInOrder = request()->productIdInOrder;
        $product = OrdersController::OrderProduct($productIdInOrder);
        return OrdersController::OrderProductChangeStatus($product, ProductsModificationsInOrders::STATUS_TEXT['chefProcesses']);
    }

    public function ChangeStatusProductToCooKed()
    {
        $productIdInOrder = request()->productIdInOrder;
        $product = OrdersController::OrderProduct($productIdInOrder);
        return OrdersController::OrderProductChangeStatus($product, ProductsModificationsInOrders::STATUS_TEXT['cooked']);
    }
}