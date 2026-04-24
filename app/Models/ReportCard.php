<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportCard extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id', 'student_id', 'class_id', 'session_id', 'term_id',
        'total_score', 'average_score', 'class_average', 'total_ca1', 'total_ca2', 'total_exam',
        'position_in_class', 'class_size',
        'total_subjects', 'subjects_passed', 'subjects_failed',
        'class_teacher_remark', 'principal_remark',
        'next_term_begins', 'next_term_fees',
        'attendance_present', 'attendance_absent', 'attendance_total',
        'is_published', 'pdf_path',
    ];

    protected $casts = [
        'total_score' => 'decimal:2',
        'average_score' => 'decimal:2',
        'class_average' => 'decimal:2',
        'total_ca1' => 'decimal:2',
        'total_ca2' => 'decimal:2',
        'total_exam' => 'decimal:2',
        'next_term_begins' => 'date',
        'next_term_fees' => 'decimal:2',
        'is_published' => 'boolean',
    ];

    public function getAttendanceDisplayAttribute(): ?string
    {
        if (!$this->attendance_total) {
            return null;
        }

        return ((int) $this->attendance_present) . '/' . ((int) $this->attendance_total);
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

    public function student() { return $this->belongsTo(Student::class); }
    public function schoolClass() { return $this->belongsTo(SchoolClass::class, 'class_id'); }
    public function session() { return $this->belongsTo(AcademicSession::class, 'session_id'); }
    public function term() { return $this->belongsTo(AcademicTerm::class, 'term_id'); }
}
