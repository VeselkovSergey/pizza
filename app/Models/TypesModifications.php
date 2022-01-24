<?php

namespace App\Models;

/**
 * @property integer id
 * @property string title
 * @property string value_unit
 */
class TypesModifications extends BaseModel
{
    protected $fillable = [
        'title',
        'value_unit',
    ];
}
