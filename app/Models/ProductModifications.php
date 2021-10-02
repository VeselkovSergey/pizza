<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductModifications extends Model
{
    protected $fillable = [
        'product_id',
        'modification_id',
        'selling_price',
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
