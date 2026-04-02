<?php

namespace App\Models;

use App\Enums\Term;
use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicTerm extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id', 'session_id', 'name', 'term', 'start_date', 'end_date', 'is_current',
    ];

    protected $casts = [
        'term' => Term::class,
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
    ];

    public function session()
    {
        return $this->belongsTo(AcademicSession::class, 'session_id');
    }
}
