<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use App\Traits\HasAuditTrail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeStructure extends Model
{
    use HasFactory, BelongsToSchool, HasAuditTrail;

    protected $fillable = [
        'school_id', 'session_id', 'term_id', 'class_id',
        'name', 'category', // tuition, development_levy, ict, uniform, exam, pta, transport, hostel
        'amount', 'description',
        'is_compulsory', 'is_active',
        'due_date', 'late_fee_amount', 'late_fee_after_days',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'late_fee_amount' => 'decimal:2',
        'is_compulsory' => 'boolean',
        'is_active' => 'boolean',
        'due_date' => 'date',
    ];

    public function session() { return $this->belongsTo(AcademicSession::class, 'session_id'); }
    public function term() { return $this->belongsTo(AcademicTerm::class, 'term_id'); }
    public function schoolClass() { return $this->belongsTo(SchoolClass::class, 'class_id'); }

    public function scopeForClass($query, int $classId)
    {
        return $query->where('class_id', $classId)->orWhereNull('class_id');
    }
}
