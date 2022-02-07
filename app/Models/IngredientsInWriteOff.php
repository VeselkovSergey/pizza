<?php

namespace App\Models;

/**
 * @property integer id
 * @property integer write_off_id
 * @property integer ingredient_id
 * @property float amount_ingredient
 * @property Ingredients Ingredient
 */
class IngredientsInWriteOff extends BaseModel
{
    protected $table = 'ingredients_in_write_off';

    protected $guarded = ['id'];

    public function Ingredient()
    {
        return $this->hasOne(Ingredients::class, 'id', 'ingredient_id');
    }
}