<?php

namespace App\Models;

class Products extends BaseModel
{
    protected $fillable = [
        'title',
    ];

    public function Modifications()
    {
        return $this->hasMany(ProductModifications::class, 'product_id', 'id');
    }

    public function MinimumPrice()
    {
        return ProductModifications::where('product_id', $this->id)->orderBy('selling_price')->first()->selling_price;
    }
}
