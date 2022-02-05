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
            return $this->LastSupply()->price_ingredient;
        } catch (\Exception $e) {
            throw new \Exception('Нет поставки для ингредиента: ' . '#'.$this->id . ' - ' . $this->title);
        }
    }

    public function LastSupply()
    {
        try {

            return IngredientsInSupply::select('ingredients_in_supply.*', 'supply.supply_date as supply_date')
                ->where('ingredient_id', $this->id)
                ->leftJoin('supply', 'supply.id', '=', 'ingredients_in_supply.supply_id')
                ->orderByDesc('supply_date')
                ->first();

        } catch (\Exception $e) {
             throw new \Exception('Нет поставки для ингредиента: ' . '#'.$this->id . ' - ' . $this->title);
        }
    }
}
