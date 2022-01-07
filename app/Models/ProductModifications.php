<?php

namespace App\Models;

/**
 * @property integer id
 * @property integer modification_id
 * @property float selling_price
 * @property integer stop_list
 * @property Products Product
 * @property Modifications Modification
 * @property ProductModificationsIngredients Ingredients
 */
class ProductModifications extends BaseModel
{
    protected $fillable = [
        'product_id',
        'modification_id',
        'selling_price',
        'stop_list',
    ];

    public function Product()
    {
        return $this->hasOne(Products::class, 'id', 'product_id');
    }

    public function Modification()
    {
        return $this->hasOne(Modifications::class, 'id', 'modification_id');
    }

    public function Ingredients()
    {
        return $this->hasMany(ProductModificationsIngredients::class, 'product_modification_id', 'id')->orderBy('ingredient_id');
    }
}
