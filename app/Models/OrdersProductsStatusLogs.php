<?php

namespace App\Models;

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
