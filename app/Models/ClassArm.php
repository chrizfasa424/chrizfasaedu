<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassArm extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id', 'class_id', 'name', 'capacity', 'arm_teacher_id',
    ];

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function teacher()
    {
        return $this->belongsTo(Staff::class, 'arm_teacher_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'arm_id');
    }
}
