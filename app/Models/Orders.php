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

    public function ProductsModifications()
    {
        return $this->hasMany(ProductsModificationsInOrders::class, 'order_id', 'id');
    }

    public function Statuses()
    {
        return $this->hasMany(OrdersStatusLogs::class, 'order_id', 'id');
    }
}
