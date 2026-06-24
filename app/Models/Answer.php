<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Answer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'pre_registration_id',
    'question_id',
    'answer'
    ];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function preRegistration()
    {
        return $this->belongsTo(PreRegistration::class);
    }
}
