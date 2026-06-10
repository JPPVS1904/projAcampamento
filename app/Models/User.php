<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'cpf',
    'name',
    'birthday',
    'sex',
    'phone',
    'email',
    'photo',
    'is_counselor',
    'is_admin',
    'password',
    'access_token',
    'refresh_token',
    'marital_status_id'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'birthday' => 'date',
            'password' => 'hashed',
            'is_counselor' => 'boolean',
            'is_admin' => 'boolean',
        ];
    }
}
