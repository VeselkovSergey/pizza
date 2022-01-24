<?php

namespace App\Models;

/**
 * @property integer id
 * @property integer product_modification_id
 * @property integer ingredient_id
 * @property float ingredient_amount
 * @property integer visible
 *
 * @property Ingredients Ingredient
 * @property ProductModifications ProductModification
 */
class ProductModificationsIngredients extends BaseModel
{
    protected $guarded = ['id'];

    public function Ingredient()
    {
        return $this->hasOne(Ingredients::class, 'id', 'ingredient_id');
    }

    public function ProductModification()
    {
        return $this->hasOne(ProductModifications::class, 'id', 'product_modification_id');
    }
}
