@extends('layouts.app')
@section('title', 'Result Submission Review')
@section('header', 'Result Submission Review')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Submission #{{ $submission->id }}</h1>
            <p class="text-sm text-slate-500 mt-1">{{ $submission->original_file_name }}</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('examination.result-submissions.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Back</a>
            <a href="{{ route('examination.result-submissions.download', $submission) }}" class="rounded-lg border border-indigo-300 bg-indigo-50 px-4 py-2 text-sm font-medium text-indigo-700 hover:bg-indigo-100">Download File</a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs text-slate-500">Status</p>
            <p class="mt-1 text-lg font-semibold text-slate-900">{{ ucwords(str_replace('_', ' ', $submission->status)) }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs text-slate-500">Rows Read</p>
            <p class="mt-1 text-lg font-semibold text-slate-900">{{ (int) ($validation['total_rows'] ?? 0) }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs text-slate-500">Valid Student Rows</p>
            <p class="mt-1 text-lg font-semibold text-green-700">{{ (int) ($validation['student_count'] ?? 0) }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs text-slate-500">Errors</p>
            <p class="mt-1 text-lg font-semibold text-red-700">{{ count((array) ($validation['errors'] ?? [])) }}</p>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="text-sm font-semibold text-slate-700 mb-3">Submission Scope</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm text-slate-700">
            <div><span class="font-medium">Submitted By:</span> {{ $submission->teacher?->full_name ?? 'N/A' }}</div>
            <div><span class="font-medium">Assessment Type:</span> {{ ucwords(str_replace('_', ' ', $submission->assessment_type)) }}</div>
            <div><span class="font-medium">Class:</span> {{ $submission->schoolClass?->grade_level?->label() ?? $submission->schoolClass?->name }} @if($submission->arm)- {{ $submission->arm->name }}@endif</div>
            <div><span class="font-medium">Subject:</span> {{ $submission->subject?->name ?? 'Whole class result' }}</div>
            <div><span class="font-medium">Session / Term:</span> {{ $submission->session?->name }} / {{ $submission->term?->name }}</div>
            <div><span class="font-medium">Exam Type:</span> {{ $submission->examType?->name }}</div>
            <div class="md:col-span-2"><span class="font-medium">Staff Note:</span> {{ $submission->staff_note ?: 'No note provided.' }}</div>
            <div class="md:col-span-2"><span class="font-medium">Admin Note:</span> {{ $submission->admin_note ?: 'No note yet.' }}</div>
        </div>
    </div>

    @if($canEdit && in_array($submission->status, ['draft', 'rejected'], true))
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5">
            <h2 class="text-sm font-semibold text-amber-800 mb-3">Submit This Draft</h2>
            <form method="POST" action="{{ route('examination.result-submissions.submit', $submission) }}" class="space-y-3">
                @csrf
                <textarea name="staff_note" rows="3" class="w-full rounded-lg border border-amber-300 px-3 py-2 text-sm" placeholder="Optional update note for admin...">{{ old('staff_note', $submission->staff_note) }}</textarea>
                <button type="submit" class="rounded-lg bg-amber-600 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-700">Submit to Admin</button>
            </form>
        </div>
    @endif

    @if($isAdmin)
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div class="rounded-2xl border border-green-200 bg-green-50 p-5">
                <h2 class="text-sm font-semibold text-green-800 mb-3">Approve</h2>
                <form method="POST" action="{{ route('examination.result-submissions.review', $submission) }}" class="space-y-3">
                    @csrf
                    <input type="hidden" name="decision" value="approve">
                    <textarea name="admin_note" rows="3" class="w-full rounded-lg border border-green-300 px-3 py-2 text-sm" placeholder="Approval note (optional)...">{{ old('admin_note') }}</textarea>
                    <button type="submit" class="rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700">Approve Submission</button>
                </form>
            </div>
            <div class="rounded-2xl border border-red-200 bg-red-50 p-5">
                <h2 class="text-sm font-semibold text-red-800 mb-3">Reject</h2>
                <form method="POST" action="{{ route('examination.result-submissions.review', $submission) }}" class="space-y-3">
                    @csrf
                    <input type="hidden" name="decision" value="reject">
                    <textarea name="admin_note" rows="3" class="w-full rounded-lg border border-red-300 px-3 py-2 text-sm" placeholder="Reason for rejection (required in practice)...">{{ old('admin_note') }}</textarea>
                    <button type="submit" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">Reject Submission</button>
                </form>
            </div>
        </div>

        @if($submission->status === 'approved')
            <div class="rounded-2xl border border-indigo-200 bg-indigo-50 p-5">
                <h2 class="text-sm font-semibold text-indigo-800 mb-2">Import Approved Result</h2>
                <p class="text-sm text-indigo-700 mb-3">This will import into official result tables and move status to imported.</p>
                <form method="POST" action="{{ route('examination.result-submissions.import', $submission) }}" onsubmit="return confirm('Import this approved result into official records now?');">
                    @csrf
                    <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Import Official Result</button>
                </form>
            </div>
        @endif
    @endif

    @if(count((array) ($validation['errors'] ?? [])) > 0)
        <div class="rounded-2xl border border-red-200 bg-white shadow-sm overflow-hidden">
            <div class="bg-red-50 px-5 py-3 border-b border-red-200">
                <h2 class="text-sm font-semibold text-red-700">Validation Errors</h2>
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
                        @foreach(array_slice((array) ($validation['errors'] ?? []), 0, 100) as $error)
                            <tr>
                                <td class="px-4 py-2">{{ $error['row_number'] ?? '-' }}</td>
                                <td class="px-4 py-2">{{ $error['column_name'] ?? '-' }}</td>
                                <td class="px-4 py-2 text-red-700">{{ $error['error_message'] ?? 'Validation error' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="rounded-2xl border border-green-200 bg-green-50 p-5 text-green-700">
            Validation passed. No structure/data errors were detected.
        </div>
    @endif

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="bg-slate-50 px-5 py-3 border-b border-slate-200">
            <h2 class="text-sm font-semibold text-slate-700">Sheet Preview (First 25 Rows)</h2>
        </div>
        @if(!empty($sheetPreview))
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <tbody class="divide-y divide-slate-100">
                        @foreach($sheetPreview as $row)
                            <tr>
                                @foreach($row as $cell)
                                    <td class="px-4 py-2 whitespace-nowrap">{{ $cell }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="px-5 py-8 text-sm text-slate-500">Could not render preview for this file.</div>
        @endif
    </div>
</div>
@endsection
