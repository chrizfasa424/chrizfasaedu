@extends('layouts.app')
@section('title', 'Result Submissions')
@section('header', 'Result Submissions')

@section('content')
<div class="space-y-6">
    @php
        $showAllClassesForPicker = $isAdmin || empty($authorizedClassIds);
    @endphp
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Result Submissions</h1>
            <p class="text-sm text-slate-500 mt-1">Staff upload result sheets for admin review, approval, and import.</p>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="text-sm font-semibold text-slate-700 mb-4">New Submission</h2>
        <form method="POST" action="{{ route('examination.result-submissions.store') }}" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @csrf

            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Class <span class="text-red-500">*</span></label>
                <select name="class_id" id="class_id" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    <option value="">Select class</option>
                    @foreach($classes as $class)
                        @if($showAllClassesForPicker || in_array((int) $class->id, $authorizedClassIds, true))
                            <option value="{{ $class->id }}" {{ (string) old('class_id') === (string) $class->id ? 'selected' : '' }}>
                                {{ $class->grade_level?->label() ?? $class->name }}
                            </option>
                        @endif
                    @endforeach
                </select>
                @if(!$isAdmin && empty($authorizedClassIds))
                    <p class="mt-1 text-xs text-amber-700">No explicit class assignment was found for your profile, so all classes are shown. Final authorization is still checked on submit.</p>
                @endif
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Arm</label>
                <select name="arm_id" id="arm_id" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    <option value="">All arms / no arm</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Subject (Optional)</label>
                <select name="subject_id" id="subject_id" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    <option value="">Whole class result file</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" {{ (string) old('subject_id') === (string) $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Session <span class="text-red-500">*</span></label>
                <select name="session_id" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    <option value="">Select session</option>
                    @foreach($sessions as $session)
                        <option value="{{ $session->id }}" {{ (string) old('session_id', $session->is_current ? $session->id : '') === (string) $session->id ? 'selected' : '' }}>
                            {{ $session->name }}{{ $session->is_current ? ' (Current)' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Term <span class="text-red-500">*</span></label>
                <select name="term_id" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    <option value="">Select term</option>
                    @foreach($terms as $term)
                        <option value="{{ $term->id }}" {{ (string) old('term_id', $term->is_current ? $term->id : '') === (string) $term->id ? 'selected' : '' }}>
                            {{ $term->name }} - {{ $term->session->name ?? '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Exam Type <span class="text-red-500">*</span></label>
                <select name="exam_type_id" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    <option value="">Select exam type</option>
                    @foreach($examTypes as $examType)
                        <option value="{{ $examType->id }}" {{ (string) old('exam_type_id') === (string) $examType->id ? 'selected' : '' }}>{{ $examType->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Assessment Type <span class="text-red-500">*</span></label>
                <select name="assessment_type" id="assessment_type" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    @foreach($assessmentTypes as $type => $label)
                        <option value="{{ $type }}" {{ (string) old('assessment_type', 'full_result') === (string) $type ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Import Mode <span class="text-red-500">*</span></label>
                <select name="import_mode" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    <option value="create_only" {{ old('import_mode', 'create_only') === 'create_only' ? 'selected' : '' }}>Create Only</option>
                    <option value="update_existing" {{ old('import_mode') === 'update_existing' ? 'selected' : '' }}>Update Existing</option>
                    <option value="replace_existing" {{ old('import_mode') === 'replace_existing' ? 'selected' : '' }}>Replace Existing</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Save As</label>
                <select name="action" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    <option value="submit" {{ old('action', 'submit') === 'submit' ? 'selected' : '' }}>Submit for admin review</option>
                    <option value="draft" {{ old('action') === 'draft' ? 'selected' : '' }}>Save as draft</option>
                </select>
            </div>

            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-slate-600 mb-1">Result File (xlsx/xls/csv) <span class="text-red-500">*</span></label>
                <input type="file" name="file" required accept=".xlsx,.xls,.csv" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>

            <div class="md:col-span-2 lg:col-span-3">
                <label class="block text-xs font-medium text-slate-600 mb-1">Note to Admin</label>
                <textarea name="staff_note" rows="3" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" placeholder="Optional note for reviewer...">{{ old('staff_note') }}</textarea>
            </div>

            <div class="md:col-span-2 lg:col-span-3 flex flex-wrap items-center justify-between gap-3 pt-2">
                <a id="template-link" href="{{ route('examination.result-submissions.template', ['assessment_type' => 'full_result']) }}" class="rounded-lg border border-indigo-300 bg-indigo-50 px-4 py-2 text-sm font-medium text-indigo-700 hover:bg-indigo-100">Download Template</a>
                <button type="submit" class="rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">Upload Submission</button>
            </div>
        </form>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-4 py-2 text-left">Submission</th>
                    <th class="px-4 py-2 text-left">Scope</th>
                    <th class="px-4 py-2 text-left">Status</th>
                    <th class="px-4 py-2 text-left">Summary</th>
                    <th class="px-4 py-2 text-left">Updated</th>
                    <th class="px-4 py-2 text-left">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($submissions as $submission)
                    <tr>
                        <td class="px-4 py-2">
                            <div class="font-medium text-slate-800">#{{ $submission->id }} - {{ $submission->original_file_name }}</div>
                            <div class="text-xs text-slate-400">By {{ $submission->teacher?->full_name ?? 'Unknown' }}</div>
                        </td>
                        <td class="px-4 py-2 text-slate-700">
                            <div>{{ $assessmentTypes[$submission->assessment_type] ?? ucfirst(str_replace('_', ' ', $submission->assessment_type)) }}</div>
                            <div class="text-xs text-slate-500">
                                {{ $submission->schoolClass?->grade_level?->label() ?? $submission->schoolClass?->name }}
                                @if($submission->arm)
                                    - {{ $submission->arm->name }}
                                @endif
                                @if($submission->subject)
                                    | {{ $submission->subject->name }}
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-2">
                            <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold
                                {{ $submission->status === 'approved' || $submission->status === 'imported' ? 'bg-green-100 text-green-700' : '' }}
                                {{ $submission->status === 'rejected' ? 'bg-red-100 text-red-700' : '' }}
                                {{ $submission->status === 'under_review' ? 'bg-blue-100 text-blue-700' : '' }}
                                {{ in_array($submission->status, ['draft', 'submitted'], true) ? 'bg-amber-100 text-amber-700' : '' }}
                            ">
                                {{ ucwords(str_replace('_', ' ', $submission->status)) }}
                            </span>
                        </td>
                        <td class="px-4 py-2 text-xs text-slate-600">
                            @php
                                $summary = (array) ($submission->validation_summary ?? []);
                            @endphp
                            <div>Rows: {{ (int) ($summary['total_rows'] ?? 0) }}</div>
                            <div>Students: {{ (int) ($summary['student_count'] ?? 0) }}</div>
                            <div>Errors: {{ (int) ($summary['error_count'] ?? 0) }}</div>
                        </td>
                        <td class="px-4 py-2 text-xs text-slate-500">{{ $submission->updated_at?->format('d M Y H:i') }}</td>
                        <td class="px-4 py-2">
                            <a href="{{ route('examination.result-submissions.show', $submission) }}" class="text-indigo-600 hover:underline text-xs font-medium">Open</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-sm text-slate-400">No submissions yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $submissions->links() }}
</div>

@php
    $classArms = $classes->mapWithKeys(fn($class) => [
        $class->id => $class->arms->map(fn($arm) => ['id' => $arm->id, 'name' => $arm->name])->values()
    ]);
    $classSubjects = $classes->mapWithKeys(fn($class) => [
        $class->id => $class->subjects->map(fn($subject) => ['id' => (int) $subject->id, 'name' => (string) $subject->name])->values()
    ]);
@endphp

@push('scripts')
<script>
const classArms = @json($classArms);
const classSubjects = @json($classSubjects);
const classSelect = document.getElementById('class_id');
const armSelect = document.getElementById('arm_id');
const subjectSelect = document.getElementById('subject_id');
const assessmentSelect = document.getElementById('assessment_type');
const templateLink = document.getElementById('template-link');

function refreshArms() {
    const classId = classSelect.value;
    const arms = classArms[classId] || [];
    const selected = @json((string) old('arm_id'));
    armSelect.innerHTML = '<option value="">All arms / no arm</option>';

    arms.forEach((arm) => {
        const option = document.createElement('option');
        option.value = arm.id;
        option.textContent = arm.name;
        if (String(arm.id) === String(selected)) {
            option.selected = true;
        }
        armSelect.appendChild(option);
    });
}

function refreshTemplateLink() {
    const url = new URL(@json(route('examination.result-submissions.template')), window.location.origin);
    if (classSelect.value) {
        url.searchParams.set('class_id', classSelect.value);
    }
    if (armSelect.value) {
        url.searchParams.set('arm_id', armSelect.value);
    }
    if (assessmentSelect.value) {
        url.searchParams.set('assessment_type', assessmentSelect.value);
    }
    templateLink.href = url.toString();
}

function refreshSubjects() {
    const classId = classSelect.value;
    const subjects = classSubjects[classId] || [];
    const selected = @json((string) old('subject_id'));

    subjectSelect.innerHTML = '';
    const defaultOption = document.createElement('option');
    defaultOption.value = '';
    defaultOption.textContent = 'Whole class result file';
    subjectSelect.appendChild(defaultOption);

    subjects.forEach((subject) => {
        const option = document.createElement('option');
        option.value = String(subject.id);
        option.textContent = subject.name;
        if (String(subject.id) === String(selected)) {
            option.selected = true;
        }
        subjectSelect.appendChild(option);
    });

    if (!subjects.some((subject) => String(subject.id) === String(subjectSelect.value))) {
        subjectSelect.value = '';
    }
}

classSelect.addEventListener('change', () => {
    refreshArms();
    refreshSubjects();
    refreshTemplateLink();
});
armSelect.addEventListener('change', refreshTemplateLink);
assessmentSelect.addEventListener('change', refreshTemplateLink);

refreshArms();
refreshSubjects();
refreshTemplateLink();
</script>
@endpush
@endsection
