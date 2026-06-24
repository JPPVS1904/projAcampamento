<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CampingPreRegistration extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'camping_pre_registration';

    public $timestamps = false;

    protected $fillable = [
        'substitute_position',
        'is_quitter',
        'selection_method_id',
        'spouse_id',
        'sector_id',
        'sector2_id'
    ];

    public function sector()
    {
        return $this->belongsTo(Sector::class);
    }
}
