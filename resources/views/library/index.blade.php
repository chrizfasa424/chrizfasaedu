@extends('layouts.app')
@section('title', 'Library')
@section('header', 'Library')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Library</h1>
            <p class="text-sm text-slate-500 mt-0.5">Manage books and borrowing records.</p>
        </div>
        <button onclick="document.getElementById('add-book-modal').classList.remove('hidden')"
            class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-indigo-700">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Add Book
        </button>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
    @endif

    {{-- Search --}}
    <form method="GET" action="{{ route('library.index') }}" class="flex gap-3 items-end">
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Title or author..."
                class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 w-60">
        </div>
        <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Search</button>
        @if(request('search'))
        <a href="{{ route('library.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">Clear</a>
        @endif
    </form>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-5 py-3 text-left">Title</th>
                    <th class="px-5 py-3 text-left">Author</th>
                    <th class="px-5 py-3 text-left">ISBN</th>
                    <th class="px-5 py-3 text-left">Category</th>
                    <th class="px-5 py-3 text-center">Total</th>
                    <th class="px-5 py-3 text-center">Available</th>
                    <th class="px-5 py-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($books as $book)
                <tr class="hover:bg-slate-50" id="book-{{ $book->id }}">
                    <td class="px-5 py-3 font-medium text-slate-800">{{ $book->title }}</td>
                    <td class="px-5 py-3 text-slate-600">{{ $book->author }}</td>
                    <td class="px-5 py-3 font-mono text-xs text-slate-500">{{ $book->isbn ?? '—' }}</td>
                    <td class="px-5 py-3 text-slate-600">{{ $book->category ?? '—' }}</td>
                    <td class="px-5 py-3 text-center text-slate-700">{{ $book->total_copies }}</td>
                    <td class="px-5 py-3 text-center">
                        <span class="{{ $book->available_copies > 0 ? 'text-green-600 font-semibold' : 'text-red-500' }}">{{ $book->available_copies }}</span>
                    </td>
                    <td class="px-5 py-3">
                        @if($book->available_copies > 0)
                        <button onclick="openBorrow({{ $book->id }}, '{{ addslashes($book->title) }}')"
                            class="text-xs text-indigo-600 hover:underline font-medium">Issue</button>
                        @else
                        <span class="text-xs text-slate-400">Unavailable</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-5 py-10 text-center text-slate-400">No books found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $books->links() }}</div>

</div>

{{-- Add Book Modal --}}
<div id="add-book-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-lg font-bold text-slate-900">Add Book</h2>
            <button onclick="document.getElementById('add-book-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('library.store') }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div class="col-span-2">
                    <label class="block text-xs font-medium text-slate-600 mb-1">Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-medium text-slate-600 mb-1">Author <span class="text-red-500">*</span></label>
                    <input type="text" name="author" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">ISBN</label>
                    <input type="text" name="isbn" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Category</label>
                    <input type="text" name="category" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Publisher</label>
                    <input type="text" name="publisher" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Copies</label>
                    <input type="number" name="total_copies" value="1" min="1" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('add-book-modal').classList.add('hidden')"
                    class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Cancel</button>
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Add Book</button>
            </div>
        </form>
    </div>
</div>

{{-- Borrow Modal --}}
<div id="borrow-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
    <div class="w-full max-w-sm rounded-2xl bg-white p-6 shadow-xl">
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-lg font-bold text-slate-900">Issue Book</h2>
            <button onclick="document.getElementById('borrow-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <p id="borrow-book-title" class="text-sm font-medium text-slate-700 mb-4"></p>
        <form method="POST" action="{{ route('library.borrow') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="book_id" id="borrow-book-id">
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Borrower Type</label>
                <select name="borrower_type" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    <option value="student">Student</option>
                    <option value="staff">Staff</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Borrower ID <span class="text-red-500">*</span></label>
                <input type="number" name="borrower_id" required placeholder="Student/Staff ID"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Due Date <span class="text-red-500">*</span></label>
                <input type="date" name="due_date" required
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('borrow-modal').classList.add('hidden')"
                    class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Cancel</button>
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Issue</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openBorrow(id, title) {
    document.getElementById('borrow-book-id').value = id;
    document.getElementById('borrow-book-title').textContent = title;
    document.getElementById('borrow-modal').classList.remove('hidden');
}
</script>
@endpush
@endsection
