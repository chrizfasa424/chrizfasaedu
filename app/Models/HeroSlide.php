<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HeroSlide extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'title',
        'subtitle',
        'badge_text',
        'button_1_text',
        'button_1_link',
        'button_2_text',
        'button_2_link',
        'right_card_title',
        'right_card_text',
        'school_name',
        'image_path',
        'order',
        'is_active',
    ];

    protected $casts = [
        'order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getImageUrlAttribute(): string
    {
        return asset('storage/' . ltrim((string) $this->image_path, '/'));
    }
}
