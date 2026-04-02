<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BehaviourRecord extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id', 'student_id', 'session_id', 'term_id',
        'type', // reward, sanction, observation
        'category', // punctuality, neatness, respect, bullying, etc
        'title', 'description', 'action_taken',
        'severity', // minor, moderate, major, critical
        'points', // positive or negative
        'reported_by', 'handled_by', 'parent_notified',
        'date_of_incident',
    ];

    protected $casts = [
        'date_of_incident' => 'date',
        'parent_notified' => 'boolean',
    ];

    public function student() { return $this->belongsTo(Student::class); }
    public function reporter() { return $this->belongsTo(User::class, 'reported_by'); }
    public function handler() { return $this->belongsTo(User::class, 'handled_by'); }
}
