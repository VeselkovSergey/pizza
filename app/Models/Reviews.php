<?php

namespace App\Models;

/**
 * @property integer id
 * @property integer user_id
 * @property string type
 * @property string name
 * @property string phone
 * @property string text
 */
class Reviews extends BaseModel
{
    protected $guarded = [
        'id',
    ];
}
