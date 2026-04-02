<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffAttendance extends Model
{
    use HasFactory, BelongsToSchool;

    protected $table = 'staff_attendances';

    protected $fillable = [
        'school_id', 'staff_id', 'date',
        'status', 'check_in_time', 'check_out_time',
        'remark', 'recorded_by',
    ];

    protected $casts = [
        'date' => 'date',
        'check_in_time' => 'datetime',
        'check_out_time' => 'datetime',
    ];

    public function staff() { return $this->belongsTo(Staff::class); }
}
