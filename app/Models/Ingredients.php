<?php

namespace App\Models;

/**
 * @property integer id Ид ингредиента
 * @property integer title Название ингредиента
 */
class Ingredients extends BaseModel
{
    protected $fillable = [
        'title',
    ];

    public function CurrentPrice()
    {
        try {
            return IngredientsInSupply::where('ingredient_id', $this->id)->latest('id')->first()->price_ingredient;
        } catch (\Exception $e) {
             throw new \Exception('Нет поставки для ингредиента: ' . '#'.$this->id . ' - ' . $this->title);
        }
    }
}
