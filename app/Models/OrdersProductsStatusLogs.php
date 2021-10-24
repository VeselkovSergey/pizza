<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdersProductsStatusLogs extends Model
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
