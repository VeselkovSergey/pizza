<?php

namespace App\Models;

/**
 * @property integer order_count
 * @property integer last_order
 * @property integer user_id
 * @property integer is_send_sms
 *
 * @property User User
 */
class CustomerReturns extends BaseModel
{
    protected $guarded = ['id'];

    public function User()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
