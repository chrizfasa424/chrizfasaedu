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
        'total_score', 'average_score', 'position_in_class', 'class_size',
        'total_subjects', 'subjects_passed', 'subjects_failed',
        'class_teacher_remark', 'principal_remark',
        'next_term_begins', 'next_term_fees',
        'attendance_present', 'attendance_absent', 'attendance_total',
        'is_published', 'pdf_path',
    ];

    protected $casts = [
        'total_score' => 'decimal:2',
        'average_score' => 'decimal:2',
        'next_term_begins' => 'date',
        'next_term_fees' => 'decimal:2',
        'is_published' => 'boolean',
    ];

    public function student() { return $this->belongsTo(Student::class); }
    public function schoolClass() { return $this->belongsTo(SchoolClass::class, 'class_id'); }
    public function session() { return $this->belongsTo(AcademicSession::class, 'session_id'); }
    public function term() { return $this->belongsTo(AcademicTerm::class, 'term_id'); }
}
