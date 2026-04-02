<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookBorrowing extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id', 'book_id', 'borrower_id', 'borrower_type',
        'borrowed_date', 'due_date', 'returned_date',
        'status', // borrowed, returned, overdue, lost
        'fine_amount', 'fine_paid', 'issued_by',
    ];

    protected $casts = [
        'borrowed_date' => 'date',
        'due_date' => 'date',
        'returned_date' => 'date',
        'fine_amount' => 'decimal:2',
        'fine_paid' => 'boolean',
    ];

    public function book() { return $this->belongsTo(LibraryBook::class, 'book_id'); }
    public function borrower() { return $this->morphTo(); }
}
