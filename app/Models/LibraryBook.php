<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LibraryBook extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id', 'title', 'author', 'isbn', 'publisher',
        'category', 'edition', 'publication_year',
        'total_copies', 'available_copies',
        'shelf_number', 'rack_number',
        'cover_image', 'description', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function borrowings() { return $this->hasMany(BookBorrowing::class, 'book_id'); }
}
