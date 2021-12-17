<?php

namespace App\Models;

/**
 * @property string title
 * @property integer type_id
 * @property string value
 * @property TypesModifications Type
 */
class Modifications extends BaseModel
{
    protected $fillable = [
        'title',
        'type_id',
        'value',
    ];

    public function Type()
    {
        return $this->hasOne(TypesModifications::class, 'id', 'type_id');
    }
}
