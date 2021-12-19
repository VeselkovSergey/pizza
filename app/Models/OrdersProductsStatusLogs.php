<?php

namespace App\Models;

/**
 * @property integer id
 * @property integer order_product_id
 * @property integer old_status_id
 * @property integer new_status_id
 * @property integer user_id
 */
class OrdersProductsStatusLogs extends BaseModel
{
    protected $fillable = [
        'order_product_id',
        'old_status_id',
        'new_status_id',
        'user_id',
    ];

    public function User()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
