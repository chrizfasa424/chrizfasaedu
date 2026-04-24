<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentResult extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'student_id',
        'result_batch_id',
        'section',
        'class_id',
        'arm_id',
        'session_id',
        'term_id',
        'exam_type_id',
        'total_score',
        'average_score',
        'class_average',
        'class_position',
        'promoted_to_class_id',
        'attendance_present',
        'attendance_total',
        'class_teacher_remark',
        'principal_remark',
        'vice_principal_remark',
        'class_teacher_remark_active',
        'principal_remark_active',
        'vice_principal_remark_active',
        'principal_signature',
        'signed_at',
        'is_published',
        'published_at',
        'first_test_imported_at',
        'second_test_imported_at',
        'exam_imported_at',
        'full_result_imported_at',
        'first_test_published_at',
        'first_test_published_by',
        'second_test_published_at',
        'second_test_published_by',
        'exam_published_at',
        'exam_published_by',
        'full_result_published_at',
        'full_result_published_by',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'total_score' => 'decimal:2',
        'average_score' => 'decimal:2',
        'class_average' => 'decimal:2',
        'signed_at' => 'date',
        'is_published' => 'boolean',
        'class_teacher_remark_active' => 'boolean',
        'principal_remark_active' => 'boolean',
        'vice_principal_remark_active' => 'boolean',
        'published_at' => 'datetime',
        'first_test_imported_at' => 'datetime',
        'second_test_imported_at' => 'datetime',
        'exam_imported_at' => 'datetime',
        'full_result_imported_at' => 'datetime',
        'first_test_published_at' => 'datetime',
        'second_test_published_at' => 'datetime',
        'exam_published_at' => 'datetime',
        'full_result_published_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function arm()
    {
        return $this->belongsTo(ClassArm::class, 'arm_id');
    }

    public function session()
    {
        return $this->belongsTo(AcademicSession::class, 'session_id');
    }

    public function term()
    {
        return $this->belongsTo(AcademicTerm::class, 'term_id');
    }

    public function examType()
    {
        return $this->belongsTo(ExamType::class);
    }

    public function resultBatch()
    {
        return $this->belongsTo(ResultBatch::class);
    }

    public function items()
    {
        return $this->hasMany(StudentResultItem::class);
    }

    public function promotedToClass()
    {
        return $this->belongsTo(SchoolClass::class, 'promoted_to_class_id');
    }

    public function feedbacks()
    {
        return $this->hasMany(StudentResultFeedback::class);
    }

    public function getAttendanceDisplayAttribute(): ?string
    {
        if (!$this->attendance_total) {
            return null;
        }

        return $this->attendance_present . '/' . $this->attendance_total;
    }

    public function getAttendancePercentageAttribute(): ?float
    {
        if (!$this->attendance_total) {
            return null;
        }

        return round((((float) $this->attendance_present) / ((float) $this->attendance_total)) * 100, 1);
    }

    public function getAttendanceSummaryAttribute(): ?string
    {
        if (!$this->attendance_display) {
            return null;
        }

        if ($this->attendance_percentage === null) {
            return $this->attendance_display;
        }

        return $this->attendance_display . ' (' . $this->attendance_percentage . '%)';
    }

    public function isPublishedFor(string $assessmentType): bool
    {
        return match ($assessmentType) {
            'first_test' => !is_null($this->first_test_published_at),
            'second_test' => !is_null($this->second_test_published_at),
            'exam' => !is_null($this->exam_published_at),
            'full_result' => !is_null($this->full_result_published_at),
            default => false,
        };
    }
}
