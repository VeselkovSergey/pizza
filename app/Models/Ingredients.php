<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ingredients extends Model
{
    protected $fillable = [
        'title',
    ];

    public function CurrentPrice()
    {
        try {
            return IngredientsInSupply::query()->where('ingredient_id', $this->id)->latest('id')->first()->price_ingredient;;
        } catch (\Exception $e) {
             throw new \Exception('Нет поставки для ингредиента: ' . '#'.$this->id . ' - ' . $this->title);
        }
    }
}
