<?php

namespace App\Models;

/**
 * @property integer product_modification_id
 * @property integer ingredient_id
 * @property float ingredient_amount
 * @property integer visible
 * @property Ingredients Ingredient
 */
class ProductModificationsIngredients extends BaseModel
{
    protected $fillable = [
        'product_modification_id',
        'ingredient_id',
        'ingredient_amount',
        'visible',
    ];

    public function Ingredient()
    {
        return $this->hasOne(Ingredients::class, 'id', 'ingredient_id');
    }
}
