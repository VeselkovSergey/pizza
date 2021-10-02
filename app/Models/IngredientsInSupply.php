<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IngredientsInSupply extends Model
{
    protected $table = 'ingredients_in_supply';
    protected $fillable = [
        'supply_id',
        'ingredient_id',
        'amount_ingredient',
        'price_ingredient',
    ];
}
