<?php

namespace App\Models;

/**
 * @property integer id
 * @property string title
 */
class Suppliers extends BaseModel
{
    protected $fillable = [
        'title',
    ];
}
