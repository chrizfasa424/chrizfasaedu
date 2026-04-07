@extends('layouts.app')
@section('title', 'Academic Sessions & Terms')
@section('header', 'Academic Sessions & Terms')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Sessions & Terms</h1>
            <p class="text-sm text-slate-500 mt-0.5">Manage academic sessions and set the current term.</p>
        </div>
        <button onclick="document.getElementById('create-session-modal').classList.remove('hidden')"
            class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-indigo-700">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            New Session
        </button>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    {{-- Sessions list --}}
    @forelse($sessions as $session)
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">

        {{-- Session header --}}
        <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-4 bg-slate-50 border-b border-slate-200">
            <div class="flex items-center gap-3">
                <button onclick="toggleSession('{{ $session->id }}')"
                    class="flex items-center gap-2 text-left group">
                    <svg id="chevron-{{ $session->id }}" class="h-4 w-4 text-slate-400 transition-transform rotate-180"
                        fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                    <div>
                        <span class="font-bold text-slate-800 group-hover:text-indigo-700">{{ $session->name }}</span>
                        <span class="ml-2 text-xs text-slate-400">
                            {{ $session->start_date?->format('d M Y') }} – {{ $session->end_date?->format('d M Y') }}
                        </span>
                    </div>
                </button>
                @if($session->is_current)
                    <span class="rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-semibold text-green-700">Current Session</span>
                @endif
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs text-slate-400">{{ $session->terms->count() }} term(s)</span>
                @unless($session->is_current)
                <form method="POST" action="{{ route('academic.sessions.set-current', $session) }}" class="inline">
                    @csrf
                    <button type="submit" class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-100">
                        Set Session Current
                    </button>
                </form>
                @endunless
                <button onclick="openAddTermModal({{ $session->id }})"
                    class="rounded-lg border border-indigo-300 bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-700 hover:bg-indigo-100">
                    + Add Term
                </button>
            </div>
        </div>

        {{-- Terms table --}}
        <div id="session-{{ $session->id }}">
            @if($session->terms->isEmpty())
                <div class="px-5 py-6 text-center text-sm text-slate-400">
                    No terms yet.
                    <button onclick="openAddTermModal({{ $session->id }})" class="ml-1 text-indigo-600 hover:underline font-medium">Add a term</button>
                </div>
            @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-white text-xs font-semibold uppercase tracking-wide text-slate-400">
                        <tr>
                            <th class="px-5 py-3 text-left">Term</th>
                            <th class="px-5 py-3 text-left">Start Date</th>
                            <th class="px-5 py-3 text-left">End Date</th>
                            <th class="px-5 py-3 text-left">Status</th>
                            <th class="px-5 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($session->terms->sortBy('start_date') as $term)
                        <tr class="hover:bg-slate-50">
                            <td class="px-5 py-3 font-semibold text-slate-800">{{ $term->name }}</td>
                            <td class="px-5 py-3 text-slate-600">{{ $term->start_date?->format('d M Y') ?? '—' }}</td>
                            <td class="px-5 py-3 text-slate-600">{{ $term->end_date?->format('d M Y') ?? '—' }}</td>
                            <td class="px-5 py-3">
                                @if($term->is_current)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-indigo-100 px-2.5 py-0.5 text-xs font-semibold text-indigo-700">
                                        <span class="h-1.5 w-1.5 rounded-full bg-indigo-500 inline-block"></span>
                                        Current Term
                                    </span>
                                @else
                                    <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-500">Inactive</span>
                                @endif
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-3">
                                    @unless($term->is_current)
                                    <form method="POST" action="{{ route('academic.terms.set-current', $term) }}" class="inline">
                                        @csrf
                                        <button type="submit"
                                            class="text-xs font-medium text-green-600 hover:text-green-800 hover:underline">
                                            Set Current
                                        </button>
                                    </form>
                                    @endunless
                                    <button onclick="openEditTermModal(
                                        {{ $term->id }},
                                        '{{ addslashes($term->name) }}',
                                        '{{ $term->start_date?->format('Y-m-d') }}',
                                        '{{ $term->end_date?->format('Y-m-d') }}')"
                                        class="text-xs font-medium text-indigo-600 hover:text-indigo-800 hover:underline">
                                        Edit
                                    </button>
                                    @unless($term->is_current)
                                    <form method="POST" action="{{ route('academic.terms.destroy', $term) }}"
                                          onsubmit="return confirm('Delete term \'{{ $term->name }}\'? This cannot be undone.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-xs font-medium text-red-500 hover:text-red-700 hover:underline">Delete</button>
                                    </form>
                                    @endunless
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

    </div>
    @empty
    <div class="rounded-2xl border border-dashed border-slate-300 bg-white py-14 text-center text-slate-400">
        <svg class="mx-auto mb-3 h-10 w-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 9v7.5"/>
        </svg>
        <p class="text-sm font-medium text-slate-500">No sessions yet.</p>
        <button onclick="document.getElementById('create-session-modal').classList.remove('hidden')"
            class="mt-3 text-sm text-indigo-600 hover:underline font-medium">Create your first session</button>
    </div>
    @endforelse

    <div>{{ $sessions->links() }}</div>

</div>

{{-- ── Create Session Modal ────────────────────────────────── --}}
<div id="create-session-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Create Academic Session</h2>
                <p class="text-xs text-slate-400 mt-0.5">3 terms will be created automatically.</p>
            </div>
            <button onclick="document.getElementById('create-session-modal').classList.add('hidden')"
                class="text-slate-400 hover:text-slate-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form method="POST" action="{{ route('academic.sessions.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Session Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" placeholder="e.g. 2025/2026" required
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                <p class="mt-1 text-xs text-slate-400">Use the format: 2025/2026</p>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Start Date <span class="text-red-500">*</span></label>
                    <input type="date" name="start_date" required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">End Date <span class="text-red-500">*</span></label>
                    <input type="date" name="end_date" required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('create-session-modal').classList.add('hidden')"
                    class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Cancel</button>
                <button type="submit"
                    class="rounded-lg bg-indigo-600 px-5 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Create Session</button>
            </div>
        </form>
    </div>
</div>

{{-- ── Add Term Modal ─────────────────────────────────────── --}}
<div id="add-term-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-lg font-bold text-slate-900">Add Term</h2>
            <button onclick="document.getElementById('add-term-modal').classList.add('hidden')"
                class="text-slate-400 hover:text-slate-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form id="add-term-form" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Term Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" placeholder="e.g. First Term" required
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Term Type <span class="text-red-500">*</span></label>
                <select name="term" required
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    <option value="first">First</option>
                    <option value="second">Second</option>
                    <option value="third">Third</option>
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Start Date <span class="text-red-500">*</span></label>
                    <input type="date" name="start_date" required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">End Date <span class="text-red-500">*</span></label>
                    <input type="date" name="end_date" required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('add-term-modal').classList.add('hidden')"
                    class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Cancel</button>
                <button type="submit"
                    class="rounded-lg bg-indigo-600 px-5 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Add Term</button>
            </div>
        </form>
    </div>
</div>

{{-- ── Edit Term Modal ─────────────────────────────────────── --}}
<div id="edit-term-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-lg font-bold text-slate-900">Edit Term</h2>
            <button onclick="document.getElementById('edit-term-modal').classList.add('hidden')"
                class="text-slate-400 hover:text-slate-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form id="edit-term-form" method="POST" class="space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Term Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="edit-term-name" required
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Start Date <span class="text-red-500">*</span></label>
                    <input type="date" name="start_date" id="edit-term-start" required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">End Date <span class="text-red-500">*</span></label>
                    <input type="date" name="end_date" id="edit-term-end" required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('edit-term-modal').classList.add('hidden')"
                    class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Cancel</button>
                <button type="submit"
                    class="rounded-lg bg-indigo-600 px-5 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Save Changes</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function toggleSession(id) {
    const el      = document.getElementById('session-' + id);
    const chevron = document.getElementById('chevron-' + id);
    const hidden  = el.classList.toggle('hidden');
    chevron.style.transform = hidden ? '' : 'rotate(180deg)';
}

function openAddTermModal(sessionId) {
    const form = document.getElementById('add-term-form');
    form.action = '{{ url("academic/sessions") }}/' + sessionId + '/terms';
    form.reset();
    document.getElementById('add-term-modal').classList.remove('hidden');
}

function openEditTermModal(termId, name, startDate, endDate) {
    document.getElementById('edit-term-name').value  = name;
    document.getElementById('edit-term-start').value = startDate;
    document.getElementById('edit-term-end').value   = endDate;
    document.getElementById('edit-term-form').action = '{{ url("academic/terms") }}/' + termId;
    document.getElementById('edit-term-modal').classList.remove('hidden');
}
</script>
@endpush
@endsection
