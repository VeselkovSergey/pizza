<?php

namespace App\Models;

/**
 * @property integer id
 * @property integer user_id
 * @property string date
 * @property string start_shift
 * @property string end_shift
 *
 * @property User Employee
 */
class Calendar extends BaseModel
{
    protected $table = 'calendar';

    protected $guarded = [
        'id',
    ];

    public function Employee()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
