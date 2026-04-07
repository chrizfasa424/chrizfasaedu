<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use App\Traits\HasAuditTrail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    use HasFactory, BelongsToSchool, HasAuditTrail;

    protected $fillable = [
        'school_id', 'student_id', 'class_id', 'arm_id',
        'session_id', 'term_id', 'subject_id',
        'ca1_score', 'ca2_score', 'ca3_score', 'exam_score',
        'total_score', 'grade', 'grade_point', 'position_in_subject',
        'teacher_remark', 'is_approved', 'approved_by', 'approved_at',
    ];

    protected $casts = [
        'ca1_score' => 'decimal:2',
        'ca2_score' => 'decimal:2',
        'ca3_score' => 'decimal:2',
        'exam_score' => 'decimal:2',
        'total_score' => 'decimal:2',
        'grade_point' => 'decimal:2',
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
    ];

    public function student() { return $this->belongsTo(Student::class); }
    public function subject() { return $this->belongsTo(Subject::class); }
    public function schoolClass() { return $this->belongsTo(SchoolClass::class, 'class_id'); }
    public function session() { return $this->belongsTo(AcademicSession::class, 'session_id'); }
    public function term() { return $this->belongsTo(AcademicTerm::class, 'term_id'); }
    public function approvedBy() { return $this->belongsTo(User::class, 'approved_by'); }

    // Grading system: 70-100=A, 60-69=B, 50-59=C, 40-49=D, 0-39=E
    public static function calculateGrade(float $score): array
    {
        return match(true) {
            $score >= 70 => ['grade' => 'A', 'point' => 5.0, 'remark' => 'Excellent'],
            $score >= 60 => ['grade' => 'B', 'point' => 4.5, 'remark' => 'Very Good'],
            $score >= 50 => ['grade' => 'C', 'point' => 4.0, 'remark' => 'Good'],
            $score >= 40 => ['grade' => 'D', 'point' => 3.5, 'remark' => 'Pass'],
            default      => ['grade' => 'E', 'point' => 3.0, 'remark' => 'Fail'],
        };
    }
}
