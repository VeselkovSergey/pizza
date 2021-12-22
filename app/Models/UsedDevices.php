<?php

namespace App\Models;

/**
 * @property integer id
 * @property string order_id
 * @property string device_info
 */
class UsedDevices extends BaseModel
{
    protected $fillable = [
        'device_info',
        'order_id',
    ];
}
