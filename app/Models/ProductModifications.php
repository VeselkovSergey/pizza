<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductModifications extends Model
{
    protected $fillable = [
        'product_id',
        'modification_id',
        'selling_price',
    ];
}
