<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use App\Traits\HasAuditTrail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicSession extends Model
{
    use HasFactory, BelongsToSchool, HasAuditTrail;

    protected $fillable = [
        'school_id', 'name', 'slug', 'start_date', 'end_date',
        'is_current', 'is_locked',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
        'is_locked' => 'boolean',
    ];

    public function terms()
    {
        return $this->hasMany(AcademicTerm::class, 'session_id');
    }

    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }
}
