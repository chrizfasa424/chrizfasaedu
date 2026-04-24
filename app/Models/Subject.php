<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id', 'name', 'code', 'description', 'department',
        'is_compulsory', 'is_active', 'credit_unit',
    ];

    protected $casts = [
        'is_compulsory' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function classes()
    {
        return $this->belongsToMany(SchoolClass::class, 'class_subject', 'subject_id', 'class_id')
            ->withPivot('teacher_id');
    }

    public function assessments()
    {
        return $this->hasMany(Assessment::class);
    }

    public function studentResultItems()
    {
        return $this->hasMany(StudentResultItem::class);
    }
}
