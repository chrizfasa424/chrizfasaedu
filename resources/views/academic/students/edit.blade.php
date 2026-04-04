@extends('layouts.app')
@section('title', 'Edit Student')
@section('header', 'Edit Student')

@section('content')
<div class="space-y-6 max-w-3xl">

    <div class="flex items-center gap-3">
        <a href="{{ route('academic.students.show', $student) }}" class="text-sm text-slate-500 hover:text-slate-700">← {{ $student->full_name }}</a>
        <span class="text-slate-300">/</span>
        <span class="text-sm font-medium text-slate-800">Edit</span>
    </div>

    @if($errors->any())
    <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
        <ul class="list-disc list-inside space-y-0.5">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('academic.students.update', $student) }}" enctype="multipart/form-data" class="space-y-5">
            @csrf @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">First Name <span class="text-red-500">*</span></label>
                    <input type="text" name="first_name" value="{{ old('first_name', $student->first_name) }}" required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Last Name <span class="text-red-500">*</span></label>
                    <input type="text" name="last_name" value="{{ old('last_name', $student->last_name) }}" required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Gender <span class="text-red-500">*</span></label>
                    <select name="gender" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        <option value="male" {{ old('gender', $student->gender) === 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender', $student->gender) === 'female' ? 'selected' : '' }}>Female</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Date of Birth <span class="text-red-500">*</span></label>
                    <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $student->date_of_birth?->format('Y-m-d')) }}" required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Status</label>
                    <select name="status" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        @foreach(['active','graduated','transferred','expelled','withdrawn'] as $s)
                        <option value="{{ $s }}" {{ old('status', $student->status) === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Class <span class="text-red-500">*</span></label>
                    <select name="class_id" required id="class-select"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ old('class_id', $student->class_id) == $c->id ? 'selected' : '' }}
                            data-arms="{{ $c->arms->map(fn($a) => ['id'=>$a->id,'name'=>$a->name])->toJson() }}">
                            {{ $c->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Arm</label>
                    <select name="arm_id" id="arm-select" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        <option value="">No arm</option>
                        @foreach($classes->firstWhere('id', $student->class_id)?->arms ?? [] as $arm)
                        <option value="{{ $arm->id }}" {{ old('arm_id', $student->arm_id) == $arm->id ? 'selected' : '' }}>{{ $arm->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Address</label>
                <textarea name="address" rows="2" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">{{ old('address', $student->address) }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Photo</label>
                @if($student->photo)
                <img src="{{ asset('storage/'.$student->photo) }}" class="h-16 w-16 rounded-full object-cover mb-2">
                @endif
                <input type="file" name="photo" accept="image/*"
                    class="block text-sm text-slate-600 file:mr-3 file:rounded-lg file:border file:border-slate-300 file:bg-white file:px-3 file:py-1.5 file:text-sm file:font-medium hover:file:bg-slate-50">
            </div>

            <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                <a href="{{ route('academic.students.show', $student) }}" class="rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">Cancel</a>
                <button type="submit" class="rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">Save Changes</button>
            </div>
        </form>
    </div>

</div>

@push('scripts')
<script>
document.getElementById('class-select').addEventListener('change', function() {
    const sel = this.options[this.selectedIndex];
    const arms = JSON.parse(sel.dataset.arms || '[]');
    const armSel = document.getElementById('arm-select');
    armSel.innerHTML = '<option value="">No arm</option>';
    arms.forEach(a => {
        armSel.innerHTML += `<option value="${a.id}">${a.name}</option>`;
    });
});
</script>
@endpush
@endsection
