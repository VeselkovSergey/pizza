<?php

namespace App\Models;

/**
 * @property integer id
 * @property integer courier_id
 * @property integer status_id
 * @property integer payment_id
 * @property string products_raw_data
 * @property string all_information_raw_data
 * @property string telegram_message_id
 * @property string client_raw_data
 * @property integer order_amount
 * @property integer total_order_amount
 * @property ProductsModificationsInOrders ProductsModifications
 */
class Orders extends BaseModel
{
    protected $fillable = [
        'user_id',
        'status_id',
        'client_raw_data',
        'products_raw_data',
        'all_information_raw_data',
        'courier_id',
        'payment_id',
        'telegram_message_id',
        'order_amount',
        'total_order_amount',
    ];

    const STATUS = [
        0 => 'Клиент создал заказ',
        1 => 'Новый заказ',
        2 => 'Менеджер взял в работу',
        3 => 'Передан на кухню',
        4 => 'Повар взял в работу',
        5 => 'Приготовлен',
        6 => 'Передан курьеру',
        7 => 'Доставлен',
        8 => 'Выполнен',
        9 => 'Отказ',
    ];

    const STATUS_TEXT = [
        'clientCreateOrder' => 0,
        'newOrder' => 1,
        'managerProcesses' => 2,
        'kitchen' => 3,
        'chefProcesses' => 4,
        'cooked' => 5,
        'courier' => 6,
        'delivered' => 7,
        'completed' => 8,
        'cancelled' => 9,
    ];

    public function User()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function Courier()
    {
        return $this->hasOne(User::class, 'id', 'courier_id');
    }

    public function ProductsModifications()
    {
        return $this->hasMany(ProductsModificationsInOrders::class, 'order_id', 'id');
    }

    public function CurrentStatus()
    {
        return $this->hasOne(OrdersStatusLogs::class, 'order_id', 'id')->orderBy('id', 'DESC')->first();
    }

    public function Statuses()
    {
        return $this->hasMany(OrdersStatusLogs::class, 'order_id', 'id');
    }

    public function Creator()
    {
        return $this->hasOne(OrdersStatusLogs::class, 'order_id', 'id')->first();
    }

    public function IsCancelled()
    {
        if ($this->status_id === self::STATUS_TEXT['cancelled']) {
            return true;
        }
        return false;
    }

    private static function TimeBetweenStatuses($orderId, $oldStatus, $newStatus)
    {
        $oldStatusLog = OrdersStatusLogs::where('order_id', $orderId)
            ->where('new_status_id', $oldStatus)
            ->first();

        $newStatusLog = OrdersStatusLogs::where('order_id', $orderId)
            ->where('new_status_id', $newStatus)
            ->first();

        if (!$oldStatusLog || !$newStatusLog) {
            return '-';
        }

        return date_diff($oldStatusLog->created_at, $newStatusLog->created_at)->format('%H:%I:%S');
    }

    public function TimeManagerProcesses()
    {
        return self::TimeBetweenStatuses($this->id, Orders::STATUS_TEXT['newOrder'], Orders::STATUS_TEXT['managerProcesses']);
    }

    public function TimeTransferOnKitchen()
    {
        return self::TimeBetweenStatuses($this->id, Orders::STATUS_TEXT['managerProcesses'], Orders::STATUS_TEXT['kitchen']);
    }

    public function TimeCooked()
    {
        return self::TimeBetweenStatuses($this->id, Orders::STATUS_TEXT['kitchen'], Orders::STATUS_TEXT['cooked']);
    }

    public function TimeCourier()
    {
        return self::TimeBetweenStatuses($this->id, Orders::STATUS_TEXT['cooked'], Orders::STATUS_TEXT['courier']);
    }

    public function TimeDelivered()
    {
        return self::TimeBetweenStatuses($this->id, Orders::STATUS_TEXT['courier'], Orders::STATUS_TEXT['delivered']);
    }

    public function TimeCompleted()
    {
        return self::TimeBetweenStatuses($this->id, Orders::STATUS_TEXT['delivered'], Orders::STATUS_TEXT['completed']);
    }
}
