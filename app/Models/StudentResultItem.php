<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentResultItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_result_id',
        'subject_id',
        'exam_score',
        'first_test_score',
        'second_test_score',
        'total_score',
        'subject_position',
        'grade',
        'remark',
    ];

    protected $casts = [
        'exam_score' => 'decimal:2',
        'first_test_score' => 'decimal:2',
        'second_test_score' => 'decimal:2',
        'total_score' => 'decimal:2',
    ];

    public function studentResult()
    {
        return $this->belongsTo(StudentResult::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}

