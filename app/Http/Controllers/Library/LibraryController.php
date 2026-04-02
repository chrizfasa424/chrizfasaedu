<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use App\Models\LibraryBook;
use App\Models\BookBorrowing;
use Illuminate\Http\Request;

class LibraryController extends Controller
{
    public function index(Request $request)
    {
        $query = LibraryBook::query();
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('title', 'like', "%{$search}%")->orWhere('author', 'like', "%{$search}%");
        }
        $books = $query->latest()->paginate(20);
        return view('library.index', compact('books'));
    }

    public function store(Request $request)
    {
        LibraryBook::create($request->validate([
            'title' => 'required|string',
            'author' => 'required|string',
            'isbn' => 'nullable|string',
            'publisher' => 'nullable|string',
            'category' => 'nullable|string',
            'total_copies' => 'integer|min:1',
        ]));
        return back()->with('success', 'Book added to library.');
    }

    public function borrow(Request $request)
    {
        $validated = $request->validate([
            'book_id' => 'required|exists:library_books,id',
            'borrower_id' => 'required',
            'borrower_type' => 'required|in:student,staff',
            'due_date' => 'required|date|after:today',
        ]);

        $book = LibraryBook::findOrFail($validated['book_id']);
        abort_if($book->available_copies < 1, 422, 'No copies available.');

        BookBorrowing::create([
            'school_id' => auth()->user()->school_id,
            'book_id' => $book->id,
            'borrower_id' => $validated['borrower_id'],
            'borrower_type' => $validated['borrower_type'] === 'student' ? 'App\\Models\\Student' : 'App\\Models\\Staff',
            'borrowed_date' => now(),
            'due_date' => $validated['due_date'],
            'issued_by' => auth()->id(),
        ]);

        $book->decrement('available_copies');
        return back()->with('success', 'Book issued.');
    }

    public function returnBook(BookBorrowing $borrowing)
    {
        $borrowing->update(['returned_date' => now(), 'status' => 'returned']);
        $borrowing->book->increment('available_copies');
        return back()->with('success', 'Book returned.');
    }
}
