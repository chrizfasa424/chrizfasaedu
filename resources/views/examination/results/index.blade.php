@extends('layouts.app')
@section('title', 'Results')
@section('header', 'Examination Results')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Examination Results</h1>
            <p class="text-sm text-slate-500 mt-0.5">View, import, and manage student results.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('examination.result-sheets.class-sheet') }}"
                class="inline-flex items-center gap-2 rounded-xl border border-indigo-300 bg-indigo-50 px-4 py-2.5 text-sm font-semibold text-indigo-700 hover:bg-indigo-100">
                Result Sheet Module
            </a>
            <a href="{{ route('examination.results.import') }}"
                class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                Import CSV/Excel
            </a>
            @if(request('class_id') && request('term_id'))
            <a href="{{ route('examination.results.export', ['class_id' => request('class_id'), 'term_id' => request('term_id')]) }}"
                class="inline-flex items-center gap-2 rounded-xl border border-green-300 bg-green-50 px-4 py-2.5 text-sm font-semibold text-green-700 hover:bg-green-100">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                Download Result Sheet
            </a>
            @endif
            <a href="{{ route('examination.results.enter-scores') }}"
                class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-indigo-700">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/></svg>
                Enter Scores
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
    @endif

    {{-- Filter --}}
    <form method="GET" action="{{ route('examination.results.index') }}"
          class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm flex flex-wrap gap-4 items-end">
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Class</label>
            <select name="class_id" required class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                <option value="">Select class</option>
                @foreach($classes as $c)
                <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>{{ $c->grade_level?->label() ?? $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Term</label>
            <select name="term_id" required class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                <option value="">Select term</option>
                @foreach($terms as $t)
                <option value="{{ $t->id }}" {{ request('term_id') == $t->id ? 'selected' : '' }}>
                    {{ $t->name }} — {{ $t->session->name ?? '' }}{{ $t->is_current ? ' (Current)' : '' }}
                </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">View Results</button>
        @if(request()->hasAny(['class_id','term_id']))
        <a href="{{ route('examination.results.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">Clear</a>
        @endif
    </form>

    {{-- Actions (compute + approve) --}}
    @if(request('class_id') && request('term_id') && $results->count())
    <div class="flex flex-wrap gap-2">
        <form method="POST" action="{{ route('examination.results.compute') }}">
            @csrf
            <input type="hidden" name="class_id" value="{{ request('class_id') }}">
            <input type="hidden" name="term_id" value="{{ request('term_id') }}">
            <button type="submit" onclick="return confirm('Compute positions and generate report cards?')"
                class="rounded-lg border border-amber-300 bg-amber-50 px-4 py-2 text-sm font-medium text-amber-700 hover:bg-amber-100">
                Compute Positions & Report Cards
            </button>
        </form>
        <form method="POST" action="{{ route('examination.results.approve') }}">
            @csrf
            <input type="hidden" name="class_id" value="{{ request('class_id') }}">
            <input type="hidden" name="term_id" value="{{ request('term_id') }}">
            <button type="submit" onclick="return confirm('Approve and publish results to students?')"
                class="rounded-lg border border-green-300 bg-green-50 px-4 py-2 text-sm font-medium text-green-700 hover:bg-green-100">
                Approve & Publish to Students
            </button>
        </form>
    </div>
    @endif

    {{-- Results Table --}}
    @if($results->count())
    <div class="space-y-3">
        @foreach($results as $studentId => $studentResults)
        @php
            $student    = $studentResults->first()->student;
            $avg        = round($studentResults->avg('total_score'), 1);
            $total      = $studentResults->sum('total_score');
            $approved   = $studentResults->where('is_approved', true)->count() === $studentResults->count();
            $reportCard = \App\Models\ReportCard::where('student_id', $studentId)
                            ->where('term_id', request('term_id'))->first();
        @endphp
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            {{-- Student header row --}}
            <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-3 bg-slate-50 border-b border-slate-200 cursor-pointer"
                 onclick="toggleRow('student-{{ $studentId }}')">
                <div class="flex items-center gap-3">
                    <svg id="chevron-{{ $studentId }}" class="h-4 w-4 text-slate-400 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    <div>
                        <span class="font-semibold text-slate-800">{{ $student?->full_name ?? 'Unknown' }}</span>
                        <span class="ml-2 text-xs text-slate-400">{{ $student?->admission_number }}</span>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-4 text-xs text-slate-600">
                    <span><span class="font-semibold text-slate-800">{{ $studentResults->count() }}</span> subjects</span>
                    <span>Total: <span class="font-semibold text-slate-800">{{ number_format($total, 1) }}</span></span>
                    <span>Avg: <span class="font-semibold text-slate-800">{{ $avg }}</span></span>
                    @if($reportCard)
                        <span>Pos: <span class="font-semibold text-slate-800">{{ $reportCard->position_in_class }}/{{ $reportCard->class_size }}</span></span>
                    @endif
                    @if($approved)
                        <span class="rounded-full bg-green-100 px-2 py-0.5 font-medium text-green-700">Approved</span>
                    @else
                        <span class="rounded-full bg-amber-100 px-2 py-0.5 font-medium text-amber-700">Pending</span>
                    @endif
                    @if($reportCard)
                    <a href="{{ route('examination.results.report-card', [$studentId, request('term_id')]) }}"
                       onclick="event.stopPropagation()"
                       class="rounded-lg border border-indigo-200 bg-indigo-50 px-3 py-1 font-medium text-indigo-700 hover:bg-indigo-100">
                        Report Card
                    </a>
                    @endif
                </div>
            </div>

            {{-- Per-subject results (collapsible) --}}
            <div id="student-{{ $studentId }}" class="hidden overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-white text-xs font-semibold uppercase text-slate-400">
                        <tr>
                            <th class="px-5 py-2.5 text-left">Subject</th>
                            <th class="px-4 py-2.5 text-center">First Test</th>
                            <th class="px-4 py-2.5 text-center">Second Test</th>
                            <th class="px-4 py-2.5 text-center">Examination</th>
                            <th class="px-4 py-2.5 text-center">Total</th>
                            <th class="px-4 py-2.5 text-center">Grade</th>
                            <th class="px-4 py-2.5 text-center">Remarks</th>
                            <th class="px-4 py-2.5 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($studentResults->sortBy('subject.name') as $result)
                        @php
                            $gc = match($result->grade) {
                                'A' => 'bg-emerald-100 text-emerald-700',
                                'B' => 'bg-blue-100 text-blue-700',
                                'C' => 'bg-indigo-100 text-indigo-700',
                                'D' => 'bg-amber-100 text-amber-700',
                                default => 'bg-red-100 text-red-700',
                            };
                        @endphp
                        <tr class="hover:bg-slate-50" id="result-row-{{ $result->id }}">
                            <td class="px-5 py-2.5 font-medium text-slate-800">{{ $result->subject?->name }}</td>
                            <td class="px-4 py-2.5 text-center text-slate-600">{{ (int)$result->ca1_score }}</td>
                            <td class="px-4 py-2.5 text-center text-slate-600">{{ (int)$result->ca2_score }}</td>
                            <td class="px-4 py-2.5 text-center text-slate-600">{{ (int)$result->exam_score }}</td>
                            <td class="px-4 py-2.5 text-center font-bold text-slate-800">{{ (int)$result->total_score }}</td>
                            <td class="px-4 py-2.5 text-center">
                                <span class="rounded-full px-2.5 py-0.5 text-xs font-bold {{ $gc }}">{{ $result->grade }}</span>
                            </td>
                            <td class="px-4 py-2.5 text-center text-xs text-slate-500">{{ $result->teacher_remark }}</td>
                            <td class="px-4 py-2.5 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button onclick="openEditModal({{ $result->id }}, '{{ addslashes($result->subject?->name) }}', {{ (float)$result->ca1_score }}, {{ (float)$result->ca2_score }}, {{ (float)$result->exam_score }})"
                                        class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">Edit</button>
                                    <form method="POST" action="{{ route('examination.results.destroy', $result) }}"
                                          onsubmit="return confirm('Delete this result?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-medium">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Grade Key --}}
    <div class="rounded-xl border border-slate-200 bg-white px-5 py-3 text-xs text-slate-500 flex flex-wrap gap-4">
        <span class="font-semibold text-slate-600">Grade Key:</span>
        <span><span class="font-bold text-emerald-700">A</span> 70–100 (Excellent)</span>
        <span><span class="font-bold text-blue-700">B</span> 60–69 (Very Good)</span>
        <span><span class="font-bold text-indigo-700">C</span> 50–59 (Good)</span>
        <span><span class="font-bold text-amber-700">D</span> 40–49 (Pass)</span>
        <span><span class="font-bold text-red-700">E</span> 0–39 (Fail)</span>
    </div>

    {{-- Edit Score Modal --}}
    <div id="edit-result-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
        <div class="w-full max-w-sm rounded-2xl bg-white p-6 shadow-xl">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Edit Score</h2>
                    <p id="modal-subject-name" class="text-xs text-slate-500 mt-0.5"></p>
                </div>
                <button onclick="document.getElementById('edit-result-modal').classList.add('hidden')"
                    class="text-slate-400 hover:text-slate-600">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form id="edit-result-form" method="POST" class="space-y-4">
                @csrf @method('PUT')
                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">First Test</label>
                        <input type="number" name="first_test" id="modal-test1" min="0" max="100" step="0.5"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-center focus:outline-none focus:ring-2 focus:ring-indigo-400"
                            oninput="calcModalTotal()">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Second Test</label>
                        <input type="number" name="second_test" id="modal-test2" min="0" max="100" step="0.5"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-center focus:outline-none focus:ring-2 focus:ring-indigo-400"
                            oninput="calcModalTotal()">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Examination</label>
                        <input type="number" name="exam" id="modal-exam" min="0" max="100" step="0.5"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-center focus:outline-none focus:ring-2 focus:ring-indigo-400"
                            oninput="calcModalTotal()">
                    </div>
                </div>
                <div class="rounded-lg bg-slate-50 px-4 py-2 text-center">
                    <span class="text-xs text-slate-500">Total: </span>
                    <span id="modal-total" class="text-lg font-bold text-indigo-700">0</span>
                    <span class="ml-2 text-xs" id="modal-grade"></span>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('edit-result-modal').classList.add('hidden')"
                        class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Cancel</button>
                    <button type="submit" class="rounded-lg bg-indigo-600 px-5 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Save</button>
                </div>
            </form>
        </div>
    </div>

    @elseif(request('class_id') && request('term_id'))
    <div class="rounded-2xl border border-dashed border-slate-300 bg-white py-12 text-center text-slate-400">
        No results found for the selected class and term.
        <div class="mt-3">
            <a href="{{ route('examination.results.import') }}" class="text-sm text-indigo-600 hover:underline">Import results from CSV/Excel</a>
        </div>
    </div>
    @else
    <div class="rounded-2xl border border-dashed border-slate-300 bg-white py-12 text-center text-slate-400">
        Select a class and term to view results.
    </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
function toggleRow(id) {
    const el = document.getElementById(id);
    const studentId = id.replace('student-', '');
    const chevron = document.getElementById('chevron-' + studentId);
    el.classList.toggle('hidden');
    chevron.style.transform = el.classList.contains('hidden') ? '' : 'rotate(180deg)';
}

function openEditModal(resultId, subjectName, test1, test2, exam) {
    document.getElementById('modal-subject-name').textContent = subjectName;
    document.getElementById('modal-test1').value = test1;
    document.getElementById('modal-test2').value = test2;
    document.getElementById('modal-exam').value  = exam;
    document.getElementById('edit-result-form').action = '/examination/results/' + resultId;
    calcModalTotal();
    document.getElementById('edit-result-modal').classList.remove('hidden');
}

function calcModalTotal() {
    const test1 = parseFloat(document.getElementById('modal-test1').value) || 0;
    const test2 = parseFloat(document.getElementById('modal-test2').value) || 0;
    const exam  = parseFloat(document.getElementById('modal-exam').value)  || 0;
    const total = test1 + test2 + exam;
    document.getElementById('modal-total').textContent = total.toFixed(1);

    let grade, cls;
    if (total >= 70)      { grade = 'A'; cls = 'text-emerald-600 font-bold'; }
    else if (total >= 60) { grade = 'B'; cls = 'text-blue-600 font-bold'; }
    else if (total >= 50) { grade = 'C'; cls = 'text-indigo-600 font-bold'; }
    else if (total >= 40) { grade = 'D'; cls = 'text-amber-600 font-bold'; }
    else                  { grade = 'E'; cls = 'text-red-600 font-bold'; }

    const el = document.getElementById('modal-grade');
    el.textContent  = '→ Grade ' + grade;
    el.className    = cls;
}
</script>
@endpush
