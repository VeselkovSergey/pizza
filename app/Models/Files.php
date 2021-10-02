<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Files extends Model
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
