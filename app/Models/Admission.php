<?php

namespace App\Models;

use App\Enums\AdmissionStatus;
use App\Traits\BelongsToSchool;
use App\Traits\HasAuditTrail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Admission extends Model
{
    use HasFactory, BelongsToSchool, HasAuditTrail;

    protected $fillable = [
        'school_id', 'session_id', 'application_number',
        'first_name', 'last_name', 'other_names',
        'email', 'phone', 'gender', 'date_of_birth',
        'nationality', 'state_of_origin', 'lga',
        'address', 'city', 'state',
        'class_applied_for', 'previous_school',
        'parent_name', 'parent_phone', 'parent_email', 'parent_occupation',
        'photo', 'birth_certificate', 'previous_result', 'other_documents',
        'screening_score', 'screening_date',
        'status', 'reviewed_by', 'review_notes', 'reviewed_at',
        'admission_number',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'screening_date' => 'datetime',
        'reviewed_at' => 'datetime',
        'status' => AdmissionStatus::class,
        'other_documents' => 'array',
    ];

    public function session()
    {
        return $this->belongsTo(AcademicSession::class, 'session_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function student()
    {
        return $this->hasOne(Student::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', AdmissionStatus::PENDING);
    }

    public static function generateApplicationNumber(int $schoolId): string
    {
        $year   = date('Y');
        $prefix = "APP/{$year}/";

        // Find the highest sequence already used this year for this school
        $max = static::where('school_id', $schoolId)
            ->where('application_number', 'like', $prefix . '%')
            ->lockForUpdate()
            ->max(DB::raw(
                'CAST(SUBSTRING_INDEX(application_number, "/", -1) AS UNSIGNED)'
            ));

        $next = (int) $max + 1;

        // Collision-safety loop (handles concurrent inserts)
        $candidate = sprintf('%s%05d', $prefix, $next);
        while (static::where('application_number', $candidate)->exists()) {
            $next++;
            $candidate = sprintf('%s%05d', $prefix, $next);
        }

        return $candidate;
    }
}
