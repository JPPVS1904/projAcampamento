<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'minimal_age',
    'is_paid_festival',
    'ticket_price',
    'sale_start_date',
    'payment_link'
    ];

    public function activity()
    {
        return $this->morphOne(Activity::class, 'activitable');
    }
}
