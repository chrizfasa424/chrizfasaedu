<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use App\Traits\HasAuditTrail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResultSubmission extends Model
{
    use HasFactory, BelongsToSchool, HasAuditTrail;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_UNDER_REVIEW = 'under_review';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_IMPORTED = 'imported';

    protected $fillable = [
        'school_id',
        'teacher_id',
        'section',
        'class_id',
        'arm_id',
        'subject_id',
        'session_id',
        'term_id',
        'exam_type_id',
        'assessment_type',
        'import_mode',
        'file_path',
        'original_file_name',
        'status',
        'staff_note',
        'admin_note',
        'validation_summary',
        'submitted_at',
        'reviewed_at',
        'reviewed_by',
        'imported_at',
        'imported_by',
        'result_batch_id',
    ];

    protected $casts = [
        'validation_summary' => 'array',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'imported_at' => 'datetime',
    ];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function arm()
    {
        return $this->belongsTo(ClassArm::class, 'arm_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
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

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function importer()
    {
        return $this->belongsTo(User::class, 'imported_by');
    }

    public function resultBatch()
    {
        return $this->belongsTo(ResultBatch::class, 'result_batch_id');
    }
}
