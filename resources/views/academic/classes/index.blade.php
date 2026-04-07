@extends('layouts.app')
@section('title', 'Classes')
@section('header', 'Classes')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Classes</h1>
            <p class="text-sm text-slate-500 mt-0.5">Manage school classes and arms.</p>
        </div>
        <button onclick="document.getElementById('create-class-modal').classList.remove('hidden')"
            class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-indigo-700">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Add Class
        </button>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($classes as $class)
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <h3 class="font-bold text-slate-800">{{ $class->name }}</h3>
                    <p class="text-xs text-slate-500 mt-0.5">{{ $class->grade_level?->label() }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('academic.classes.show', $class) }}" class="text-xs text-indigo-600 hover:underline font-medium">View</a>
                    <button type="button"
                        onclick="openEditModal({{ $class->id }}, '{{ addslashes($class->name) }}', '{{ $class->grade_level?->value }}', {{ $class->capacity ?? 40 }}, '{{ $class->arms->pluck('name')->join(', ') }}')"
                        class="text-xs text-amber-600 hover:text-amber-800 font-medium">Edit</button>
                    @if($classes->total() > 1)
                    <form method="POST" action="{{ route('academic.classes.destroy', $class) }}"
                          onsubmit="return confirm('Delete {{ addslashes($class->name) }}? This cannot be undone.')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-medium">Delete</button>
                    </form>
                    @endif
                </div>
            </div>
            <div class="mt-4 grid grid-cols-3 gap-2 text-center text-xs">
                <div class="rounded-lg bg-slate-50 p-2">
                    <div class="font-semibold text-slate-800">{{ $class->students_count ?? $class->students->count() }}</div>
                    <div class="text-slate-400">Students</div>
                </div>
                <div class="rounded-lg bg-slate-50 p-2">
                    <div class="font-semibold text-slate-800">{{ $class->arms->count() }}</div>
                    <div class="text-slate-400">Arms</div>
                </div>
                <div class="rounded-lg bg-slate-50 p-2">
                    <div class="font-semibold text-slate-800">{{ $class->capacity ?? '—' }}</div>
                    <div class="text-slate-400">Capacity</div>
                </div>
            </div>
            @if($class->arms->count())
            <div class="mt-3 flex flex-wrap gap-1">
                @foreach($class->arms as $arm)
                <span class="rounded-full bg-indigo-50 px-2 py-0.5 text-xs font-medium text-indigo-700">{{ $arm->name }}</span>
                @endforeach
            </div>
            @endif
        </div>
        @empty
        <div class="col-span-3 rounded-2xl border border-dashed border-slate-300 bg-white py-16 text-center text-slate-400">
            No classes yet. Add one to get started.
        </div>
        @endforelse
    </div>

    <div>{{ $classes->links() }}</div>

</div>

{{-- Create Class Modal --}}
<div id="create-class-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-lg font-bold text-slate-900">Add Class</h2>
            <button onclick="document.getElementById('create-class-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('academic.classes.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Class Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" placeholder="e.g. JSS 1" required
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Grade Level <span class="text-red-500">*</span></label>
                <select name="grade_level" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    <option value="">Select Grade Level</option>
                    @foreach(\App\Enums\GradeLevel::cases() as $level)
                    <option value="{{ $level->value }}">{{ $level->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Capacity</label>
                    <input type="number" name="capacity" min="1" value="40"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Arms (comma-separated)</label>
                <input type="text" name="arms" placeholder="e.g. A, B, C"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400"
                    onchange="this.value = this.value.split(',').map(s=>s.trim()).filter(Boolean).join(', ')">
                <p class="mt-1 text-xs text-slate-400">Leave empty for no arms.</p>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('create-class-modal').classList.add('hidden')"
                    class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Cancel</button>
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Create</button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Class Modal --}}
<div id="edit-class-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-lg font-bold text-slate-900">Edit Class</h2>
            <button onclick="document.getElementById('edit-class-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="" class="space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Class Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" required
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Grade Level <span class="text-red-500">*</span></label>
                <select name="grade_level" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    <option value="">Select Grade Level</option>
                    @foreach(\App\Enums\GradeLevel::cases() as $level)
                    <option value="{{ $level->value }}">{{ $level->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Capacity</label>
                <input type="number" name="capacity" min="1"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Arms (comma-separated)</label>
                <input type="text" name="arms" placeholder="e.g. A, B, C"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                <p class="mt-1 text-xs text-slate-400">Removing an arm here will delete it permanently.</p>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('edit-class-modal').classList.add('hidden')"
                    class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Cancel</button>
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Save Changes</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
// Create form: convert arms input to hidden array inputs
document.getElementById('create-class-modal').querySelector('form').addEventListener('submit', function() {
    const armsInput = this.querySelector('[name="arms"]');
    const val = armsInput.value.trim();
    if (val) {
        const arms = val.split(',').map(s => s.trim()).filter(Boolean);
        armsInput.remove();
        arms.forEach(arm => {
            const inp = document.createElement('input');
            inp.type = 'hidden'; inp.name = 'arms[]'; inp.value = arm;
            this.appendChild(inp);
        });
    }
});

// Edit form: convert arms input to hidden array inputs
document.getElementById('edit-class-modal').querySelector('form').addEventListener('submit', function() {
    const armsInput = this.querySelector('[name="arms"]');
    const val = armsInput.value.trim();
    // Remove any previously injected hidden arms[] inputs
    this.querySelectorAll('input[name="arms[]"]').forEach(el => el.remove());
    if (val) {
        const arms = val.split(',').map(s => s.trim()).filter(Boolean);
        armsInput.remove();
        arms.forEach(arm => {
            const inp = document.createElement('input');
            inp.type = 'hidden'; inp.name = 'arms[]'; inp.value = arm;
            this.appendChild(inp);
        });
    } else {
        armsInput.remove();
    }
});

function openEditModal(id, name, gradeLevel, capacity, arms) {
    const modal = document.getElementById('edit-class-modal');
    const form  = modal.querySelector('form');
    form.action = '/academic/classes/' + id;
    form.querySelector('[name="name"]').value        = name;
    form.querySelector('[name="grade_level"]').value = gradeLevel;
    form.querySelector('[name="capacity"]').value    = capacity;
    form.querySelector('[name="arms"]').value        = arms;
    modal.classList.remove('hidden');
}
</script>
@endpush
@endsection
