<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceLog extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id', 'asset_id', 'title', 'description',
        'cost', 'vendor', 'maintenance_date', 'next_due_date',
        'status', 'performed_by',
    ];

    protected $casts = [
        'cost' => 'decimal:2',
        'maintenance_date' => 'date',
        'next_due_date' => 'date',
    ];

    public function asset() { return $this->belongsTo(Asset::class); }
}
