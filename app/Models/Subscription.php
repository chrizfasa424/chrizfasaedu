<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id', 'plan_name', 'plan_code',
        'amount', 'billing_cycle', // monthly, termly, yearly
        'student_limit', 'staff_limit', 'storage_limit_gb',
        'features', 'starts_at', 'expires_at',
        'is_active', 'payment_reference', 'auto_renew',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'features' => 'array',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
        'auto_renew' => 'boolean',
    ];

    public function school() { return $this->belongsTo(School::class); }
}
