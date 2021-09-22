<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Modifications extends Model
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
