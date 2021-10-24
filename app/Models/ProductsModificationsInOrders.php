<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductsModificationsInOrders extends Model
{

    protected $fillable = [
        'order_id',
        'status_id',
        'product_modification_id',
        'product_modification_amount',
    ];

    const STATUS = [
        1 => 'Не начинали готовить',
        2 => 'В процессе приготовления',
        3 => 'Приготовлен',
    ];

    const STATUS_TEXT = [
        'new' => 1,
        'processing' => 2,
        'cooked' => 3,
    ];

    public function ProductModifications()
    {
        return $this->hasOne(ProductModifications::class, 'id', 'product_modification_id');
    }
}
