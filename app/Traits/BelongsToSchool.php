<?php

namespace App\Traits;

use App\Models\School;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToSchool
{
    public static function bootBelongsToSchool(): void
    {
        static::creating(function ($model) {
            if (auth()->check() && !$model->school_id) {
                $model->school_id = auth()->user()->school_id;
            }
        });

        static::addGlobalScope('school', function (Builder $builder) {
            if (auth()->check() && auth()->user()->school_id) {
                $builder->where($builder->getModel()->getTable() . '.school_id', auth()->user()->school_id);
            }
        });
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }
}
