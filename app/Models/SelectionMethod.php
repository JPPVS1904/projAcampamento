<?php

namespace App\Models;

use Database\Factories\SelectionMethodFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['method', 'description'])]
class SelectionMethod extends Model
{
    /** @use HasFactory<SelectionMethodFactory> */
    use HasFactory;

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }
}
