<?php

namespace App\Models;

/**
 * @property integer id
 * @property integer supplier_id
 * @property string supply_date
 * @property integer payment_type
 * @property integer creator_id
 * @property string files
 * @property User Creator
 * @property Suppliers Supplier
 * @property string PaymentType
 * @property IngredientsInSupply Ingredients
 * @method Supply find($supplyId)
 */
class Supply extends BaseModel
{
    protected $table = 'supply';
    protected $fillable = [
        'supplier_id',
        'supply_date',
        'payment_type',
        'creator_id',
        'files',
    ];

    public function Creator()
    {
        return $this->hasOne(User::class, 'id', 'creator_id');
    }

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

    public static function SuppliesSum()
    {
        $supplies = new self();
        $supplies = $supplies->get();

        $sum = 0;
        foreach ($supplies as $supply) {
            $sum += $supply->SupplySum();
        }
        return $sum;
    }

    public static function SuppliesSumByDate($startDate = null, $endDate = null)
    {
        $startDate = strtotime($startDate);
        $endDate = strtotime($endDate);
        $startDate = date('Y-m-d 00:00:00', $startDate);
        $endDate = date('Y-m-d 23:59:59', $endDate);

        $supplies = new self();

        $supplies = $supplies->where('created_at', '>=', $startDate);
        $supplies = $supplies->where('created_at', '<=', $endDate);

        $supplies = $supplies->get();

        $sum = 0;
        foreach ($supplies as $supply) {
            $sum += $supply->SupplySum();
        }
        return $sum;

    }
}
