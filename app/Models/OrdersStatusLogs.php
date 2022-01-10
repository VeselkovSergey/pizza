<?php

namespace App\Models;

/**
 * @property integer id
 * @property integer order_id
 * @property integer old_status_id
 * @property integer new_status_id
 * @property integer user_id
 * @property User User
 */
class OrdersStatusLogs extends BaseModel
{
    protected $fillable = [
        'order_id',
        'old_status_id',
        'new_status_id',
        'user_id',
    ];

    public function User()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
