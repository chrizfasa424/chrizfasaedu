@extends('layouts.app')
@section('title', 'Subjects')
@section('header', 'Subjects')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Subjects</h1>
            <p class="text-sm text-slate-500 mt-0.5">Manage subjects and assign them to classes.</p>
        </div>
        <button onclick="document.getElementById('create-subject-modal').classList.remove('hidden')"
            class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-indigo-700">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Add Subject
        </button>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
    @endif

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-5 py-3 text-left">Subject</th>
                    <th class="px-5 py-3 text-left">Code</th>
                    <th class="px-5 py-3 text-left">Assigned Classes</th>
                    <th class="px-5 py-3 text-left">Type</th>
                    <th class="px-5 py-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($subjects as $subject)
                <tr class="hover:bg-slate-50">
                    <td class="px-5 py-3 font-medium text-slate-800">{{ $subject->name }}</td>
                    <td class="px-5 py-3 text-slate-600 font-mono text-xs">{{ $subject->code }}</td>
                    <td class="px-5 py-3">
                        @if($subject->classes->count())
                            <div class="flex flex-wrap gap-1">
                                @foreach($subject->classes as $cls)
                                    <span class="rounded-full bg-indigo-50 px-2 py-0.5 text-xs font-medium text-indigo-700">{{ $cls->grade_level?->label() ?? $cls->name }}</span>
                                @endforeach
                            </div>
                        @else
                            <span class="text-slate-400 text-xs">Not assigned</span>
                        @endif
                    </td>
                    <td class="px-5 py-3">
                        @if($subject->is_compulsory)
                            <span class="inline-flex rounded-full bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-700">Compulsory</span>
                        @else
                            <span class="inline-flex rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-600">Elective</span>
                        @endif
                    </td>
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            <button onclick="openEdit({{ $subject->id }}, '{{ addslashes($subject->name) }}', '{{ $subject->code }}', {{ $subject->is_compulsory ? 'true' : 'false' }}, [{{ $subject->classes->pluck('id')->join(',') }}])"
                                class="text-xs text-indigo-600 hover:underline font-medium">Edit</button>
                            <form method="POST" action="{{ route('academic.subjects.destroy', $subject) }}" onsubmit="return confirm('Delete this subject?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs text-red-500 hover:underline font-medium">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-5 py-10 text-center text-slate-400">No subjects yet. Add one to get started.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $subjects->links() }}</div>

</div>

{{-- Create Modal --}}
<div id="create-subject-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
    <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl">
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-lg font-bold text-slate-900">Add Subject</h2>
            <button onclick="document.getElementById('create-subject-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('academic.subjects.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Subject Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" required
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Code <span class="text-red-500">*</span></label>
                <input type="text" name="code" required placeholder="e.g. MTH"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-xs font-medium text-slate-600">Assign to Classes</label>
                    <label class="flex items-center gap-1.5 text-xs text-indigo-600 cursor-pointer font-medium">
                        <input type="checkbox" id="create-select-all" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-400"
                            onchange="toggleAll('create-class-cb', this.checked)">
                        Select All
                    </label>
                </div>
                <div class="grid grid-cols-2 gap-1.5 max-h-44 overflow-y-auto rounded-lg border border-slate-200 p-3">
                    @foreach($classes as $cls)
                    <label class="flex items-center gap-2 text-sm text-slate-700 cursor-pointer hover:text-indigo-700">
                        <input type="checkbox" name="class_ids[]" value="{{ $cls->id }}"
                            class="create-class-cb rounded border-slate-300 text-indigo-600 focus:ring-indigo-400"
                            onchange="syncSelectAll('create-class-cb', 'create-select-all')">
                        {{ $cls->grade_level?->label() ?? $cls->name }}
                    </label>
                    @endforeach
                </div>
                @if($classes->isEmpty())
                <p class="text-xs text-slate-400 mt-1">No classes found. Add classes first.</p>
                @endif
            </div>
            <label class="flex items-center gap-2 text-sm text-slate-700 cursor-pointer">
                <input type="checkbox" name="is_compulsory" value="1" class="rounded border-slate-300 text-indigo-600">
                Compulsory subject
            </label>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('create-subject-modal').classList.add('hidden')"
                    class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Cancel</button>
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Create</button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Modal --}}
<div id="edit-subject-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
    <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl">
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-lg font-bold text-slate-900">Edit Subject</h2>
            <button onclick="document.getElementById('edit-subject-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="edit-subject-form" method="POST" class="space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Subject Name <span class="text-red-500">*</span></label>
                <input type="text" id="edit-name" name="name" required
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Code <span class="text-red-500">*</span></label>
                <input type="text" id="edit-code" name="code" required
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-xs font-medium text-slate-600">Assign to Classes</label>
                    <label class="flex items-center gap-1.5 text-xs text-indigo-600 cursor-pointer font-medium">
                        <input type="checkbox" id="edit-select-all" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-400"
                            onchange="toggleAll('edit-class-cb', this.checked)">
                        Select All
                    </label>
                </div>
                <div id="edit-classes-list" class="grid grid-cols-2 gap-1.5 max-h-44 overflow-y-auto rounded-lg border border-slate-200 p-3">
                    @foreach($classes as $cls)
                    <label class="flex items-center gap-2 text-sm text-slate-700 cursor-pointer hover:text-indigo-700">
                        <input type="checkbox" name="class_ids[]" value="{{ $cls->id }}"
                            class="edit-class-cb rounded border-slate-300 text-indigo-600 focus:ring-indigo-400"
                            onchange="syncSelectAll('edit-class-cb', 'edit-select-all')">
                        {{ $cls->grade_level?->label() ?? $cls->name }}
                    </label>
                    @endforeach
                </div>
            </div>
            <label class="flex items-center gap-2 text-sm text-slate-700 cursor-pointer">
                <input type="checkbox" id="edit-compulsory" name="is_compulsory" value="1"
                    class="rounded border-slate-300 text-indigo-600">
                Compulsory subject
            </label>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('edit-subject-modal').classList.add('hidden')"
                    class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Cancel</button>
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Save Changes</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function toggleAll(cbClass, checked) {
    document.querySelectorAll('.' + cbClass).forEach(cb => cb.checked = checked);
}

function syncSelectAll(cbClass, selectAllId) {
    const all  = document.querySelectorAll('.' + cbClass);
    const done = [...all].every(cb => cb.checked);
    document.getElementById(selectAllId).checked = done;
}

function openEdit(id, name, code, compulsory, assignedClassIds) {
    document.getElementById('edit-name').value         = name;
    document.getElementById('edit-code').value         = code;
    document.getElementById('edit-compulsory').checked = compulsory;
    document.getElementById('edit-subject-form').action = '/academic/subjects/' + id;

    const cbs = document.querySelectorAll('.edit-class-cb');
    cbs.forEach(cb => { cb.checked = assignedClassIds.includes(parseInt(cb.value)); });

    // Sync the Select All checkbox
    document.getElementById('edit-select-all').checked = [...cbs].every(cb => cb.checked);

    document.getElementById('edit-subject-modal').classList.remove('hidden');
}
</script>
@endpush
@endsection
