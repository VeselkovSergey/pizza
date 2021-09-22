<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductModifications extends Model
{
    protected $fillable = [
        'id_product',
        'id_modifications',
        'selling_price',
    ];
}
