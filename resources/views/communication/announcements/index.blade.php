@extends('layouts.app')
@section('title', 'Announcements')
@section('header', 'Announcements')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Announcements</h1>
            <p class="text-sm text-slate-500 mt-0.5">Broadcast messages to students, parents, and staff.</p>
        </div>
        <button onclick="document.getElementById('create-modal').classList.remove('hidden')"
            class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-indigo-700">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            New Announcement
        </button>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
    @endif

    <div class="space-y-4">
        @forelse($announcements as $ann)
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between gap-4">
                <div class="flex-1">
                    <div class="flex flex-wrap items-center gap-2 mb-2">
                        <h3 class="text-sm font-semibold text-slate-900">{{ $ann->title }}</h3>
                        <span class="rounded-full px-2 py-0.5 text-xs font-medium
                            @if($ann->priority === 'urgent') bg-red-100 text-red-700
                            @elseif($ann->priority === 'high') bg-orange-100 text-orange-700
                            @elseif($ann->priority === 'normal') bg-blue-100 text-blue-700
                            @else bg-slate-100 text-slate-600 @endif capitalize">
                            {{ $ann->priority ?? 'normal' }}
                        </span>
                        <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-600 capitalize">{{ $ann->type }}</span>
                        <span class="rounded-full bg-indigo-50 px-2 py-0.5 text-xs font-medium text-indigo-600 capitalize">→ {{ $ann->audience }}</span>
                    </div>
                    <p class="text-sm text-slate-600 leading-relaxed">{{ Str::limit($ann->body, 200) }}</p>
                    <p class="mt-2 text-xs text-slate-400">
                        By {{ $ann->creator?->name ?? 'System' }} · {{ $ann->published_at ? \Carbon\Carbon::parse($ann->published_at)->diffForHumans() : '' }}
                    </p>
                </div>
                <form method="POST" action="{{ route('announcements.destroy', $ann) }}" onsubmit="return confirm('Delete this announcement?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-xs text-red-400 hover:text-red-600">Delete</button>
                </form>
            </div>
        </div>
        @empty
        <div class="rounded-2xl border border-dashed border-slate-300 bg-white py-12 text-center text-slate-400">
            No announcements yet.
        </div>
        @endforelse
    </div>

    <div>{{ $announcements->links() }}</div>

</div>

{{-- Create Modal --}}
<div id="create-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
    <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl">
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-lg font-bold text-slate-900">New Announcement</h2>
            <button onclick="document.getElementById('create-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('announcements.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Title <span class="text-red-500">*</span></label>
                <input type="text" name="title" required
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Message <span class="text-red-500">*</span></label>
                <textarea name="body" required rows="4"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400"></textarea>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Type <span class="text-red-500">*</span></label>
                    <select name="type" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        @foreach(['general','academic','financial','event'] as $t)
                        <option value="{{ $t }}">{{ ucfirst($t) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Audience <span class="text-red-500">*</span></label>
                    <select name="audience" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        @foreach(['all','students','parents','staff'] as $a)
                        <option value="{{ $a }}">{{ ucfirst($a) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Priority</label>
                <select name="priority" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    @foreach(['low','normal','high','urgent'] as $p)
                    <option value="{{ $p }}">{{ ucfirst($p) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('create-modal').classList.add('hidden')"
                    class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Cancel</button>
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Publish</button>
            </div>
        </form>
    </div>
</div>
@endsection
