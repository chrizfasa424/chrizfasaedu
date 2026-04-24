<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResultBatch extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'section',
        'class_id',
        'arm_id',
        'session_id',
        'term_id',
        'exam_type_id',
        'assessment_type',
        'file_name',
        'stored_path',
        'source_type',
        'import_mode',
        'total_rows',
        'success_rows',
        'failed_rows',
        'status',
        'summary',
        'imported_by',
        'validated_at',
        'imported_at',
    ];

    protected $casts = [
        'summary' => 'array',
        'validated_at' => 'datetime',
        'imported_at' => 'datetime',
    ];

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

    public function importer()
    {
        return $this->belongsTo(User::class, 'imported_by');
    }

    public function errors()
    {
        return $this->hasMany(ResultImportError::class);
    }

    public function studentResults()
    {
        return $this->hasMany(StudentResult::class);
    }
}
