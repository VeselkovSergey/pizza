<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

/**
 * @property integer id
 */
class Files extends BaseModel
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'hash_name',
        'original_name',
        'extension',
        'type',
        'disk',
        'path',
    ];
}
