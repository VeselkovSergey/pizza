<?php

namespace App\Models;

/**
 * @property integer id
 * @property integer courier_id
 * @property integer status_id
 * @property integer payment_id
 * @property string products_raw_data
 * @property string all_information_raw_data
 * @property string courier_telegram_message_id
 * @property string order_telegram_message_id
 * @property string client_raw_data
 * @property integer order_amount
 * @property integer total_order_amount
 * @property string geo_yandex
 * @property string created_at
 * @property string updated_at
 *
 * @property ProductsModificationsInOrders ProductsModifications
 * @property OrdersStatusLogs LatestStatus
 * @property User Courier
 * @property User User
 * @property OrdersStatusLogs Statuses
 * @method Orders find($orderId)
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
        'courier_telegram_message_id',
        'order_telegram_message_id',
        'order_amount',
        'total_order_amount',
        'geo_yandex',
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

    public function CurrentStatusText()
    {
        return self::STATUS[$this->status_id];
    }

    public function Statuses()
    {
        return $this->hasMany(OrdersStatusLogs::class, 'order_id', 'id');
    }

    public function LatestStatus()
    {
        return $this->hasOne(OrdersStatusLogs::class, 'order_id', 'id')->limit(1)->latest('id');
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

    public function IsCompleted()
    {
        if ($this->status_id === self::STATUS_TEXT['completed']) {
            return true;
        }
        return false;
    }

    public static function AllOrders($direction = 'DESC')
    {
        return self::orderBy('id', $direction)->get();
    }

    public static function ByDate($startDate, $endDate, $allOrdersByDate = false, $direction = 'DESC')
    {
        $startDate = strtotime($startDate);
        $endDate = strtotime($endDate);
        $startDate = date('Y-m-d 00:00:00', $startDate);
        $endDate = date('Y-m-d 23:59:59', $endDate);
        $orders = new self();
        $orders = $orders->where('created_at', '>=', $startDate);
        $orders = $orders->where('created_at', '<=', $endDate);
        if (!$allOrdersByDate) {
            $orders = $orders->whereNotIn('status_id', [self::STATUS_TEXT['completed'], self::STATUS_TEXT['cancelled']]);
        }
        $orders = $orders->orderBy('id', $direction);
        return $orders->get();
    }

    public static function KitchenStatusOnly()
    {
        return self::where('status_id', self::STATUS_TEXT['kitchen'])->get();
    }

    /**
     * @param int $messageId
     * @return Orders
     */
    public static function ByCourierMessageTelegram(int $messageId)
    {
        return self::where('courier_telegram_message_id', $messageId)->first();
    }

    public static function TimeBetweenStatuses($orderId, $oldStatus, $newStatus)
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
        return self::TimeBetweenStatuses($this->id, self::STATUS_TEXT['newOrder'], self::STATUS_TEXT['managerProcesses']);
    }

    public function TimeTransferOnKitchen()
    {
        return self::TimeBetweenStatuses($this->id, self::STATUS_TEXT['managerProcesses'], self::STATUS_TEXT['kitchen']);
    }

    public function TimeCooked()
    {
        return self::TimeBetweenStatuses($this->id, self::STATUS_TEXT['kitchen'], self::STATUS_TEXT['cooked']);
    }

    public function TimeCourier()
    {
        return self::TimeBetweenStatuses($this->id, self::STATUS_TEXT['cooked'], self::STATUS_TEXT['courier']);
    }

    public function TimeDelivered()
    {
        return self::TimeBetweenStatuses($this->id, self::STATUS_TEXT['courier'], self::STATUS_TEXT['delivered']);
    }

    public function TimeCompleted()
    {
        return self::TimeBetweenStatuses($this->id, self::STATUS_TEXT['delivered'], self::STATUS_TEXT['completed']);
    }
}
