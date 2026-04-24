<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamType extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'name',
        'slug',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function studentResults()
    {
        return $this->hasMany(StudentResult::class);
    }

    public function resultBatches()
    {
        return $this->hasMany(ResultBatch::class);
    }
}

