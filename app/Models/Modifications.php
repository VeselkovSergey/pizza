<?php

namespace App\Models;

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
