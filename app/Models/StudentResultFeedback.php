<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentResultFeedback extends Model
{
    use HasFactory, BelongsToSchool;

    protected $table = 'student_result_feedbacks';

    protected $fillable = [
        'school_id',
        'student_id',
        'student_result_id',
        'term_id',
        'exam_type_id',
        'subject_id',
        'feedback_type',
        'title',
        'message',
        'status',
        'admin_response',
        'responded_by',
        'responded_at',
        'student_read_at',
    ];

    protected $casts = [
        'responded_at' => 'datetime',
        'student_read_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function studentResult()
    {
        return $this->belongsTo(StudentResult::class);
    }

    public function term()
    {
        return $this->belongsTo(AcademicTerm::class);
    }

    public function examType()
    {
        return $this->belongsTo(ExamType::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function responder()
    {
        return $this->belongsTo(User::class, 'responded_by');
    }
}
