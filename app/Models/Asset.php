<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id', 'name', 'asset_code', 'category',
        'description', 'location', 'quantity',
        'purchase_date', 'purchase_price', 'supplier',
        'condition', // new, good, fair, poor, damaged
        'warranty_expires', 'last_maintenance_date',
        'assigned_to', 'photo', 'is_active',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'purchase_price' => 'decimal:2',
        'warranty_expires' => 'date',
        'last_maintenance_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function maintenanceLogs() { return $this->hasMany(MaintenanceLog::class); }
}
