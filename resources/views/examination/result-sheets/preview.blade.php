@extends('layouts.app')
@section('title', 'Result Import Preview')
@section('header', 'Result Import Preview')

@section('content')
<div class="space-y-6 max-w-6xl">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Import Preview - Batch #{{ $batch->id }}</h1>
            <p class="text-sm text-slate-500 mt-1">
                {{ $assessmentTypes[$batch->assessment_type] ?? ucfirst(str_replace('_', ' ', (string) $batch->assessment_type)) }}
                |
                {{ $batch->schoolClass?->grade_level?->label() ?? $batch->schoolClass?->name }}
                @if($batch->arm) - Arm {{ $batch->arm->name }} @endif
                | {{ $batch->term?->name }} | {{ $batch->session?->name }} | {{ $batch->examType?->name }}
            </p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('examination.result-sheets.import') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">New Import</a>
            <a href="{{ route('examination.result-sheets.history') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">History</a>
        </div>
    </div>

    @if($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs text-slate-500">Status</p>
            <p class="text-sm font-semibold mt-1 {{ $batch->status === 'validated' ? 'text-green-700' : ($batch->status === 'validation_failed' ? 'text-red-700' : 'text-slate-700') }}">
                {{ str_replace('_', ' ', ucfirst($batch->status)) }}
            </p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs text-slate-500">Rows Read</p>
            <p class="text-lg font-bold text-slate-800 mt-1">{{ $batch->total_rows }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs text-slate-500">Valid Student Rows</p>
            <p class="text-lg font-bold text-green-700 mt-1">{{ $batch->success_rows }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs text-slate-500">Errors</p>
            <p class="text-lg font-bold text-red-700 mt-1">{{ $batch->errors->count() }}</p>
        </div>
    </div>

    @php
        $allBatchErrors = $batch->errors()->orderBy('id')->get();
        $subjectMismatchError = $allBatchErrors->first(fn($error) => data_get($error->raw_payload, 'type') === 'subject_set_mismatch');
        $subjectMismatchData = $subjectMismatchError?->raw_payload ?? [];
        $expectedSubjects = collect(data_get($subjectMismatchData, 'expected_subjects', []))->filter()->values();
        $foundSubjects = collect(data_get($subjectMismatchData, 'found_subjects', []))->filter()->values();
        $missingSubjects = collect(data_get($subjectMismatchData, 'missing_subjects', []))->filter()->values();
        $unexpectedSubjects = collect(data_get($subjectMismatchData, 'unexpected_subjects', []))->filter()->values();
        $tableErrors = $allBatchErrors->reject(fn($error) => data_get($error->raw_payload, 'type') === 'subject_set_mismatch')->values();
    @endphp

    @if($subjectMismatchError)
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <div>
                    <h2 class="text-sm font-semibold text-amber-800">Subject Diagnostics</h2>
                    <p class="text-xs text-amber-700 mt-1">Expected subjects are based on admin-assigned subjects for the selected class.</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <button type="button" id="copy-expected-subjects-btn"
                        class="rounded-lg border border-amber-300 bg-white px-3 py-1.5 text-xs font-semibold text-amber-800 hover:bg-amber-100">
                        Copy Expected Subjects
                    </button>
                    <button type="button" id="download-expected-subjects-btn"
                        class="rounded-lg border border-amber-300 bg-white px-3 py-1.5 text-xs font-semibold text-amber-800 hover:bg-amber-100">
                        Download Expected CSV
                    </button>
                </div>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 mt-3">
                <div>
                    <p class="text-xs font-semibold text-slate-700">Expected ({{ $expectedSubjects->count() }})</p>
                    <p class="text-xs text-slate-600 mt-1">{{ $expectedSubjects->isNotEmpty() ? $expectedSubjects->implode(', ') : '-' }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-700">Found in File ({{ $foundSubjects->count() }})</p>
                    <p class="text-xs text-slate-600 mt-1">{{ $foundSubjects->isNotEmpty() ? $foundSubjects->implode(', ') : '-' }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-red-700">Missing ({{ $missingSubjects->count() }})</p>
                    <p class="text-xs text-red-700 mt-1">{{ $missingSubjects->isNotEmpty() ? $missingSubjects->implode(', ') : 'None' }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-red-700">Unexpected ({{ $unexpectedSubjects->count() }})</p>
                    <p class="text-xs text-red-700 mt-1">{{ $unexpectedSubjects->isNotEmpty() ? $unexpectedSubjects->implode(', ') : 'None' }}</p>
                </div>
            </div>
            <p id="expected-subjects-feedback" class="text-xs text-amber-700 mt-3 hidden"></p>
        </div>
    @endif

    @if($batch->errors->isNotEmpty())
        <div class="rounded-2xl border border-red-200 bg-white shadow-sm overflow-hidden">
            <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-4 border-b border-red-100 bg-red-50">
                <h2 class="text-sm font-semibold text-red-800">Validation Errors (Fix and Re-import)</h2>
                <a href="{{ route('examination.result-sheets.errors', $batch) }}" class="rounded-lg border border-red-300 bg-white px-3 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-100">Download Errors</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-4 py-2 text-left">Row</th>
                            <th class="px-4 py-2 text-left">Column</th>
                            <th class="px-4 py-2 text-left">Message</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($tableErrors->take(300) as $error)
                            <tr>
                                <td class="px-4 py-2 text-slate-700">{{ $error->row_number ?? '-' }}</td>
                                <td class="px-4 py-2 text-slate-600">{{ $error->column_name ?? '-' }}</td>
                                <td class="px-4 py-2 text-slate-700">{{ $error->error_message }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="rounded-2xl border border-green-200 bg-green-50 p-5">
            <h2 class="text-sm font-semibold text-green-800">Validation Passed</h2>
            <p class="text-sm text-green-700 mt-1">No validation errors found. You can now commit this import to save results.</p>
            <form method="POST" action="{{ route('examination.result-sheets.commit', $batch) }}" class="mt-4">
                @csrf
                <button type="submit" onclick="return confirm('Save this validated batch to database?')"
                    class="rounded-lg bg-green-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-green-700">
                    Commit Import
                </button>
            </form>
        </div>
    @endif
</div>

@if($subjectMismatchError)
    @php
        $expectedSubjectsForJs = $expectedSubjects->values()->all();
    @endphp
    @push('scripts')
    <script>
    (function () {
        const expectedSubjects = @json($expectedSubjectsForJs);
        const copyBtn = document.getElementById('copy-expected-subjects-btn');
        const downloadBtn = document.getElementById('download-expected-subjects-btn');
        const feedback = document.getElementById('expected-subjects-feedback');

        if (!copyBtn || !downloadBtn) return;

        const expectedText = expectedSubjects.join(', ');
        const expectedCsv = ['subject_name', ...expectedSubjects].join('\n');

        function showFeedback(message, ok = true) {
            if (!feedback) return;
            feedback.textContent = message;
            feedback.classList.remove('hidden');
            feedback.classList.toggle('text-amber-700', ok);
            feedback.classList.toggle('text-red-700', !ok);
            window.setTimeout(() => feedback.classList.add('hidden'), 3000);
        }

        copyBtn.addEventListener('click', async () => {
            try {
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    await navigator.clipboard.writeText(expectedText);
                    showFeedback('Expected subjects copied.');
                    return;
                }

                const input = document.createElement('textarea');
                input.value = expectedText;
                document.body.appendChild(input);
                input.select();
                document.execCommand('copy');
                document.body.removeChild(input);
                showFeedback('Expected subjects copied.');
            } catch (e) {
                showFeedback('Copy failed. Please copy manually from the list.', false);
            }
        });

        downloadBtn.addEventListener('click', () => {
            const blob = new Blob([expectedCsv], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = 'expected-subjects.csv';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
            showFeedback('Expected subject CSV downloaded.');
        });
    })();
    </script>
    @endpush
@endif
@endsection
