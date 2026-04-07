<?php

namespace App\Models;

use App\Enums\UserRole;
use App\Notifications\CustomResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasRoles;

    protected $fillable = [
        'school_id',
        'first_name',
        'last_name',
        'other_names',
        'email',
        'phone',
        'password',
        'role',
        'avatar',
        'is_active',
        'last_login_at',
        'last_login_ip',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'role' => UserRole::class,
    ];

    // ── Accessors ──────────────────────────────────────────

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    // ── Relationships ──────────────────────────────────────

    public function student()
    {
        return $this->hasOne(Student::class);
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function staffProfile()
    {
        return $this->hasOne(Staff::class);
    }

    public function parentProfile()
    {
        return $this->hasOne(ParentGuardian::class);
    }

    // ── Helpers ─────────────────────────────────────────────

    public function isSuperAdmin(): bool
    {
        return $this->role === UserRole::SUPER_ADMIN;
    }

    public function isSchoolAdmin(): bool
    {
        return $this->role === UserRole::SCHOOL_ADMIN;
    }

    public function isTeacher(): bool
    {
        return $this->role === UserRole::TEACHER;
    }

    public function isStudent(): bool
    {
        return $this->role === UserRole::STUDENT;
    }

    public function isParent(): bool
    {
        return $this->role === UserRole::PARENT;
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new CustomResetPasswordNotification($token));
    }

    // ── Messaging relationships ────────────────────────────

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function messageRecipients()
    {
        return $this->hasMany(MessageRecipient::class);
    }

    public function messageReplies()
    {
        return $this->hasMany(MessageReply::class, 'sender_id');
    }

    // ── Notification bell helpers ──────────────────────────

    /** Unread inbox messages — used by portal users (student/parent) */
    public function unreadMessagesCount(): int
    {
        return MessageRecipient::where('user_id', $this->id)
            ->whereNull('read_at')
            ->count();
    }

    /** Unread replies from portal users — used by admin/staff */
    public function unreadAdminRepliesCount(): int
    {
        return MessageReply::whereHas('message', function ($q) {
            $q->where('school_id', $this->school_id);
        })->whereNull('read_by_admin_at')->count();
    }
}
