<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
    'type'
    ];

    public function sectors()
    {
        return $this->belongsToMany(Sector::class, 'categories_sectors')
            ->withPivot('id', 'base_vacancies', 'raffle_vacancies')
            ->withTimestamps();
    }
}
