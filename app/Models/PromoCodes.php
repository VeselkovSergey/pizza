<?php

namespace App\Models;

/**
 * @property string title
 * @property string description
 * @property string conditions
 * @property string start_date
 * @property string end_date
 * @property integer amount
 * @property integer amount_used
 */
class PromoCodes extends BaseModel
{
    protected $fillable = [
        'title',
        'description',
        'conditions',
        'start_date',
        'end_date',
        'amount',
        'amount_used',
    ];
}
