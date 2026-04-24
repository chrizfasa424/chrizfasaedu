<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use App\Traits\GeneratesAdmissionNumber;
use App\Traits\HasAuditTrail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes, BelongsToSchool, HasAuditTrail, GeneratesAdmissionNumber;

    protected $fillable = [
        'school_id', 'user_id', 'admission_id', 'class_id', 'arm_id',
        'admission_number', 'registration_number',
        'first_name', 'last_name', 'other_names',
        'gender', 'date_of_birth', 'blood_group', 'genotype',
        'nationality', 'state_of_origin', 'lga',
        'religion', 'address', 'city', 'state',
        'photo', 'previous_school',
        'medical_conditions', 'allergies', 'disabilities',
        'emergency_contact_name', 'emergency_contact_phone',
        'session_admitted', 'status', // active, graduated, transferred, expelled, withdrawn
        'is_boarding', 'hostel_room_id', 'transport_route_id',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'is_boarding' => 'boolean',
        'medical_conditions' => 'array',
        'allergies' => 'array',
    ];

    // ── Relationships ──────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function arm()
    {
        return $this->belongsTo(ClassArm::class, 'arm_id');
    }

    public function parents()
    {
        return $this->belongsToMany(ParentGuardian::class, 'parent_student', 'student_id', 'parent_id')
            ->withPivot('relationship');
    }

    public function admission()
    {
        return $this->belongsTo(Admission::class);
    }

    public function attendances()
    {
        return $this->hasMany(StudentAttendance::class);
    }

    public function assessments()
    {
        return $this->hasMany(Assessment::class);
    }

    public function assignmentSubmissions()
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    public function results()
    {
        return $this->hasMany(Result::class);
    }

    public function sheetResults()
    {
        return $this->hasMany(StudentResult::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments()
    {
        return $this->hasManyThrough(Payment::class, Invoice::class);
    }

    public function behaviourRecords()
    {
        return $this->hasMany(BehaviourRecord::class);
    }

    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class);
    }

    public function hostelRoom()
    {
        return $this->belongsTo(HostelRoom::class);
    }

    public function transportRoute()
    {
        return $this->belongsTo(TransportRoute::class);
    }

    // ── Accessors ──────────────────────────────────────────

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function getAgeAttribute(): ?int
    {
        return $this->date_of_birth?->age;
    }

    // ── Scopes ──────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInClass($query, int $classId)
    {
        return $query->where('class_id', $classId);
    }
}
