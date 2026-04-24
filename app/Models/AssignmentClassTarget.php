<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignmentClassTarget extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_id',
        'class_id',
        'arm_id',
        'subject_id',
    ];

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
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
        return $this->belongsTo(Subject::class, 'subject_id');
    }
}
