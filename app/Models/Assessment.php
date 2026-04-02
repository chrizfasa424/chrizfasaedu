<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id', 'student_id', 'subject_id', 'class_id', 'arm_id',
        'session_id', 'term_id', 'teacher_id',
        'type',  // ca1, ca2, ca3, exam, project, assignment
        'score', 'max_score', 'remarks',
        'is_published',
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'max_score' => 'decimal:2',
        'is_published' => 'boolean',
    ];

    public function student() { return $this->belongsTo(Student::class); }
    public function subject() { return $this->belongsTo(Subject::class); }
    public function schoolClass() { return $this->belongsTo(SchoolClass::class, 'class_id'); }
    public function session() { return $this->belongsTo(AcademicSession::class, 'session_id'); }
    public function term() { return $this->belongsTo(AcademicTerm::class, 'term_id'); }
    public function teacher() { return $this->belongsTo(Staff::class, 'teacher_id'); }
}
