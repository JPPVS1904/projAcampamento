<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InboxMessage extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'content',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
