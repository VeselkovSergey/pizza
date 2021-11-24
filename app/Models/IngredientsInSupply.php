<?php

namespace App\Models;

/**
 * @property integer supply_id
 * @property integer ingredient_id
 * @property float amount_ingredient
 * @property float price_ingredient
 */
class IngredientsInSupply extends BaseModel
{
    protected $table = 'ingredients_in_supply';
    protected $fillable = [
        'supply_id',
        'ingredient_id',
        'amount_ingredient',
        'price_ingredient',
    ];
}
