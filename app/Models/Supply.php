<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supply extends Model
{
    protected $table = 'supply';
    protected $fillable = [
        'supplier_id',
        'supply_date',
        'payment_type',
    ];
}
