<?php

namespace App\Models;

use Database\Factories\MaritalStatusFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['title'])]
class MaritalStatus extends Model
{
    /** @use HasFactory<MaritalStatusFactory> */
    use HasFactory, SoftDeletes;

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
