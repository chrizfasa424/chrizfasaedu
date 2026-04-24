<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolHoliday extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'session_id',
        'term_id',
        'name',
        'holiday_date',
        'is_public',
        'created_by',
    ];

    protected $casts = [
        'holiday_date' => 'date',
        'is_public' => 'boolean',
    ];

    public function session()
    {
        return $this->belongsTo(AcademicSession::class, 'session_id');
    }

    public function term()
    {
        return $this->belongsTo(AcademicTerm::class, 'term_id');
    }
}

