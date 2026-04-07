@extends('layouts.app')
@section('title', 'Enter Scores')
@section('header', 'Enter Scores')

@section('content')
<div class="space-y-6 max-w-5xl">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('examination.results.index') }}" class="text-sm text-slate-500 hover:text-slate-700">← Results</a>
        <span class="text-slate-300">/</span>
        <span class="text-sm font-medium text-slate-800">Enter Scores</span>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    {{-- ── Selector Card ─────────────────────────────────────────────────── --}}
    <form method="GET" action="{{ route('examination.results.enter-scores') }}"
          class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

        <h3 class="text-sm font-semibold text-slate-700 mb-4">Select Class, Term &amp; Subject to Load Students</h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">

            {{-- Class --}}
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Class <span class="text-red-500">*</span></label>
                <select name="class_id" required
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    <option value="">Choose class…</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>
                            {{ $c->grade_level?->label() ?? $c->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Term --}}
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Term <span class="text-red-500">*</span></label>
                <select name="term_id" required
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    <option value="">Choose term…</option>
                    @foreach($terms as $t)
                        <option value="{{ $t->id }}"
                            {{ request('term_id') == $t->id || (!request('term_id') && $t->is_current) ? 'selected' : '' }}>
                            {{ $t->name }} — {{ $t->session->name ?? '' }}{{ $t->is_current ? ' (Current)' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Subject --}}
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Subject <span class="text-red-500">*</span></label>
                <select name="subject_id" required
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    <option value="">Choose subject…</option>
                    @foreach($subjects as $s)
                        <option value="{{ $s->id }}" {{ request('subject_id') == $s->id ? 'selected' : '' }}>
                            {{ $s->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Submit --}}
            <div>
                <button type="submit"
                    class="w-full rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 transition-colors">
                    Load Students
                </button>
            </div>

        </div>
    </form>

    {{-- ── Score Entry Table (only shown when students are loaded) ─────── --}}
    @if($students->isNotEmpty())

    @php
        $selectedClass   = $classes->firstWhere('id', request('class_id'));
        $selectedSubject = $subjects->firstWhere('id', request('subject_id'));
        $className       = $selectedClass?->grade_level?->label() ?? $selectedClass?->name ?? '—';
        $subjectName     = $selectedSubject?->name ?? '—';
        $termName        = $selectedTerm ? $selectedTerm->name . ' — ' . ($selectedTerm->session->name ?? '') : '—';
    @endphp

    <form method="POST" action="{{ route('examination.results.store-scores') }}">
        @csrf
        <input type="hidden" name="class_id"   value="{{ request('class_id') }}">
        <input type="hidden" name="subject_id" value="{{ request('subject_id') }}">
        <input type="hidden" name="term_id"    value="{{ request('term_id') }}">

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">

            {{-- Table Header --}}
            <div class="px-5 py-4 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h3 class="text-sm font-semibold text-slate-800">Score Entry</h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        <span class="font-medium text-indigo-600">{{ $className }}</span>
                        &nbsp;·&nbsp;
                        <span class="font-medium text-indigo-600">{{ $subjectName }}</span>
                        &nbsp;·&nbsp;
                        <span class="font-medium text-indigo-600">{{ $termName }}</span>
                    </p>
                </div>
                <div class="flex items-center gap-2 text-xs text-slate-500">
                    <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-1">
                        <svg class="h-3.5 w-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>
                        </svg>
                        {{ $students->count() }} students
                    </span>
                    <span class="inline-flex items-center gap-1 rounded-full bg-indigo-50 px-2.5 py-1 text-indigo-600 font-medium">
                        Max total: 100
                    </span>
                </div>
            </div>

            {{-- Score Table --}}
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50">
                        <tr class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <th class="px-5 py-3 text-left">#</th>
                            <th class="px-5 py-3 text-left">Student</th>
                            <th class="px-4 py-3 text-left text-slate-400 text-xs">Reg. No.</th>
                            <th class="px-4 py-3 text-center">
                                First Test
                                <span class="block text-slate-400 font-normal normal-case">(max 100)</span>
                            </th>
                            <th class="px-4 py-3 text-center">
                                Second Test
                                <span class="block text-slate-400 font-normal normal-case">(max 100)</span>
                            </th>
                            <th class="px-4 py-3 text-center">
                                Examination
                                <span class="block text-slate-400 font-normal normal-case">(max 100)</span>
                            </th>
                            <th class="px-4 py-3 text-center">Total</th>
                            <th class="px-4 py-3 text-center">Grade</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($students as $i => $student)
                        @php
                            $existing = $existingScores->get($student->id);
                            $exam  = $existing?->exam_score  ?? '';
                            $test1 = $existing?->ca1_score   ?? '';
                            $test2 = $existing?->ca2_score   ?? '';
                            $total = $existing?->total_score ?? 0;
                            $grade = $existing?->grade       ?? '—';

                            $gradeBadge = match($grade) {
                                'A'  => 'bg-emerald-100 text-emerald-700',
                                'B'  => 'bg-blue-100 text-blue-700',
                                'C'  => 'bg-indigo-100 text-indigo-700',
                                'D'  => 'bg-amber-100 text-amber-700',
                                'E'  => 'bg-red-100 text-red-700',
                                default => 'bg-slate-100 text-slate-500',
                            };
                        @endphp
                        <tr class="hover:bg-slate-50 transition-colors" id="row-{{ $i }}">
                            <td class="px-5 py-3 text-slate-400 text-xs">{{ $i + 1 }}</td>
                            <td class="px-5 py-3 font-medium text-slate-800">
                                {{ $student->full_name }}
                                <input type="hidden" name="scores[{{ $i }}][student_id]" value="{{ $student->id }}">
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-400">{{ $student->registration_number ?? $student->admission_number }}</td>

                            {{-- First Test --}}
                            <td class="px-4 py-3 text-center">
                                <input type="number"
                                    name="scores[{{ $i }}][first_test]"
                                    value="{{ $test1 }}"
                                    min="0" max="100" step="0.5"
                                    placeholder="0"
                                    class="w-20 rounded-lg border border-slate-300 px-2 py-1.5 text-sm text-center focus:outline-none focus:ring-2 focus:ring-indigo-400"
                                    oninput="calcRow({{ $i }})">
                            </td>

                            {{-- Second Test --}}
                            <td class="px-4 py-3 text-center">
                                <input type="number"
                                    name="scores[{{ $i }}][second_test]"
                                    value="{{ $test2 }}"
                                    min="0" max="100" step="0.5"
                                    placeholder="0"
                                    class="w-20 rounded-lg border border-slate-300 px-2 py-1.5 text-sm text-center focus:outline-none focus:ring-2 focus:ring-indigo-400"
                                    oninput="calcRow({{ $i }})">
                            </td>

                            {{-- Examination --}}
                            <td class="px-4 py-3 text-center">
                                <input type="number"
                                    name="scores[{{ $i }}][exam]"
                                    value="{{ $exam }}"
                                    min="0" max="100" step="0.5"
                                    placeholder="0"
                                    class="w-20 rounded-lg border border-slate-300 px-2 py-1.5 text-sm text-center focus:outline-none focus:ring-2 focus:ring-indigo-400"
                                    oninput="calcRow({{ $i }})">
                            </td>

                            {{-- Total --}}
                            <td class="px-4 py-3 text-center font-bold text-slate-800 tabular-nums" id="total-{{ $i }}">
                                {{ $existing ? number_format($total, 1) : '—' }}
                            </td>

                            {{-- Grade --}}
                            <td class="px-4 py-3 text-center" id="grade-{{ $i }}">
                                @if($existing)
                                    <span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $gradeBadge }}">{{ $grade }}</span>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Grade Key --}}
            <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/60 flex flex-wrap gap-3 text-xs text-slate-500">
                <span class="font-semibold text-slate-600 mr-1">Grade key:</span>
                <span class="rounded-full bg-emerald-100 text-emerald-700 px-2 py-0.5 font-semibold">A</span><span>70–100 · Excellent</span>
                <span class="rounded-full bg-blue-100 text-blue-700 px-2 py-0.5 font-semibold">B</span><span>60–69 · Very Good</span>
                <span class="rounded-full bg-indigo-100 text-indigo-700 px-2 py-0.5 font-semibold">C</span><span>50–59 · Good</span>
                <span class="rounded-full bg-amber-100 text-amber-700 px-2 py-0.5 font-semibold">D</span><span>40–49 · Pass</span>
                <span class="rounded-full bg-red-100 text-red-700 px-2 py-0.5 font-semibold">E</span><span>0–39 · Fail</span>
            </div>

            {{-- Actions --}}
            <div class="px-5 py-4 border-t border-slate-200 flex justify-between items-center">
                <a href="{{ route('examination.results.enter-scores') }}"
                   class="text-sm text-slate-500 hover:text-slate-700">← Change selection</a>
                <button type="submit"
                    class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 transition-colors shadow-sm">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    Save Scores
                </button>
            </div>

        </div>
    </form>

    @elseif(request()->hasAny(['class_id','term_id','subject_id']))

    {{-- Searched but no students found --}}
    <div class="rounded-2xl border border-amber-200 bg-amber-50 px-5 py-8 text-center text-sm text-amber-800">
        <svg class="mx-auto mb-3 h-8 w-8 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
        </svg>
        <p class="font-semibold mb-1">No active students found in this class.</p>
        <p class="text-amber-700">Make sure students are enrolled and marked <strong>active</strong> in the selected class.</p>
    </div>

    @else

    {{-- Initial state prompt --}}
    <div class="rounded-2xl border border-slate-200 bg-white px-5 py-12 text-center shadow-sm">
        <svg class="mx-auto mb-4 h-12 w-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
        </svg>
        <p class="text-sm font-semibold text-slate-600 mb-1">Select a class, term, and subject above</p>
        <p class="text-xs text-slate-400">The score entry table will appear here once you click <strong>Load Students</strong>.</p>
    </div>

    @endif

</div>

@push('scripts')
<script>
const gradeInfo = (total) => {
    if (total >= 70) return {g:'A', cls:'bg-emerald-100 text-emerald-700'};
    if (total >= 60) return {g:'B', cls:'bg-blue-100 text-blue-700'};
    if (total >= 50) return {g:'C', cls:'bg-indigo-100 text-indigo-700'};
    if (total >= 40) return {g:'D', cls:'bg-amber-100 text-amber-700'};
    return {g:'E', cls:'bg-red-100 text-red-700'};
};

function calcRow(i) {
    const row    = document.getElementById('row-' + i);
    const inputs = row.querySelectorAll('input[type=number]');
    let total = 0;
    inputs.forEach(inp => total += parseFloat(inp.value) || 0);
    total = Math.min(total, 300); // safety cap

    const totalCell = document.getElementById('total-' + i);
    const gradeCell = document.getElementById('grade-' + i);

    totalCell.textContent = total.toFixed(1);

    // Highlight total cell based on grade
    const {g, cls} = gradeInfo(total);
    gradeCell.innerHTML = `<span class="rounded-full px-2.5 py-1 text-xs font-bold ${cls}">${g}</span>`;

    // Color total cell
    totalCell.className = 'px-4 py-3 text-center font-bold tabular-nums ';
    if (total >= 70) totalCell.className += 'text-emerald-700';
    else if (total >= 60) totalCell.className += 'text-blue-700';
    else if (total >= 50) totalCell.className += 'text-indigo-700';
    else if (total >= 40) totalCell.className += 'text-amber-700';
    else totalCell.className += 'text-red-700';
}
</script>
@endpush
@endsection
