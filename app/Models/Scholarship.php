<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scholarship extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id', 'name', 'description', 'type', // percentage, fixed
        'value', 'criteria', 'session_id', 'is_active',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'criteria' => 'array',
        'is_active' => 'boolean',
    ];

    public function students()
    {
        return $this->belongsToMany(Student::class, 'scholarship_student')
            ->withPivot('session_id', 'approved_by', 'status')
            ->withTimestamps();
    }
}
