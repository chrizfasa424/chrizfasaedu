<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HostelRoom extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id', 'hostel_id', 'room_number', 'capacity',
        'occupied', 'floor', 'description', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function hostel() { return $this->belongsTo(Hostel::class); }
    public function students() { return $this->hasMany(Student::class); }

    public function isAvailable(): bool
    {
        return $this->occupied < $this->capacity;
    }
}
