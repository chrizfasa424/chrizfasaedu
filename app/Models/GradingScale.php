<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GradingScale extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'section',
        'class_id',
        'min_score',
        'max_score',
        'grade',
        'point',
        'remark',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'min_score' => 'decimal:2',
        'max_score' => 'decimal:2',
        'point' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }
}

