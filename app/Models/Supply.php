<?php

namespace App\Models;

class Supply extends BaseModel
{
    protected $table = 'supply';
    protected $fillable = [
        'supplier_id',
        'supply_date',
        'payment_type',
    ];
}
