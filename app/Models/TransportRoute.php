<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransportRoute extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id', 'name', 'description',
        'pickup_points', 'driver_id', 'vehicle_number',
        'vehicle_type', 'capacity', 'fee_amount',
        'is_active',
    ];

    protected $casts = [
        'pickup_points' => 'array',
        'fee_amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function driver() { return $this->belongsTo(Staff::class, 'driver_id'); }
    public function students() { return $this->hasMany(Student::class); }
}
