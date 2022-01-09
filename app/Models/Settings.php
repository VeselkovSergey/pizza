<?php

namespace App\Models;

/**
 * @property integer id
 * @property string key
 * @property string value
 */
class Settings extends BaseModel
{
    protected $fillable = [
        'key',
        'value',
    ];
}
