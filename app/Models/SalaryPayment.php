<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryPayment extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id', 'staff_id', 'month', 'year',
        'basic_salary', 'allowances', 'deductions',
        'gross_salary', 'net_salary',
        'payment_method', 'payment_date', 'status',
        'approved_by', 'notes',
    ];

    protected $casts = [
        'basic_salary' => 'decimal:2',
        'allowances' => 'array',
        'deductions' => 'array',
        'gross_salary' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function staff() { return $this->belongsTo(Staff::class); }
    public function approver() { return $this->belongsTo(User::class, 'approved_by'); }
}
