<?php

namespace App\Models;

/**
 * @property integer id
 * @property integer supply_id
 * @property integer ingredient_id
 * @property float amount_ingredient
 * @property float price_ingredient
 * @property Ingredients Ingredient
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

    public $timestamps = false;

    public function Ingredient()
    {
        $ingredient = $this->hasOne(Ingredients::class, 'id', 'ingredient_id');
        if ($ingredient) {
            return $ingredient;
        } else {
            return (object) [
                'id' => $this->ingredient_id,
                'title' => 'Удалил'
            ];
        }
    }
}
