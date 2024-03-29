<?php

namespace App\Http\Controllers\ARM;

use App\Http\Controllers\Orders\OrdersController;
use App\Models\Orders;

class CourierARMController
{
    public static function ChangeStatusOrderToDelivered(int $messageId): bool
    {
        $order = Orders::ByCourierMessageTelegram($messageId);
        if ($order) {
            return OrdersController::ChangeStatus($order, Orders::STATUS_TEXT['delivered'], $order->courier_id);
        }
        return false;
    }

    public static function ChangeStatusOrderToCanceled(int $messageId): bool
    {
        $order = Orders::ByCourierMessageTelegram($messageId);
        if ($order) {
            return OrdersController::ChangeStatus($order, Orders::STATUS_TEXT['cancelled'], $order->courier_id);
        }
        return false;
    }

    public static function CourierError(int $messageId): bool
    {
        $order = Orders::ByCourierMessageTelegram($messageId);
        if ($order) {
            return OrdersController::ChangeStatus($order, Orders::STATUS_TEXT['courier'], $order->courier_id);
        }
        return false;
    }
}
