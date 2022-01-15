<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @mixin Builder
 *
 * @property integer id
 * @property string phone
 * @property string name
 * @property string surname
 * @property string patronymic
 * @property string telegram_chat_id
 * @property integer role_id
 * @property integer is_employee
 * @property Orders Orders
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'patronymic',
        'surname',
        'full_name',
        'email',
        'phone',
        'email_verified_at',
        'phone_verified_at',
        'password',
        'role_id',
        'telegram_chat_id',
        'is_employee',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
    ];

    const PERMISSIONS = [
        777 => [        // менеджер
            'ARM' => [
                'management' => [],
                'chef' => [],
            ],
        ],
    ];

    public function Orders()
    {
        return $this->hasMany(Orders::class, 'user_id', 'id');
    }

    public function IsAdmin()
    {
        if (auth()->check() && auth()->user()->role_id === 999) {
            return true;
        }
        return false;
    }

    public function IsManager()
    {
        if ($this->IsAdmin()) {
            return true;
        }

        if (auth()->check() && auth()->user()->role_id === 777) {
            return true;
        }
        return false;
    }

    public function IsStaff()
    {
        if (auth()->check() && auth()->user()->role_id > 100) {
            return true;
        }
        return false;
    }

    public function UserIsAdmin()
    {
        if ($this->role_id === 999) {
            return true;
        }
        return false;
    }

    public function UserIsManager()
    {
        if ($this->role_id === 777) {
            return true;
        }
        return false;
    }

    public function checkAccess($permissionCategory, $permission = null, $subPermission = null)
    {
        $roleId = $this->role_id;

        if ($this->IsAdmin()) {
            return true;
        }

        $permissionCategory = trim($permissionCategory);
        if (isset(self::PERMISSIONS[$roleId][$permissionCategory])) {

            if (isset($permission)) {
                $permission = trim($permission);

                if (isset(self::PERMISSIONS[$roleId][$permissionCategory][$permission])) {

                    if (isset($subPermission)) {
                        $subPermission = trim($subPermission);

                        if (isset(self::PERMISSIONS[$roleId][$permissionCategory][$permission][$subPermission])) {
                            return true;
                        }

                    } else {
                        return true;
                    }
                }

            } else {
                return true;
            }
        }
        return false;
    }

    public static function Employees()
    {
        return User::where('role_id', '!=', 1)->get();
    }
}
