<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use App\Traits\HasAuditTrail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Staff extends Model
{
    use HasFactory, SoftDeletes, BelongsToSchool, HasAuditTrail;

    protected $table = 'staff';

    protected $fillable = [
        'school_id', 'user_id', 'staff_id_number', 'employee_type',
        'department', 'designation', 'qualification',
        'date_of_employment', 'date_of_birth', 'gender',
        'marital_status', 'nationality', 'state_of_origin',
        'address', 'city', 'state',
        'bank_name', 'account_number', 'account_name',
        'basic_salary', 'allowances', 'deductions',
        'photo', 'resume', 'status', // active, on_leave, terminated, retired
    ];

    protected $casts = [
        'date_of_employment' => 'date',
        'date_of_birth' => 'date',
        'basic_salary' => 'decimal:2',
        'allowances' => 'array',
        'deductions' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function classTeaching()
    {
        return $this->hasOne(SchoolClass::class, 'class_teacher_id');
    }

    public function subjectsTeaching()
    {
        return $this->belongsToMany(Subject::class, 'class_subject', 'teacher_id', 'subject_id');
    }

    public function attendances()
    {
        return $this->hasMany(StaffAttendance::class);
    }

    public function salary()
    {
        return $this->hasMany(SalaryPayment::class);
    }

    public function getFullNameAttribute(): string
    {
        return $this->user?->full_name ?? 'N/A';
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
