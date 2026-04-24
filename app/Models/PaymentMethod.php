<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'code',
        'name',
        'is_active',
        'settings_json',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'settings_json' => 'array',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public static function availableForSchool(int $schoolId)
    {
        return static::query()
            ->where(function ($query) use ($schoolId) {
                $query->whereNull('school_id')
                    ->orWhere('school_id', $schoolId);
            })
            ->orderByRaw('case when school_id is null then 1 else 0 end')
            ->orderBy('name')
            ->get()
            ->groupBy('code')
            ->map(fn ($items) => $items->first())
            ->filter(fn ($method) => (bool) $method->is_active)
            ->values();
    }
}
