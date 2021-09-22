<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductProperties extends Model
{
    protected $fillable = [
        'id_product',
        'id_properties',
    ];
}
