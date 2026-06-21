<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PreRegistration extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'subscription_type',
    'is_fee_paid',
    'payment_code',
    'qrcode_data',
    'is_qrcode_used',
    'user_id',
    'activity_id',
    'camping_pre_registration_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function campingPreRegistration()
    {
        return $this->belongsTo(CampingPreRegistration::class);
    }
}
