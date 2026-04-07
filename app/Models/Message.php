<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'sender_id',
        'audience',
        'class_id',
        'subject',
        'body',
    ];

    protected $casts = [
        'audience' => 'string',
    ];

    // ── Relationships ──────────────────────────────────────

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function schoolClass()
    {
        return $this->belongsTo(\App\Models\SchoolClass::class, 'class_id');
    }

    public function recipients()
    {
        return $this->hasMany(MessageRecipient::class);
    }

    public function replies()
    {
        return $this->hasMany(MessageReply::class);
    }

    // ── Helpers ────────────────────────────────────────────

    public function audienceLabel(): string
    {
        return match ($this->audience) {
            'all_students' => 'All Students',
            'all_parents'  => 'All Parents',
            'all_portal'   => 'All Students & Parents',
            'class'        => 'Class: ' . ($this->schoolClass?->name ?? '—'),
            default        => ucfirst($this->audience),
        };
    }

    public function unreadRepliesCount(): int
    {
        return $this->replies()->whereNull('read_by_admin_at')->count();
    }

    public function recipientsCount(): int
    {
        return $this->recipients()->count();
    }

    public function readCount(): int
    {
        return $this->recipients()->whereNotNull('read_at')->count();
    }
}
