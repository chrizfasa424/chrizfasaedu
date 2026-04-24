<?php

namespace App\Traits;

use App\Models\School;
use Illuminate\Database\Eloquent\SoftDeletes;

trait GeneratesAdmissionNumber
{
    public static function generateAdmissionNumber(int $schoolId, ?string $prefix = null): string
    {
        $year = date('Y');
        $prefix = static::admissionPrefixForSchool($schoolId, $prefix);
        $sequence = static::nextAdmissionSequence($schoolId, $prefix, (string) $year);

        return sprintf('%s/%s/%04d', $prefix, $year, $sequence);
    }

    public static function admissionPrefixForSchool(int $schoolId, ?string $prefix = null): string
    {
        return static::resolveAdmissionPrefix($schoolId, $prefix);
    }

    public static function registrationNumberPatternForSchool(int $schoolId, ?string $prefix = null): string
    {
        $resolvedPrefix = preg_quote(static::admissionPrefixForSchool($schoolId, $prefix), '/');
        return '/^' . $resolvedPrefix . '\/\d{4}\/\d{4}$/i';
    }

    public static function isValidRegistrationNumberForSchool(string $value, int $schoolId, ?string $prefix = null): bool
    {
        $candidate = strtoupper(trim($value));
        if ($candidate === '') {
            return false;
        }

        return preg_match(static::registrationNumberPatternForSchool($schoolId, $prefix), $candidate) === 1;
    }

    protected static function resolveAdmissionPrefix(int $schoolId, ?string $prefix = null): string
    {
        $custom = static::normalizeAdmissionPrefix($prefix);
        if ($custom !== null) {
            return $custom;
        }

        $school = School::query()
            ->select(['id', 'name', 'code'])
            ->find($schoolId);

        $code = strtoupper(trim((string) ($school?->code ?? '')));
        if ($code !== '' && preg_match('/^[A-Z]{2,6}$/', $code) === 1) {
            return $code;
        }

        $namePrefix = static::initialsFromSchoolName((string) ($school?->name ?? ''));
        if ($namePrefix !== null) {
            return $namePrefix;
        }

        return 'EIS';
    }

    protected static function nextAdmissionSequence(int $schoolId, string $prefix, string $year): int
    {
        $pattern = $prefix . '/' . $year . '/%';
        $usesSoftDeletes = in_array(SoftDeletes::class, class_uses_recursive(static::class), true);
        $query = $usesSoftDeletes
            ? static::withTrashed()
            : static::query();

        $numbers = $query
            ->where('school_id', $schoolId)
            ->whereNotNull('admission_number')
            ->where('admission_number', 'like', $pattern)
            ->pluck('admission_number');

        $max = 0;
        foreach ($numbers as $admissionNumber) {
            if (preg_match('/\/(\d+)$/', (string) $admissionNumber, $matches) !== 1) {
                continue;
            }

            $max = max($max, (int) $matches[1]);
        }

        return $max + 1;
    }

    protected static function initialsFromSchoolName(string $name): ?string
    {
        $name = trim($name);
        if ($name === '') {
            return null;
        }

        $words = preg_split('/\s+/', $name) ?: [];
        $initials = '';

        foreach ($words as $word) {
            $clean = preg_replace('/[^a-z]/i', '', (string) $word) ?? '';
            if ($clean === '' || in_array(strtolower($clean), ['and', 'of', 'the'], true)) {
                continue;
            }

            $initials .= strtoupper($clean[0]);
            if (strlen($initials) >= 6) {
                break;
            }
        }

        if ($initials !== '') {
            return $initials;
        }

        $letters = strtoupper(preg_replace('/[^a-z]/i', '', $name) ?? '');
        if ($letters === '') {
            return null;
        }

        return substr($letters, 0, 6);
    }

    protected static function normalizeAdmissionPrefix(?string $value): ?string
    {
        $value = strtoupper(preg_replace('/[^a-z0-9]/i', '', (string) $value) ?? '');
        return $value !== '' ? $value : null;
    }
}
