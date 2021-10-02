<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Products extends Model
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
        return ProductModifications::query()->where('product_id', $this->id)->orderBy('selling_price')->first()->selling_price;
    }
}
