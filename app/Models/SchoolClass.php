<?php

namespace App\Models;

use App\Enums\GradeLevel;
use App\Traits\BelongsToSchool;
use App\Traits\HasAuditTrail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolClass extends Model
{
    use HasFactory, BelongsToSchool, HasAuditTrail;

    protected $table = 'classes';

    protected $fillable = [
        'school_id', 'name', 'grade_level', 'section', 'capacity',
        'class_teacher_id', 'is_active', 'order',
    ];

    protected $casts = [
        'grade_level' => GradeLevel::class,
        'is_active' => 'boolean',
    ];

    public function arms()
    {
        return $this->hasMany(ClassArm::class, 'class_id');
    }

    public function classTeacher()
    {
        return $this->belongsTo(Staff::class, 'class_teacher_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'class_id');
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'class_subject', 'class_id', 'subject_id')
            ->withPivot('teacher_id');
    }

    public function timetables()
    {
        return $this->hasMany(Timetable::class, 'class_id');
    }
}
