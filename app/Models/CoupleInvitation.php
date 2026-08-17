<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoupleInvitation extends Model
{
    protected $fillable = [
        'inviter_id',
        'invitee_id',
        'activity_id',
        'status',
    ];

    public function inviter()
    {
        return $this->belongsTo(User::class, 'inviter_id');
    }

    public function invitee()
    {
        return $this->belongsTo(User::class, 'invitee_id');
    }

    public function activity()
    {
        return $this->belongsTo(Activity::class, 'activity_id');
    }
}
