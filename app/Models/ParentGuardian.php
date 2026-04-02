<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParentGuardian extends Model
{
    use HasFactory, BelongsToSchool;

    protected $table = 'parents';

    protected $fillable = [
        'school_id', 'user_id', 'title', 'first_name', 'last_name',
        'email', 'phone', 'alt_phone', 'occupation', 'employer',
        'address', 'city', 'state', 'photo', 'relationship_type',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function children()
    {
        return $this->belongsToMany(Student::class, 'parent_student', 'parent_id', 'student_id')
            ->withPivot('relationship');
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->title} {$this->first_name} {$this->last_name}");
    }
}
