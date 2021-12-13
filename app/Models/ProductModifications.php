<?php

namespace App\Models;

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
        return $this->hasMany(ProductModificationsIngredients::class, 'product_modification_id', 'id');
    }
}
