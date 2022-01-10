<?php

namespace App\Models;

/**
 * @property ProductModifications ProductModifications
 * @property OrdersProductsStatusLogs Statuses
 * @property integer product_modification_amount
 * @property integer id
 * @property integer order_id
 * @property integer status_id
 * @property integer product_modification_id
 */
class ProductsModificationsInOrders extends BaseModel
{

    protected $fillable = [
        'order_id',
        'status_id',
        'product_modification_id',
        'product_modification_amount',
    ];

    const STATUS = [
        0 => 'Создана позиция',
        1 => 'Не начинали готовить',
        2 => 'В процессе приготовления',
        3 => 'Приготовлен',
    ];

    const STATUS_TEXT = [
        'create' => 0,
        'new' => 1,
        'chefProcesses' => 2,
        'cooked' => 3,
    ];

    public function ProductModifications()
    {
        return $this->hasOne(ProductModifications::class, 'id', 'product_modification_id');
    }

    public function Statuses()
    {
        return $this->hasMany(OrdersProductsStatusLogs::class, 'order_product_id', 'id');
    }
}
