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
        'must_change_password',
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
        'must_change_password' => 'boolean',
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

    public function isStaff(): bool
    {
        return $this->role === UserRole::STAFF;
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

    /** Unread admin responses on student's result queries */
    public function unreadResultFeedbackResponsesCount(): int
    {
        if (!$this->isStudent() || !$this->school_id || !$this->student) {
            return 0;
        }

        return StudentResultFeedback::query()
            ->where('school_id', (int) $this->school_id)
            ->where('student_id', (int) $this->student->id)
            ->where('feedback_type', 'query')
            ->whereNotNull('admin_response')
            ->whereNotNull('responded_at')
            ->whereNull('student_read_at')
            ->count();
    }

    /** Open result feedback / queries from students â€” used by admin/staff bell */
    public function openResultFeedbackCount(): int
    {
        if (!$this->school_id) {
            return 0;
        }

        if (!in_array((string) ($this->role?->value ?? ''), [
            UserRole::SUPER_ADMIN->value,
            UserRole::SCHOOL_ADMIN->value,
            UserRole::PRINCIPAL->value,
            UserRole::VICE_PRINCIPAL->value,
            UserRole::TEACHER->value,
        ], true)) {
            return 0;
        }

        return StudentResultFeedback::query()
            ->where('school_id', (int) $this->school_id)
            ->where('status', 'open')
            ->count();
    }
}
