<?php

namespace App\Models;

/**
 * @property integer id
 * @property integer order_id
 * @property integer amount
 * @property integer status
 * @property integer type
 * @property string bankResponse
 * @property string bankOrderId
 * @property string link
 */
class Payments extends BaseModel
{
    protected $fillable = [
        'order_id',
        'amount',
        'status',
        'type',
        'bankResponse',
        'bankOrderId',
        'link',
    ];

    const STATUS = [
        0 => 'Новый',
        1 => 'Оплачен',
        2 => 'Ошибка',
    ];

    const STATUS_TEXT = [
        'newPayment' => 0,
        'paid' => 1,
        'error' => 2,
    ];
    const TYPE = [
        0 => 'Наличный',
        1 => 'Безналичный',
    ];

    const TYPE_TEXT = [
        'cash' => 0,
        'bank' => 1,
    ];
}