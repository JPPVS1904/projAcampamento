<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'text',
        'order',
        'type',
        'accept_generic_answer',
        'section_id',
        'depends_on_option_id'
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'categories_questions');
    }
}
