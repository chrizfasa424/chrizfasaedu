@extends('layouts.app')
@section('title', 'My Results')
@section('header', 'My Results')

@php
    $primaryColor = trim((string) data_get($school->settings ?? [], 'branding.primary_color', '#2D1D5C'));
    $canViewFirst = (bool) ($componentVisibility['first_test'] ?? false);
    $canViewSecond = (bool) ($componentVisibility['second_test'] ?? false);
    $canViewExam = (bool) ($componentVisibility['exam'] ?? false);
    $canViewTerminal = (bool) ($componentVisibility['full_result'] ?? false);
    $isTestView = ($selectedViewType ?? '') === 'test';
@endphp

@section('content')
<div class="space-y-6">
    <section class="relative overflow-hidden rounded-3xl p-6 shadow-xl sm:p-8"
        style="background:linear-gradient(135deg, {{ $primaryColor }} 0%, #23134e 58%, #130a2a 100%);">
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_80%_22%,rgba(223,231,83,0.2),transparent_36%)]"></div>
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_18%_88%,rgba(255,255,255,0.09),transparent_46%)]"></div>
        <div class="relative z-10 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-white/65">Premium Result Center</p>
                <h1 class="mt-2 text-2xl font-extrabold text-white sm:text-3xl">{{ $student->full_name }}</h1>
                <p class="mt-2 text-sm text-white/80">
                    View published First Test, Second Test, Exam, and Full Terminal Result by session and term.
                </p>
            </div>
            <a href="{{ route('student.dashboard') }}"
                class="inline-flex items-center gap-2 rounded-xl border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold text-white hover:bg-white/15">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m15 19-7-7 7-7" />
                </svg>
                Back To Dashboard
            </a>
        </div>
    </section>

    <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <form method="GET" action="{{ route('portal.results.center') }}" class="grid gap-4 md:grid-cols-4">
            <input type="hidden" name="filter" value="1">
            <div>
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500">Term</label>
                <select id="term-slot-select" name="term_slot"
                    class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm text-slate-800 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                    <option value="">Select</option>
                    @foreach(($termOptions ?? []) as $termValue => $termLabel)
                        <option value="{{ $termValue }}" @selected(($selectedTermSlot ?? '') === $termValue)>
                            {{ $termLabel }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500">Exam Term/Test</label>
                <select id="view-type-select" name="view_type"
                    class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm text-slate-800 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                    <option value="">Select</option>
                    @foreach(($viewTypeOptions ?? []) as $value => $label)
                        <option value="{{ $value }}" @selected(($selectedViewType ?? '') === $value)>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end justify-end gap-2 md:col-span-2">
                <button id="results-filter-btn" type="submit"
                    class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">
                    Filter
                </button>
                <a href="{{ route('portal.results.center') }}"
                    class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">
                    Reset
                </a>
            </div>
        </form>
    </section>

    @if(!empty($selectionNotice))
        <section class="rounded-2xl border border-amber-200 bg-amber-50 px-5 py-3 text-sm text-amber-800 shadow-sm">
            {{ $selectionNotice }}
        </section>
    @endif

    @if(!($isFilterApplied ?? false))
        <section class="rounded-3xl border border-dashed border-slate-300 bg-white px-6 py-12 text-center shadow-sm">
            <p class="text-lg font-bold text-slate-800">No Result Displayed Yet</p>
            <p class="mt-2 text-sm text-slate-500">
                Select <strong>Term</strong> and <strong>Exam Term/Test</strong>, then click <strong>Filter</strong>.
            </p>
        </section>
    @elseif($sheets->isEmpty())
        <section class="rounded-3xl border border-dashed border-slate-300 bg-white px-6 py-12 text-center shadow-sm">
            <p class="text-lg font-bold text-slate-800">No Published Result Found</p>
            <p class="mt-2 text-sm text-slate-500">
                {{ $emptyStateMessage ?? 'No published result found for the selected filter.' }}
            </p>
        </section>
    @else
        <section class="rounded-3xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-6 py-4">
                <h2 class="text-base font-bold text-slate-900">Available Published Result Sheets</h2>
                <p class="mt-0.5 text-xs text-slate-500">Select the exact term and exam record you want to inspect.</p>
            </div>
            <div class="grid gap-3 p-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($sheets as $sheetOption)
                    @php
                        $query = array_filter([
                            'filter' => 1,
                            'term_slot' => $selectedTermSlot ?? null,
                            'view_type' => $selectedViewType ?? null,
                            'sheet_id' => $sheetOption->id,
                        ], fn ($value) => !is_null($value) && $value !== '');
                        $isActive = $activeSheet && (int) $activeSheet->id === (int) $sheetOption->id;
                        $terminalPublished = !is_null($sheetOption->full_result_published_at)
                            || (bool) $sheetOption->is_published
                            || !is_null($sheetOption->published_at);

                        $stages = [
                            'First Test' => !is_null($sheetOption->first_test_published_at) || $terminalPublished,
                            'Second Test' => !is_null($sheetOption->second_test_published_at) || $terminalPublished,
                            'Exam' => !is_null($sheetOption->exam_published_at) || $terminalPublished,
                            'Terminal' => $terminalPublished,
                        ];
                    @endphp
                    <a href="{{ route('portal.results.center', $query) }}"
                        class="rounded-2xl border p-4 transition {{ $isActive ? 'border-indigo-300 bg-indigo-50 shadow-sm' : 'border-slate-200 bg-white hover:border-indigo-200 hover:bg-slate-50' }}">
                        <p class="text-xs font-semibold uppercase tracking-wider {{ $isActive ? 'text-indigo-600' : 'text-slate-400' }}">
                            {{ $sheetOption->term?->name ?? 'Term' }}
                        </p>
                        <p class="mt-1 font-bold {{ $isActive ? 'text-indigo-900' : 'text-slate-900' }}">
                            {{ $sheetOption->examType?->name ?? 'Exam Type' }}
                        </p>
                        <p class="mt-1 text-xs text-slate-500">
                            {{ $sheetOption->session?->name ?? 'Session' }}
                        </p>
                        <div class="mt-3 flex flex-wrap gap-1.5">
                            @foreach($stages as $stageLabel => $isStagePublished)
                                <span class="inline-flex rounded-full px-2 py-0.5 text-[11px] font-semibold {{ $isStagePublished ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                    {{ $stageLabel }}
                                </span>
                            @endforeach
                        </div>
                    </a>
                @endforeach
            </div>
        </section>

        @if($activeSheet)
            @php
                $items = $activeSheet->items->sortBy('subject.name')->values();
                $firstTotal = (float) $items->sum('first_test_score');
                $secondTotal = (float) $items->sum('second_test_score');
                $examTotal = (float) $items->sum('exam_score');
                $subjectCount = max((int) $items->count(), 1);
            @endphp

            <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                @if($canViewFirst)
                    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">First Test Total</p>
                        <p class="mt-2 text-3xl font-extrabold text-slate-900">{{ number_format($firstTotal, 2) }}</p>
                        <p class="mt-1 text-xs text-slate-500">Average: {{ number_format($firstTotal / $subjectCount, 2) }}</p>
                    </div>
                @endif
                @if($canViewSecond)
                    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Second Test Total</p>
                        <p class="mt-2 text-3xl font-extrabold text-slate-900">{{ number_format($secondTotal, 2) }}</p>
                        <p class="mt-1 text-xs text-slate-500">Average: {{ number_format($secondTotal / $subjectCount, 2) }}</p>
                    </div>
                @endif
                @if($canViewExam)
                    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Exam Total</p>
                        <p class="mt-2 text-3xl font-extrabold text-slate-900">{{ number_format($examTotal, 2) }}</p>
                        <p class="mt-1 text-xs text-slate-500">Average: {{ number_format($examTotal / $subjectCount, 2) }}</p>
                    </div>
                @endif
                @if($canViewTerminal)
                    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Terminal Average</p>
                        <p class="mt-2 text-3xl font-extrabold text-indigo-700">{{ number_format((float) $activeSheet->average_score, 2) }}</p>
                        <p class="mt-1 text-xs text-slate-500">Position: {{ $activeSheet->class_position ?: '-' }}</p>
                    </div>
                @endif
            </section>

            @if($isTestView && ($canViewFirst || $canViewSecond))
                <section class="rounded-3xl border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-100 px-6 py-4">
                        <h2 class="text-base font-bold text-slate-900">Test Result</h2>
                        <p class="mt-0.5 text-xs text-slate-500">Subjects and published first/second test scores.</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="bg-slate-50 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                                    <th class="px-6 py-3 text-left">Subject</th>
                                    @if($canViewFirst)
                                        <th class="px-6 py-3 text-center">First Test</th>
                                    @endif
                                    @if($canViewSecond)
                                        <th class="px-6 py-3 text-center">Second Test</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($items as $item)
                                    <tr class="hover:bg-slate-50/70">
                                        <td class="px-6 py-3.5 font-semibold text-slate-900">{{ $item->subject?->name ?? '-' }}</td>
                                        @if($canViewFirst)
                                            <td class="px-6 py-3.5 text-center text-slate-700">{{ number_format((float) $item->first_test_score, 2) }}</td>
                                        @endif
                                        @if($canViewSecond)
                                            <td class="px-6 py-3.5 text-center text-slate-700">{{ number_format((float) $item->second_test_score, 2) }}</td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>
            @endif

            @if(!$isTestView && $canViewExam)
                <section class="rounded-3xl border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-100 px-6 py-4">
                        <h2 class="text-base font-bold text-slate-900">Exam Result</h2>
                        <p class="mt-0.5 text-xs text-slate-500">Subjects and published exam scores.</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="bg-slate-50 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                                    <th class="px-6 py-3 text-left">Subject</th>
                                    <th class="px-6 py-3 text-center">Exam Score</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($items as $item)
                                    <tr class="hover:bg-slate-50/70">
                                        <td class="px-6 py-3.5 font-semibold text-slate-900">{{ $item->subject?->name ?? '-' }}</td>
                                        <td class="px-6 py-3.5 text-center text-slate-700">{{ number_format((float) $item->exam_score, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>
            @endif

            @if($canViewTerminal)
                <section class="rounded-3xl border border-slate-200 bg-white shadow-sm">
                    <div class="flex flex-col gap-3 border-b border-slate-100 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-base font-bold text-slate-900">Full Terminal Result</h2>
                            <p class="mt-0.5 text-xs text-slate-500">
                                {{ $activeSheet->term?->name ?? 'Term' }} | {{ $activeSheet->examType?->name ?? 'Exam Type' }}
                                | {{ $activeSheet->schoolClass?->grade_level?->label() ?? $activeSheet->schoolClass?->name }}@if($activeSheet->arm?->name) - Arm {{ $activeSheet->arm->name }}@endif
                            </p>
                        </div>
                        <a href="{{ route('portal.results.sheet.pdf', $activeSheet) }}"
                            class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
                            Download PDF
                        </a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="bg-slate-50 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                                    <th class="px-6 py-3 text-left">Subject</th>
                                    <th class="px-4 py-3 text-center">Exam</th>
                                    <th class="px-4 py-3 text-center">First Test</th>
                                    <th class="px-4 py-3 text-center">Second Test</th>
                                    <th class="px-4 py-3 text-center">Total</th>
                                    <th class="px-4 py-3 text-center">Grade</th>
                                    <th class="px-6 py-3 text-left">Remark</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($items as $item)
                                    @php
                                        $grade = strtoupper((string) $item->grade);
                                        $gradeBadge = match ($grade) {
                                            'A', 'A1' => 'bg-emerald-100 text-emerald-700',
                                            'B', 'B2', 'B3' => 'bg-blue-100 text-blue-700',
                                            'C', 'C4', 'C5', 'C6' => 'bg-indigo-100 text-indigo-700',
                                            'D', 'D7', 'E8' => 'bg-amber-100 text-amber-700',
                                            default => 'bg-red-100 text-red-700',
                                        };
                                    @endphp
                                    <tr class="hover:bg-slate-50/70">
                                        <td class="px-6 py-3.5 font-semibold text-slate-900">{{ $item->subject?->name ?? '-' }}</td>
                                        <td class="px-4 py-3.5 text-center text-slate-700">{{ number_format((float) $item->exam_score, 2) }}</td>
                                        <td class="px-4 py-3.5 text-center text-slate-700">{{ number_format((float) $item->first_test_score, 2) }}</td>
                                        <td class="px-4 py-3.5 text-center text-slate-700">{{ number_format((float) $item->second_test_score, 2) }}</td>
                                        <td class="px-4 py-3.5 text-center font-bold text-slate-900">{{ number_format((float) $item->total_score, 2) }}</td>
                                        <td class="px-4 py-3.5 text-center">
                                            <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-bold {{ $gradeBadge }}">
                                                {{ $item->grade ?: '-' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-3.5 text-slate-600">{{ $item->remark ?: '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @php
                        $remarkRows = [
                            [
                                'label' => 'Teacher Comment',
                                'active' => (bool) ($activeSheet->class_teacher_remark_active ?? true),
                                'value' => $activeSheet->class_teacher_remark,
                            ],
                            [
                                'label' => 'Principal Comment',
                                'active' => (bool) ($activeSheet->principal_remark_active ?? true),
                                'value' => $activeSheet->principal_remark,
                            ],
                            [
                                'label' => 'Vice Principal Comment',
                                'active' => (bool) ($activeSheet->vice_principal_remark_active ?? true),
                                'value' => $activeSheet->vice_principal_remark,
                            ],
                        ];
                        $visibleRemarkRows = collect($remarkRows)->where('active', true)->values();
                    @endphp
                    <div class="grid gap-3 border-t border-slate-100 px-6 py-4 md:grid-cols-3">
                        @forelse($visibleRemarkRows as $remark)
                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $remark['label'] }}</p>
                                <p class="mt-2 text-sm text-slate-700">{{ $remark['value'] ?: 'No comment yet.' }}</p>
                            </div>
                        @empty
                            <div class="md:col-span-3 rounded-xl border border-slate-200 bg-slate-50 p-3 text-sm text-slate-500">
                                Comments are currently unavailable.
                            </div>
                        @endforelse
                    </div>
                </section>
            @endif
        @endif
    @endif

</div>
@endsection

@push('scripts')
<script>
    (() => {
        const termSelect = document.getElementById('term-slot-select');
        const viewTypeSelect = document.getElementById('view-type-select');
        const filterButton = document.getElementById('results-filter-btn');

        if (!termSelect || !viewTypeSelect || !filterButton) {
            return;
        }

        const updateButtonState = () => {
            const canSubmit = termSelect.value !== '' && viewTypeSelect.value !== '';
            filterButton.disabled = !canSubmit;
            filterButton.classList.toggle('opacity-50', !canSubmit);
            filterButton.classList.toggle('cursor-not-allowed', !canSubmit);
        };

        termSelect.addEventListener('change', updateButtonState);
        viewTypeSelect.addEventListener('change', updateButtonState);
        updateButtonState();
    })();
</script>
@endpush
