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
        0 => 'Создана позиция',
        1 => 'Не начинали готовить',
        2 => 'В процессе приготовления',
        3 => 'Приготовлен',
    ];

    const STATUS_TEXT = [
        'create' => 0,
        'new' => 1,
        'chefProcesses' => 2,
        'cooked' => 3,
    ];

    public function ProductModifications()
    {
        return $this->hasOne(ProductModifications::class, 'id', 'product_modification_id');
    }

    public function Statusses()
    {
        return $this->hasMany(OrdersProductsStatusLogs::class, 'order_product_id', 'id');
    }
}
