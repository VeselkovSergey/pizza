<?php

namespace App\Models;

/**
 * @property string title
 * @property ProductModifications Modifications
 * @property string MinimumPrice
 * @property Categories Category
 */
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

    public function Category()
    {
        return $this->hasOne(Categories::class, 'id', 'category_id');
    }
}
