<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalRecord extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id', 'student_id', 'type', // checkup, clinic_visit, emergency, vaccination
        'title', 'description', 'diagnosis', 'treatment',
        'medications', 'doctor_name', 'visit_date',
        'follow_up_date', 'attachments', 'recorded_by',
    ];

    protected $casts = [
        'visit_date' => 'date',
        'follow_up_date' => 'date',
        'medications' => 'array',
        'attachments' => 'array',
    ];

    public function student() { return $this->belongsTo(Student::class); }
    public function recorder() { return $this->belongsTo(User::class, 'recorded_by'); }
}
