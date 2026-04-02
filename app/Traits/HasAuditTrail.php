<?php

namespace App\Traits;

use App\Models\AuditLog;

trait HasAuditTrail
{
    public static function bootHasAuditTrail(): void
    {
        static::created(function ($model) {
            self::logActivity('created', $model);
        });

        static::updated(function ($model) {
            self::logActivity('updated', $model, $model->getChanges());
        });

        static::deleted(function ($model) {
            self::logActivity('deleted', $model);
        });
    }

    protected static function logActivity(string $action, $model, array $changes = []): void
    {
        if (auth()->check()) {
            AuditLog::create([
                'school_id' => auth()->user()->school_id ?? null,
                'user_id' => auth()->id(),
                'action' => $action,
                'model_type' => get_class($model),
                'model_id' => $model->getKey(),
                'changes' => !empty($changes) ? json_encode($changes) : null,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }
    }
}
