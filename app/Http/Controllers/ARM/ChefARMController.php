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
        $orders = Orders::KitchenStatusOnly();
        return view('arm.chef.orders.index', [
            'orders' => $orders
        ]);
    }

    public function OrdersKitchenInterface()
    {
        $orders = Orders::KitchenStatusOnly();
        return view('arm.chef.orders.kitchen-interface', [
            'orders' => $orders
        ]);
    }

    public function Order()
    {
        $orderId = request()->orderId;
        $order = Orders::find($orderId);
        $productsModificationsInOrder = $order->ProductsModifications;
        return view('arm.chef.orders.order', [
            'order' => $order,
            'productsModificationsInOrder' => $productsModificationsInOrder,
        ]);
    }

    public function ChangeStatusOrderToCooked()
    {
        $orderId = request()->orderId;
        $order = Orders::find($orderId);
        $productsInOrder = $order->ProductsModifications;
        foreach ($productsInOrder as $product) {
            OrdersController::OrderProductChangeStatus($product, ProductsModificationsInOrders::STATUS_TEXT['cooked']);
        }

        return OrdersController::ChangeStatus($order, Orders::STATUS_TEXT['cooked']);
    }

    public function ChangeStatusProductToNew()
    {
        $productIdInOrder = request()->productIdInOrder;
        $product = ProductsModificationsInOrders::find($productIdInOrder);
        return OrdersController::OrderProductChangeStatus($product, ProductsModificationsInOrders::STATUS_TEXT['new']);
    }

    public function ChangeStatusProductToChefProcesses()
    {
        $productIdInOrder = request()->productIdInOrder;
        $product = ProductsModificationsInOrders::find($productIdInOrder);
        return OrdersController::OrderProductChangeStatus($product, ProductsModificationsInOrders::STATUS_TEXT['chefProcesses']);
    }

    public function ChangeStatusProductToCooKed()
    {
        $productIdInOrder = request()->productIdInOrder;
        $product = ProductsModificationsInOrders::find($productIdInOrder);
        return OrdersController::OrderProductChangeStatus($product, ProductsModificationsInOrders::STATUS_TEXT['cooked']);
    }
}
