<?php

namespace App\Models;

/**
 * @property integer id
 * @property string title
 * @property string MinimumPrice
 * @property Categories Category
 * @property string description
 * @property integer category_id
 * @property integer show_in_catalog
 * @property integer sort
 * @property integer is_additional_sales
 * @property integer additional_sales_sort
 * @property integer is_new
 * @property integer is_spicy
 * @property integer is_hidden
 *
 * @property ProductModifications Modifications
 */
class Products extends BaseModel
{
    protected $guarded = ['id'];

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
