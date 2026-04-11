<?php

namespace App\Models;

use App\Enums\SubscriptionType;
use Database\Factories\SubscriptionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'subscription_date',
    'subscription_type',
    'was_selected',
    'substitute_position',
    'paid_the_fee',
    'is_quitter',
    'payment_code',
    'qrcode_data',
    'used_qrcode',
    'selection_method_id',
    'user_id',
    'spouse_id',
    'event_id',
    'sector_id',
])]
class Subscription extends Model
{
    /** @use HasFactory<SubscriptionFactory> */
    use HasFactory, SoftDeletes;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'subscription_date' => 'datetime',
            'subscription_type' => SubscriptionType::class,
            'was_selected' => 'boolean',
            'substitute_position' => 'integer',
            'paid_the_fee' => 'boolean',
            'is_quitter' => 'boolean',
            'used_qrcode' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function spouse(): BelongsTo
    {
        return $this->belongsTo(User::class, 'spouse_id');
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function sector(): BelongsTo
    {
        return $this->belongsTo(Sector::class);
    }

    public function selectionMethod(): BelongsTo
    {
        return $this->belongsTo(SelectionMethod::class);
    }
}
