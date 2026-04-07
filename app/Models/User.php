<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // Essencial para o Svelte
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'cpf', 'nome', 'email', 'password', 'dt_nasc',
        'sexo', 'telefone', 'role_id', 'marital_status_id'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    // Relacionamentos para a API entregar nomes em vez de apenas IDs
    public function role() {
        return $this->belongsTo(Role::class);
    }

    public function maritalStatus() {
        return $this->belongsTo(MaritalStatus::class, 'marital_status_id');
    }
}
