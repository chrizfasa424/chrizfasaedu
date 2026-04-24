<?php

namespace App\Services;

use App\Models\ClassArm;
use App\Models\ResultSubmission;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\File\UploadedFile as SymfonyUploadedFile;

class ResultSubmissionService
{
    public function __construct(
        private readonly StaffClassAuthorizationService $authorizationService,
        private readonly ResultImportValidatorService $validator,
        private readonly ResultImportService $resultImportService
    ) {
    }

    public function createSubmission(array $payload, UploadedFile $file, User $actor): ResultSubmission
    {
        $classId = (int) $payload['class_id'];
        $subjectId = !empty($payload['subject_id']) ? (int) $payload['subject_id'] : null;
        $armId = !empty($payload['arm_id']) ? (int) $payload['arm_id'] : null;

        if (!$this->authorizationService->canManageClassSubject($actor, $classId, $subjectId)) {
            throw ValidationException::withMessages([
                'class_id' => 'You are not authorized to submit results for the selected class/subject.',
            ]);
        }

        if ($armId) {
            $armValid = ClassArm::query()
                ->where('id', $armId)
                ->where('class_id', $classId)
                ->exists();

            if (!$armValid) {
                throw ValidationException::withMessages([
                    'arm_id' => 'Selected arm does not belong to the selected class.',
                ]);
            }
        }

        $class = SchoolClass::query()->findOrFail($classId);

        $storedPath = $file->storeAs(
            'result-submissions/raw',
            now()->format('YmdHis') . '-' . uniqid('', true) . '-' . $file->getClientOriginalName(),
            'local'
        );

        $action = strtolower(trim((string) ($payload['action'] ?? 'submit')));
        $status = $action === ResultSubmission::STATUS_DRAFT
            ? ResultSubmission::STATUS_DRAFT
            : ResultSubmission::STATUS_SUBMITTED;

        return DB::transaction(function () use ($payload, $actor, $class, $storedPath, $file, $status, $armId, $subjectId) {
            return ResultSubmission::query()->create([
                'school_id' => (int) $actor->school_id,
                'teacher_id' => (int) $actor->id,
                'section' => (string) ($payload['section'] ?? ($class->section ?: ($class->grade_level?->section() ?? ''))),
                'class_id' => (int) $class->id,
                'arm_id' => $armId,
                'subject_id' => $subjectId,
                'session_id' => (int) $payload['session_id'],
                'term_id' => (int) $payload['term_id'],
                'exam_type_id' => (int) $payload['exam_type_id'],
                'assessment_type' => (string) $payload['assessment_type'],
                'import_mode' => (string) $payload['import_mode'],
                'file_path' => $storedPath,
                'original_file_name' => $file->getClientOriginalName(),
                'status' => $status,
                'staff_note' => $payload['staff_note'] ?? null,
                'submitted_at' => $status === ResultSubmission::STATUS_SUBMITTED ? now() : null,
            ]);
        });
    }

    public function submitDraft(ResultSubmission $submission, User $actor, ?string $staffNote = null): ResultSubmission
    {
        $submission->update([
            'status' => ResultSubmission::STATUS_SUBMITTED,
            'staff_note' => $staffNote !== null ? trim($staffNote) : $submission->staff_note,
            'submitted_at' => now(),
            'reviewed_at' => null,
            'reviewed_by' => null,
            'admin_note' => null,
        ]);

        return $submission->fresh();
    }

    public function markUnderReview(ResultSubmission $submission): ResultSubmission
    {
        if ($submission->status === ResultSubmission::STATUS_SUBMITTED) {
            $submission->update([
                'status' => ResultSubmission::STATUS_UNDER_REVIEW,
            ]);
        }

        return $submission->fresh();
    }

    public function validateSubmission(ResultSubmission $submission): array
    {
        if (!Storage::disk('local')->exists($submission->file_path)) {
            return [
                'rows' => [],
                'errors' => [[
                    'row_number' => null,
                    'column_name' => 'file',
                    'error_message' => 'Uploaded file not found. Re-upload the result file.',
                    'raw_payload' => null,
                ]],
                'total_rows' => 0,
                'student_count' => 0,
                'detected_format' => 'unknown',
            ];
        }

        $absolutePath = Storage::disk('local')->path($submission->file_path);

        return $this->validator->validate($absolutePath, $this->validationContext($submission));
    }

    public function reviewSubmission(ResultSubmission $submission, User $reviewer, string $decision, ?string $adminNote = null): ResultSubmission
    {
        $decision = strtolower(trim($decision));
        if (!in_array($decision, ['approve', 'reject'], true)) {
            throw ValidationException::withMessages([
                'decision' => 'Invalid review decision.',
            ]);
        }

        $validation = $this->validateSubmission($submission);
        $errorCount = count((array) ($validation['errors'] ?? []));

        if ($decision === 'approve' && $errorCount > 0) {
            throw ValidationException::withMessages([
                'submission' => 'Cannot approve this submission because validation failed. Fix and re-submit.',
            ]);
        }

        $submission->update([
            'status' => $decision === 'approve' ? ResultSubmission::STATUS_APPROVED : ResultSubmission::STATUS_REJECTED,
            'reviewed_at' => now(),
            'reviewed_by' => (int) $reviewer->id,
            'admin_note' => $adminNote !== null ? trim($adminNote) : null,
            'validation_summary' => [
                'detected_format' => $validation['detected_format'] ?? 'unknown',
                'total_rows' => (int) ($validation['total_rows'] ?? 0),
                'student_count' => (int) ($validation['student_count'] ?? 0),
                'error_count' => $errorCount,
            ],
        ]);

        return $submission->fresh();
    }

    public function importApprovedSubmission(ResultSubmission $submission, User $admin): ResultSubmission
    {
        if ($submission->status !== ResultSubmission::STATUS_APPROVED) {
            throw ValidationException::withMessages([
                'submission' => 'Only approved submissions can be imported.',
            ]);
        }

        if (!Storage::disk('local')->exists($submission->file_path)) {
            throw ValidationException::withMessages([
                'submission' => 'Submission file is missing. Ask staff to re-upload the file.',
            ]);
        }

        $absolutePath = Storage::disk('local')->path($submission->file_path);
        $uploadedFile = new SymfonyUploadedFile(
            $absolutePath,
            $submission->original_file_name,
            null,
            null,
            true
        );

        $batch = $this->resultImportService->previewImport(
            $this->validationContext($submission),
            $uploadedFile,
            (int) $admin->id
        );

        if ($batch->status !== 'validated') {
            $submission->update([
                'status' => ResultSubmission::STATUS_REJECTED,
                'reviewed_at' => now(),
                'reviewed_by' => (int) $admin->id,
                'validation_summary' => [
                    'error_count' => (int) $batch->failed_rows,
                    'total_rows' => (int) $batch->total_rows,
                    'detected_format' => (string) data_get($batch->summary, 'detected_format', 'unknown'),
                ],
                'admin_note' => 'Import attempt failed validation. Review the linked import batch errors and ask staff to correct the sheet.',
            ]);

            throw ValidationException::withMessages([
                'submission' => 'Validation failed while importing approved submission. Submission has been moved to rejected.',
            ]);
        }

        $this->resultImportService->commitImport($batch, (int) $admin->id);
        $batch->refresh();

        $submission->update([
            'status' => ResultSubmission::STATUS_IMPORTED,
            'imported_at' => now(),
            'imported_by' => (int) $admin->id,
            'result_batch_id' => (int) $batch->id,
            'validation_summary' => [
                'error_count' => 0,
                'total_rows' => (int) $batch->total_rows,
                'student_count' => (int) $batch->success_rows,
                'detected_format' => (string) data_get($batch->summary, 'detected_format', 'unknown'),
            ],
        ]);

        return $submission->fresh();
    }

    public function validationContext(ResultSubmission $submission): array
    {
        return [
            'school_id' => (int) $submission->school_id,
            'section' => (string) ($submission->section ?? ''),
            'class_id' => (int) $submission->class_id,
            'arm_id' => $submission->arm_id ? (int) $submission->arm_id : null,
            'session_id' => (int) $submission->session_id,
            'term_id' => (int) $submission->term_id,
            'exam_type_id' => (int) $submission->exam_type_id,
            'assessment_type' => (string) $submission->assessment_type,
            'import_mode' => (string) $submission->import_mode,
        ];
    }
}
