<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * @property string $name
 * @property string $email
 * @property bool $admin
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // protected $attributes = [
    //     'active' => true,
    //     'admin' => false,
    // ];

    protected $fillable = [
        'name', 'email', 'avatar',
        'active','password', 'admin', 'super_admin', 'organization', 'corrector',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
    
    public function isAdmin()
    {
        return $this->admin === 1;
    }
    
    public function isSuperAdmin()
    {
        // Строгая проверка: только если super_admin равен 1, true или строке '1'
        if ($this->super_admin === 1 || $this->super_admin === true) {
            return true;
        }
        // Проверяем строковое значение
        if ($this->super_admin === '1') {
            return true;
        }
        // Все остальные случаи (0, false, null, '0', и т.д.) - не суперадмин
        return false;
    }
    
    public function isCorrector()
    {
        return $this->corrector === 1;
    }
}
