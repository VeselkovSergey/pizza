<?php

namespace App\Models;

/**
 * @property integer id
 * @property integer supplier_id
 * @property string supply_date
 * @property integer payment_type
 * @property Suppliers Supplier
 * @property string PaymentType
 */
class Supply extends BaseModel
{
    protected $table = 'supply';
    protected $fillable = [
        'supplier_id',
        'supply_date',
        'payment_type',
    ];

    public function Supplier()
    {
        return $this->hasOne(Suppliers::class, 'id', 'supplier_id');
    }

    public function Ingredients()
    {
        return $this->hasMany(IngredientsInSupply::class, 'supply_id', 'id');
    }

    public function PaymentType()
    {
        return match ($this->payment_type) {
            1 => 'Наличные',
            2 => 'Безналичные',
            3 => 'Перевод',
        };
    }

    public function SupplySum()
    {
        $sum = 0;
        foreach ($this->Ingredients as $ingredient) {
            /** @var IngredientsInSupply $ingredient */
            $sum += $ingredient->amount_ingredient * $ingredient->price_ingredient;
        }
        return $sum;
    }
}
