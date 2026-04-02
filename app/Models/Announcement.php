<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id', 'title', 'body', 'type', // general, academic, financial, event
        'audience', // all, students, parents, staff, specific_class
        'target_class_id', 'priority', // low, normal, high, urgent
        'published_at', 'expires_at',
        'send_sms', 'send_email', 'is_pinned',
        'created_by',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'expires_at' => 'datetime',
        'send_sms' => 'boolean',
        'send_email' => 'boolean',
        'is_pinned' => 'boolean',
    ];

    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function targetClass() { return $this->belongsTo(SchoolClass::class, 'target_class_id'); }
}
