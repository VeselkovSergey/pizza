<?php

namespace App\Models;

/**
 * @property integer id
 * @property integer user_id
 * @property integer promo_code_id
 * @property integer order_id
 */
class PromoCodesUsersUsed extends BaseModel
{
    protected $table = 'promo_codes_users_used';

    protected $fillable = [
        'user_id',
        'promo_code_id',
        'order_id',
    ];
}