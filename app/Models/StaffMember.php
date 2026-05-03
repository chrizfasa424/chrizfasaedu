<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class StaffMember extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'staffs';

    protected $fillable = [
        'school_id',
        'staff_id_number',
        'first_name',
        'last_name',
        'other_names',
        'email',
        'phone',
        'employee_type',
        'department',
        'designation',
        'qualification',
        'date_of_employment',
        'date_of_birth',
        'gender',
        'marital_status',
        'nationality',
        'state_of_origin',
        'address',
        'city',
        'state',
        'bank_name',
        'account_number',
        'account_name',
        'basic_salary',
        'allowances',
        'deductions',
        'photo',
        'resume',
        'status',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'date_of_employment' => 'date',
        'date_of_birth' => 'date',
        'basic_salary' => 'decimal:2',
        'allowances' => 'array',
        'deductions' => 'array',
        'password' => 'hashed',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }
}
