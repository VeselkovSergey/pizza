<?php

namespace App\Models;

/**
 * @property integer id Ид ингредиента
 * @property string title Название ингредиента
 * @property string description Описание ингредиента
 */
class Ingredients extends BaseModel
{
    protected $fillable = [
        'title',
        'description',
    ];

    public function CurrentPrice()
    {
        try {
            return $this->LastSupply()->price_ingredient;
        } catch (\Exception $e) {
            throw new \Exception('Нет поставки для ингредиента: ' . '#' . $this->id . ' - ' . $this->title);
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
            throw new \Exception('Нет поставки для ингредиента: ' . '#' . $this->id . ' - ' . $this->title);
        }
    }

    public function SupplyByDate($supplyDate)
    {
        try {
            return IngredientsInSupply::select('ingredients_in_supply.*', 'supply.supply_date as supply_date')
                ->where('ingredient_id', $this->id)
                ->leftJoin('supply', 'supply.id', '=', 'ingredients_in_supply.supply_id')
                ->where('supply_date', '<=', $supplyDate)
                ->orderByDesc('supply_date')
                ->first();
        } catch (\Exception $e) {
            throw new \Exception('На ' . $supplyDate . ' нет поставки для ингредиента: ' . '#' . $this->id . ' - ' . $this->title);
        }
    }

    public function PriceByDate($supplyDate)
    {
        try {
            return $this->SupplyByDate($supplyDate)->price_ingredient;
        } catch (\Exception $e) {
            throw new \Exception('На ' . $supplyDate . ' нет поставки для ингредиента: ' . '#' . $this->id . ' - ' . $this->title);
        }
    }
}
