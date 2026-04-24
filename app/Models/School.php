<?php

namespace App\Models;

use App\Traits\HasAuditTrail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class School extends Model
{
    use HasFactory, SoftDeletes, HasAuditTrail;

    protected $fillable = [
        'name',
        'slug',
        'code',
        'domain',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'country',
        'logo',
        'banner',
        'motto',
        'website',
        'established_year',
        'registration_number',
        'school_type',       // primary, secondary, combined
        'ownership',         // private, government, mission
        'subscription_plan',
        'subscription_expires_at',
        'settings',
        'is_active',
    ];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
        'subscription_expires_at' => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function staff()
    {
        return $this->hasMany(Staff::class);
    }

    public function classes()
    {
        return $this->hasMany(SchoolClass::class);
    }

    public function sessions()
    {
        return $this->hasMany(AcademicSession::class);
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }

    public function examTypes()
    {
        return $this->hasMany(ExamType::class);
    }

    public function studentResults()
    {
        return $this->hasMany(StudentResult::class);
    }

    public function feeStructures()
    {
        return $this->hasMany(FeeStructure::class);
    }

    public function admissions()
    {
        return $this->hasMany(Admission::class);
    }

    public function heroSlides()
    {
        return $this->hasMany(HeroSlide::class);
    }

    // ── Helpers ─────────────────────────────────────────────

    public function isSubscriptionActive(): bool
    {
        return $this->is_active && $this->subscription_expires_at?->isFuture();
    }

    public function currentSession()
    {
        return $this->sessions()->where('is_current', true)->first();
    }

    public function getSetting(string $key, $default = null)
    {
        return data_get($this->settings, $key, $default);
    }
}
