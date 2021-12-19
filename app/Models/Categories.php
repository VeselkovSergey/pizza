<?php

namespace App\Models;

/**
 * @property integer id
 * @property string title
 */
class Categories extends BaseModel
{
    protected $fillable = [
        'title',
    ];
}
