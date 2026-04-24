<?php

namespace App\Traits;

use App\Models\School;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

trait BelongsToSchool
{
    public static function bootBelongsToSchool(): void
    {
        static::creating(function ($model) {
            $schoolId = static::resolveAuthenticatedSchoolId();

            if ($schoolId && !$model->school_id) {
                $model->school_id = $schoolId;
            }
        });

        static::addGlobalScope('school', function (Builder $builder) {
            $schoolId = static::resolveAuthenticatedSchoolId();

            if ($schoolId) {
                $builder->where($builder->getModel()->getTable() . '.school_id', $schoolId);
            }
        });
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    protected static function resolveAuthenticatedSchoolId(): ?int
    {
        $user = Auth::user();

        if (!$user) {
            $configuredGuards = array_keys((array) config('auth.guards', []));
            $candidateGuards = array_values(array_unique(array_merge(
                ['web', 'portal', 'sanctum'],
                $configuredGuards
            )));

            foreach ($candidateGuards as $guard) {
                try {
                    $guardedUser = Auth::guard($guard)->user();
                } catch (\InvalidArgumentException) {
                    continue;
                }

                if ($guardedUser) {
                    $user = $guardedUser;
                    break;
                }
            }
        }

        $schoolId = (int) ($user?->school_id ?? 0);

        return $schoolId > 0 ? $schoolId : null;
    }
}
