<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use App\Traits\HasAuditTrail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use HasFactory, BelongsToSchool, HasAuditTrail;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_UNPUBLISHED = 'unpublished';

    protected $fillable = [
        'school_id',
        'teacher_id',
        'subject_id',
        'session_id',
        'term_id',
        'title',
        'description',
        'due_date',
        'attachment_path',
        'attachment_type',
        'status',
        'published_by',
        'published_at',
    ];

    protected $casts = [
        'due_date' => 'date',
        'published_at' => 'datetime',
    ];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
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

    public function targets()
    {
        return $this->hasMany(AssignmentClassTarget::class);
    }

    public function publishedBy()
    {
        return $this->belongsTo(User::class, 'published_by');
    }

    public function submissions()
    {
        return $this->hasMany(AssignmentSubmission::class);
    }
}
