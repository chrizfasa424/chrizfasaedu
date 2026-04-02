<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAttendance extends Model
{
    use HasFactory, BelongsToSchool;

    protected $table = 'student_attendances';

    protected $fillable = [
        'school_id', 'student_id', 'class_id', 'arm_id',
        'session_id', 'term_id', 'date',
        'status', // present, absent, late, excused
        'check_in_time', 'check_out_time',
        'remark', 'recorded_by',
    ];

    protected $casts = [
        'date' => 'date',
        'check_in_time' => 'datetime',
        'check_out_time' => 'datetime',
    ];

    public function student() { return $this->belongsTo(Student::class); }
    public function schoolClass() { return $this->belongsTo(SchoolClass::class, 'class_id'); }
    public function recorder() { return $this->belongsTo(User::class, 'recorded_by'); }
}
