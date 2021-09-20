<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertiesForProducts extends Model
{
    protected $fillable = [
        'title',
        'type_id',
        'value',
    ];

    public function Type()
    {
        return $this->hasOne(TypesForProperties::class, 'id', 'type_id');
    }
}
