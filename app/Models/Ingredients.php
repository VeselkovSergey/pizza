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

            return IngredientsInSupply::select('ingredients_in_supply.*', 'supply.supply_date as supply_date')
                ->where('ingredient_id', $this->id)
                ->leftJoin('supply', 'supply.id', '=', 'ingredients_in_supply.supply_id')
                ->orderBy('supply_date', 'DESC')
                ->first()
                ->price_ingredient;

        } catch (\Exception $e) {
             throw new \Exception('Нет поставки для ингредиента: ' . '#'.$this->id . ' - ' . $this->title);
        }
    }
}
