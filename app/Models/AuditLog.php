<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id', 'user_id', 'action', 'model_type', 'model_id',
        'changes', 'ip_address', 'user_agent',
    ];

    protected $casts = [
        'changes' => 'array',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function school() { return $this->belongsTo(School::class); }
}
