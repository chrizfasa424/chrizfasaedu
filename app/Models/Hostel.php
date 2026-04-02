<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hostel extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id', 'name', 'type', // male, female
        'description', 'warden_id', 'capacity', 'fee_amount', 'is_active',
    ];

    protected $casts = [
        'fee_amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function rooms() { return $this->hasMany(HostelRoom::class); }
    public function warden() { return $this->belongsTo(Staff::class, 'warden_id'); }
}
