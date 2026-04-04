<?php

namespace App\Support;

use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;

class SchoolContext
{
    public static function isSingleSchoolMode(): bool
    {
        return (bool) config('brand.single_school_mode', true);
    }

    public static function current(?Request $request = null): ?School
    {
        if (auth()->check() && auth()->user()?->school) {
            return auth()->user()->school;
        }

        $configuredSchoolId = config('brand.default_school_id');

        if ($configuredSchoolId) {
            $configuredSchool = School::query()
                ->where('is_active', true)
                ->find($configuredSchoolId);

            if ($configuredSchool) {
                return $configuredSchool;
            }
        }

        return School::query()
            ->where('is_active', true)
            ->orderBy('id')
            ->first();
    }

    public static function ensureUserSchool(User $user): void
    {
        if (!$user->school_id && self::isSingleSchoolMode()) {
            $school = self::current();

            if ($school) {
                $user->forceFill(['school_id' => $school->id])->save();
                $user->unsetRelation('school');
            }
        }
    }
}
