<?php

namespace App\Models;

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
