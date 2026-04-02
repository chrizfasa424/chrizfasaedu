<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timetable extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id', 'class_id', 'arm_id', 'subject_id', 'teacher_id',
        'session_id', 'term_id',
        'day_of_week', // monday-friday
        'start_time', 'end_time',
        'room', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function schoolClass() { return $this->belongsTo(SchoolClass::class, 'class_id'); }
    public function arm() { return $this->belongsTo(ClassArm::class, 'arm_id'); }
    public function subject() { return $this->belongsTo(Subject::class); }
    public function teacher() { return $this->belongsTo(Staff::class, 'teacher_id'); }
}
