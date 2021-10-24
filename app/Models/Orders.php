<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    protected $fillable = [
        'user_id',
        'status_id',
        'client_raw_data',
        'products_raw_data',
        'all_information_raw_data',
    ];

    const STATUS = [
        1 => 'Новый заказ',
        2 => 'Менеджер взял в работу',
        3 => 'Передан на кухню',
        4 => 'Приготовлен',
        5 => 'Передан курьеру',
        6 => 'Доставлен',
        7 => 'Выполнен',
        8 => 'Отказ',
    ];

    const STATUS_TEXT = [
        'newOrder' => 1,
        'processing' => 2,
        'kitchen' => 3,
        'cooked' => 4,
        'courier' => 5,
        'delivered' => 6,
        'completed' => 7,
        'cancelled' => 8,
    ];

    public function ProductsModifications()
    {
        return $this->hasMany(ProductsModificationsInOrders::class, 'order_id', 'id');
    }

    public function Statuses()
    {
        return $this->hasMany(OrdersStatusLogs::class, 'order_id', 'id');
    }
}
