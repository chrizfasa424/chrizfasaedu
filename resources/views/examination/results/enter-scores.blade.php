@extends('layouts.app')
@section('title', 'Enter Scores')
@section('header', 'Enter Scores')

@section('content')
<div class="space-y-6 max-w-5xl">

    <div class="flex items-center gap-3">
        <a href="{{ route('examination.results.index') }}" class="text-sm text-slate-500 hover:text-slate-700">← Results</a>
        <span class="text-slate-300">/</span>
        <span class="text-sm font-medium text-slate-800">Enter Scores</span>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
    @endif

    @if($errors->any())
    <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
        <ul class="list-disc list-inside space-y-0.5">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    {{-- Class & Subject Selector --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <h3 class="text-sm font-semibold text-slate-700 mb-4">Select Class & Subject</h3>
        <div class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Class</label>
                <select id="class-select" onchange="loadSubjectsAndStudents()"
                    class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    <option value="">Choose class...</option>
                    @foreach($classes as $c)
                    <option value="{{ $c->id }}"
                        data-subjects="{{ $c->subjects->map(fn($s) => ['id'=>$s->id,'name'=>$s->name])->toJson() }}"
                        data-students="{{ $c->students->map(fn($st) => ['id'=>$st->id,'name'=>$st->full_name])->toJson() }}">
                        {{ $c->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Subject</label>
                <select id="subject-select"
                    class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    <option value="">Choose subject...</option>
                </select>
            </div>
            <button onclick="renderScoreTable()"
                class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Load Students</button>
        </div>
    </div>

    {{-- Score Entry Table --}}
    <div id="score-section" class="hidden">
        <form method="POST" action="{{ route('examination.results.store-scores') }}">
            @csrf
            <input type="hidden" name="class_id" id="form-class-id">
            <input type="hidden" name="subject_id" id="form-subject-id">

            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100">
                    <h3 class="text-sm font-semibold text-slate-700">Score Entry
                        <span class="font-normal text-slate-500 ml-2">CA1, CA2, CA3 (max 20 each) · Exam (max 60)</span>
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-xs font-semibold uppercase text-slate-500">
                            <tr>
                                <th class="px-5 py-3 text-left">Student</th>
                                <th class="px-4 py-3 text-center">CA1 <span class="font-normal text-slate-400">/20</span></th>
                                <th class="px-4 py-3 text-center">CA2 <span class="font-normal text-slate-400">/20</span></th>
                                <th class="px-4 py-3 text-center">CA3 <span class="font-normal text-slate-400">/20</span></th>
                                <th class="px-4 py-3 text-center">Exam <span class="font-normal text-slate-400">/60</span></th>
                                <th class="px-4 py-3 text-center">Total</th>
                            </tr>
                        </thead>
                        <tbody id="score-tbody" class="divide-y divide-slate-100"></tbody>
                    </table>
                </div>
                <div class="px-5 py-4 border-t border-slate-100 flex justify-end">
                    <button type="submit" class="rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">Save Scores</button>
                </div>
            </div>
        </form>
    </div>

</div>

@push('scripts')
<script>
let students = [];

function loadSubjectsAndStudents() {
    const sel = document.getElementById('class-select');
    const opt = sel.options[sel.selectedIndex];
    const subjects = JSON.parse(opt.dataset.subjects || '[]');
    students = JSON.parse(opt.dataset.students || '[]');

    const subjectSel = document.getElementById('subject-select');
    subjectSel.innerHTML = '<option value="">Choose subject...</option>';
    subjects.forEach(s => {
        subjectSel.innerHTML += `<option value="${s.id}">${s.name}</option>`;
    });
    document.getElementById('score-section').classList.add('hidden');
}

function renderScoreTable() {
    const classId = document.getElementById('class-select').value;
    const subjectId = document.getElementById('subject-select').value;
    if (!classId || !subjectId || !students.length) return;

    document.getElementById('form-class-id').value = classId;
    document.getElementById('form-subject-id').value = subjectId;

    let rows = '';
    students.forEach((st, i) => {
        rows += `
        <tr class="hover:bg-slate-50">
            <td class="px-5 py-2 font-medium text-slate-800">
                ${st.name}
                <input type="hidden" name="scores[${i}][student_id]" value="${st.id}">
            </td>
            <td class="px-4 py-2"><input type="number" name="scores[${i}][ca1]" min="0" max="20" step="0.5" class="w-16 rounded border border-slate-300 px-2 py-1 text-sm text-center focus:outline-none focus:ring-1 focus:ring-indigo-400" onchange="calcTotal(this, ${i})"></td>
            <td class="px-4 py-2"><input type="number" name="scores[${i}][ca2]" min="0" max="20" step="0.5" class="w-16 rounded border border-slate-300 px-2 py-1 text-sm text-center focus:outline-none focus:ring-1 focus:ring-indigo-400" onchange="calcTotal(this, ${i})"></td>
            <td class="px-4 py-2"><input type="number" name="scores[${i}][ca3]" min="0" max="20" step="0.5" class="w-16 rounded border border-slate-300 px-2 py-1 text-sm text-center focus:outline-none focus:ring-1 focus:ring-indigo-400" onchange="calcTotal(this, ${i})"></td>
            <td class="px-4 py-2"><input type="number" name="scores[${i}][exam]" min="0" max="60" step="0.5" class="w-16 rounded border border-slate-300 px-2 py-1 text-sm text-center focus:outline-none focus:ring-1 focus:ring-indigo-400" onchange="calcTotal(this, ${i})"></td>
            <td class="px-4 py-2 text-center font-semibold text-slate-700" id="total-${i}">—</td>
        </tr>`;
    });

    document.getElementById('score-tbody').innerHTML = rows;
    document.getElementById('score-section').classList.remove('hidden');
}

function calcTotal(el, i) {
    const row = el.closest('tr');
    const inputs = row.querySelectorAll('input[type=number]');
    let total = 0;
    inputs.forEach(inp => total += parseFloat(inp.value) || 0);
    document.getElementById(`total-${i}`).textContent = total;
}
</script>
@endpush
@endsection
