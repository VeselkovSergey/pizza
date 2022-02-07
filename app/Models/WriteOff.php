<?php

namespace App\Models;

/**
 * @property integer id
 * @property integer creator_id
 * @property string date
 * @property string description
 */
class WriteOff extends BaseModel
{
    protected $guarded = ['id'];

    public function Ingredients()
    {
        return $this->hasMany(IngredientsInWriteOff::class, 'write_off_id', 'id');
    }

    public function Creator()
    {
        return $this->hasOne(User::class, 'id', 'creator_id');
    }
}