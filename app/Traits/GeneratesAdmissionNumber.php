<?php

namespace App\Traits;

trait GeneratesAdmissionNumber
{
    public static function generateAdmissionNumber(int $schoolId, string $prefix = 'ADM'): string
    {
        $year = date('Y');
        $lastStudent = static::where('school_id', $schoolId)
            ->whereYear('created_at', $year)
            ->orderByDesc('id')
            ->first();

        $sequence = $lastStudent ? ((int) substr($lastStudent->admission_number, -4)) + 1 : 1;

        return sprintf('%s/%s/%04d', $prefix, $year, $sequence);
    }
}
