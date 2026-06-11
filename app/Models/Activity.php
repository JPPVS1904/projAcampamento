<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Activity extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
    'image',
    'place',
    'year',
    'start_date',
    'duration_days',
    'total_vacancies',
    'category_id',
    'activitable_type',
    'activitable_id'
    ];

    public function activitable()
    {
        return $this->morphTo();
    }
}
