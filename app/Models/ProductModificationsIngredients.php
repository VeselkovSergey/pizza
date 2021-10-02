<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductModificationsIngredients extends Model
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
