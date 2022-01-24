<?php

namespace App\Models;

/**
 * @property integer id
 * @property string title
 * @property string MinimumPrice
 * @property Categories Category
 * @property string description
 * @property integer category_id
 * @property integer active
 * @property integer sort
 *
 * @property ProductModifications Modifications
 */
class Products extends BaseModel
{
    protected $fillable = ['id'];

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
