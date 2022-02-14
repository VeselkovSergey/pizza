<?php

namespace App\Models;

/**
 * @property integer id
 * @property integer user_id
 * @property integer promo_code_id
 * @property integer order_id
 *
 * @property PromoCodes PromoCode
 */
class PromoCodesUsersUsed extends BaseModel
{
    protected $table = 'promo_codes_users_used';

    protected $fillable = [
        'user_id',
        'promo_code_id',
        'order_id',
    ];

    public function PromoCode()
    {
        return $this->hasOne(PromoCodes::class, 'id', 'promo_code_id');
    }
}