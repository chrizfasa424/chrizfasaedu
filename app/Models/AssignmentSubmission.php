<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use App\Traits\HasAuditTrail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignmentSubmission extends Model
{
    use HasFactory, BelongsToSchool, HasAuditTrail;

    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_RESUBMITTED = 'resubmitted';
    public const STATUS_REVIEWED = 'reviewed';

    protected $fillable = [
        'school_id',
        'assignment_id',
        'student_id',
        'submission_text',
        'attachment_path',
        'attachment_type',
        'score',
        'teacher_feedback',
        'student_feedback',
        'student_feedback_at',
        'reviewed_by',
        'submitted_at',
        'reviewed_at',
        'status',
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'student_feedback_at' => 'datetime',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
