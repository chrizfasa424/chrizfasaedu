<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CmsPage extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id', 'title', 'slug', 'content', 'type', // page, news, event, blog
        'featured_image', 'gallery', 'event_date',
        'is_published', 'published_at', 'author_id', 'meta_description',
    ];

    protected $casts = [
        'gallery' => 'array',
        'event_date' => 'datetime',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function author() { return $this->belongsTo(User::class, 'author_id'); }
}
