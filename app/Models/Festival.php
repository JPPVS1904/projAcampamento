<?php

namespace App\Models;

use Database\Factories\FestivalFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'minimal_age',
    'is_paid_festival',
    'ticket_price',
    'sale_start_date',
    'payment_link',
])]
class Festival extends Model
{
    /** @use HasFactory<FestivalFactory> */
    use HasFactory, SoftDeletes;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'minimal_age' => 'integer',
            'is_paid_festival' => 'boolean',
            'ticket_price' => 'integer',
            'sale_start_date' => 'datetime',
            'payment_link' => 'integer',
        ];
    }

    public function event(): MorphOne
    {
        return $this->morphOne(Event::class, 'eventable');
    }
}
