<?php

namespace App\Services;

use App\Models\ResultBatch;
use App\Models\ResultImportError;
use App\Models\SchoolClass;
use App\Models\StudentResult;
use App\Models\StudentResultItem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ResultImportService
{
    public function __construct(
        private readonly ResultImportValidatorService $validator,
        private readonly ResultSheetRankingService $rankingService,
        private readonly GradingService $gradingService
    ) {
    }

    public function previewImport(array $context, UploadedFile $file, int $userId): ResultBatch
    {
        $storedPath = $file->storeAs(
            'result-imports/raw',
            now()->format('YmdHis') . '-' . uniqid() . '-' . $file->getClientOriginalName(),
            'local'
        );

        $batch = ResultBatch::query()->create([
            'school_id' => (int) $context['school_id'],
            'section' => (string) ($context['section'] ?? ''),
            'class_id' => (int) $context['class_id'],
            'arm_id' => !empty($context['arm_id']) ? (int) $context['arm_id'] : null,
            'session_id' => (int) $context['session_id'],
            'term_id' => (int) $context['term_id'],
            'exam_type_id' => (int) $context['exam_type_id'],
            'assessment_type' => (string) ($context['assessment_type'] ?? 'full_result'),
            'file_name' => $file->getClientOriginalName(),
            'stored_path' => $storedPath,
            'source_type' => 'upload',
            'import_mode' => (string) $context['import_mode'],
            'status' => 'validating',
            'imported_by' => $userId,
        ]);

        $absolutePath = Storage::disk('local')->path($storedPath);
        $result = $this->validator->validate($absolutePath, $context);

        if (!empty($result['errors'])) {
            $this->storeErrors($batch, $result['errors']);

            $batch->update([
                'status' => 'validation_failed',
                'total_rows' => (int) $result['total_rows'],
                'success_rows' => 0,
                'failed_rows' => count($result['errors']),
                'summary' => [
                    'detected_format' => $result['detected_format'],
                    'student_count' => (int) $result['student_count'],
                    'error_count' => count($result['errors']),
                    'message' => 'Validation failed. Fix the listed issues and re-import.',
                ],
                'validated_at' => now(),
            ]);

            return $batch->fresh(['errors', 'schoolClass', 'arm', 'session', 'term', 'examType']);
        }

        $payloadPath = 'result-imports/payloads/batch-' . $batch->id . '.json';
        Storage::disk('local')->put($payloadPath, json_encode($result['rows'], JSON_THROW_ON_ERROR));

        $batch->update([
            'status' => 'validated',
            'total_rows' => (int) $result['total_rows'],
            'success_rows' => (int) $result['student_count'],
            'failed_rows' => 0,
            'summary' => [
                'detected_format' => $result['detected_format'],
                'student_count' => (int) $result['student_count'],
                'error_count' => 0,
                'payload_path' => $payloadPath,
            ],
            'validated_at' => now(),
        ]);

        return $batch->fresh(['errors', 'schoolClass', 'arm', 'session', 'term', 'examType']);
    }

    public function commitImport(ResultBatch $batch, int $userId): ResultBatch
    {
        if ($batch->status !== 'validated') {
            throw ValidationException::withMessages([
                'batch' => 'This import batch is not in a valid state for commit.',
            ]);
        }

        $payloadPath = (string) data_get($batch->summary, 'payload_path', '');
        if ($payloadPath === '' || !Storage::disk('local')->exists($payloadPath)) {
            throw ValidationException::withMessages([
                'batch' => 'Validated payload not found. Please upload and validate again.',
            ]);
        }

        $rows = json_decode((string) Storage::disk('local')->get($payloadPath), true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($rows) || empty($rows)) {
            throw ValidationException::withMessages([
                'batch' => 'Validated payload is empty. Nothing to import.',
            ]);
        }

        DB::transaction(function () use ($batch, $rows, $userId) {
            $batch->update(['status' => 'importing']);

            $saved = 0;
            $assessmentType = (string) ($batch->assessment_type ?: 'full_result');
            foreach ($rows as $row) {
                $studentResult = StudentResult::query()
                    ->where('student_id', (int) $row['student_id'])
                    ->where('session_id', $batch->session_id)
                    ->where('term_id', $batch->term_id)
                    ->where('exam_type_id', $batch->exam_type_id)
                    ->first();

                if ($studentResult && $batch->import_mode === 'create_only' && $this->hasImportedAssessment($studentResult, $assessmentType)) {
                    throw ValidationException::withMessages([
                        'import_mode' => "Result already exists for student ID {$row['student_id']} for selected assessment in create-only mode.",
                    ]);
                }

                $items = array_values((array) ($row['items'] ?? []));

                $promotedToClassId = $this->resolvePromotedClassId($batch->school_id, (string) ($row['promoted_to'] ?? ''));

                $studentResult = StudentResult::query()->updateOrCreate(
                    [
                        'student_id' => (int) $row['student_id'],
                        'session_id' => $batch->session_id,
                        'term_id' => $batch->term_id,
                        'exam_type_id' => $batch->exam_type_id,
                    ],
                    [
                        'school_id' => $batch->school_id,
                        'result_batch_id' => $batch->id,
                        'section' => $batch->section,
                        'class_id' => $batch->class_id,
                        'arm_id' => $batch->arm_id,
                        // Recomputed after item merge in refreshStudentResultAggregates().
                        'total_score' => (float) ($studentResult?->total_score ?? 0),
                        'average_score' => (float) ($studentResult?->average_score ?? 0),
                        'promoted_to_class_id' => $promotedToClassId,
                        'attendance_present' => (int) ($row['attendance_present'] ?? 0),
                        'attendance_total' => (int) ($row['attendance_total'] ?? 0),
                        'class_teacher_remark' => $row['class_teacher_remark'] ?? null,
                        'principal_remark' => $row['principal_remark'] ?? null,
                        'principal_signature' => $row['principal_signature'] ?? null,
                        'signed_at' => $row['signed_at'] ?? null,
                        'created_by' => $studentResult?->created_by ?: $userId,
                        'updated_by' => $userId,
                    ]
                );

                $subjectIdsInPayload = [];
                foreach ($items as $item) {
                    $subjectIdsInPayload[] = (int) $item['subject_id'];

                    $existingItem = StudentResultItem::query()->where([
                        'student_result_id' => $studentResult->id,
                        'subject_id' => (int) $item['subject_id'],
                    ])->first();

                    $payload = $this->buildItemPayload($assessmentType, $item, $existingItem, $batch->school_id, $batch->class_id, $batch->section);

                    StudentResultItem::query()->updateOrCreate(
                        [
                            'student_result_id' => $studentResult->id,
                            'subject_id' => (int) $item['subject_id'],
                        ],
                        $payload
                    );
                }

                if ($assessmentType === 'full_result' && $subjectIdsInPayload) {
                    $studentResult->items()
                        ->whereNotIn('subject_id', $subjectIdsInPayload)
                        ->delete();
                }

                $this->refreshStudentResultAggregates($studentResult, $assessmentType, $userId);

                $saved++;
            }

            $this->rankingService->recomputeScope(
                $batch->school_id,
                $batch->class_id,
                $batch->arm_id,
                $batch->session_id,
                $batch->term_id,
                $batch->exam_type_id
            );

            $summary = (array) ($batch->summary ?? []);
            $summary['saved_students'] = $saved;

            $batch->update([
                'status' => 'completed',
                'success_rows' => $saved,
                'failed_rows' => 0,
                'imported_at' => now(),
                'summary' => $summary,
            ]);
        });

        return $batch->fresh(['errors', 'schoolClass', 'arm', 'session', 'term', 'examType']);
    }

    protected function resolvePromotedClassId(int $schoolId, string $promotedTo): ?int
    {
        $promotedTo = trim($promotedTo);
        if ($promotedTo === '') {
            return null;
        }

        $normalized = strtolower(preg_replace('/\s+/', '', $promotedTo));

        return SchoolClass::query()
            ->where('school_id', $schoolId)
            ->get()
            ->first(function (SchoolClass $class) use ($normalized) {
                $name = strtolower(preg_replace('/\s+/', '', (string) $class->name));
                $grade = strtolower(preg_replace('/\s+/', '', (string) ($class->grade_level?->label() ?? '')));
                return $normalized === $name || $normalized === $grade;
            })
            ?->id;
    }

    protected function storeErrors(ResultBatch $batch, array $errors): void
    {
        $payload = array_map(function (array $error) use ($batch) {
            $rowNumber = $error['row_number'] ?? null;
            $rowNumber = is_numeric($rowNumber) ? (int) $rowNumber : null;

            $columnName = $this->normalizeTextValue($error['column_name'] ?? null);
            $errorMessage = $this->normalizeTextValue($error['error_message'] ?? null, 'Validation error');
            $rawPayload = $this->normalizeJsonValue($error['raw_payload'] ?? null);

            return [
                'result_batch_id' => $batch->id,
                'row_number' => $rowNumber,
                'column_name' => $columnName,
                'error_message' => $errorMessage,
                'raw_payload' => $rawPayload,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }, $errors);

        ResultImportError::query()->insert($payload);
    }

    protected function normalizeTextValue(mixed $value, string $fallback = ''): ?string
    {
        if ($value === null) {
            return $fallback !== '' ? $fallback : null;
        }

        if (is_string($value)) {
            $value = trim($value);
            return $value !== '' ? $value : ($fallback !== '' ? $fallback : null);
        }

        if (is_scalar($value)) {
            return (string) $value;
        }

        if (is_array($value) || is_object($value)) {
            try {
                return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
            } catch (\JsonException) {
                return $fallback !== '' ? $fallback : null;
            }
        }

        return $fallback !== '' ? $fallback : null;
    }

    protected function normalizeJsonValue(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (is_array($value) || is_object($value)) {
            try {
                return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
            } catch (\JsonException) {
                return null;
            }
        }

        if (is_string($value)) {
            $value = trim($value);
            return $value !== '' ? $value : null;
        }

        if (is_scalar($value)) {
            return (string) $value;
        }

        return null;
    }

    protected function hasImportedAssessment(StudentResult $studentResult, string $assessmentType): bool
    {
        return match ($assessmentType) {
            'first_test' => !is_null($studentResult->first_test_imported_at)
                || !is_null($studentResult->full_result_imported_at),
            'second_test' => !is_null($studentResult->second_test_imported_at)
                || !is_null($studentResult->full_result_imported_at),
            'exam' => !is_null($studentResult->exam_imported_at)
                || !is_null($studentResult->full_result_imported_at),
            default => true,
        };
    }

    protected function buildItemPayload(
        string $assessmentType,
        array $item,
        ?StudentResultItem $existingItem,
        int $schoolId,
        int $classId,
        ?string $section
    ): array {
        $exam = (float) ($existingItem?->exam_score ?? 0);
        $first = (float) ($existingItem?->first_test_score ?? 0);
        $second = (float) ($existingItem?->second_test_score ?? 0);

        if ($assessmentType === 'full_result') {
            $exam = (float) ($item['exam_score'] ?? 0);
            $first = (float) ($item['first_test_score'] ?? 0);
            $second = (float) ($item['second_test_score'] ?? 0);
        } elseif ($assessmentType === 'first_test') {
            $first = (float) ($item['component_score'] ?? 0);
        } elseif ($assessmentType === 'second_test') {
            $second = (float) ($item['component_score'] ?? 0);
        } elseif ($assessmentType === 'exam') {
            $exam = (float) ($item['component_score'] ?? 0);
        }

        $total = round($exam + $first + $second, 2);
        $grade = $this->gradingService->gradeForScore($total, $schoolId, $classId, (string) $section);

        return [
            'exam_score' => $exam,
            'first_test_score' => $first,
            'second_test_score' => $second,
            'total_score' => $total,
            'grade' => (string) ($grade['grade'] ?? ''),
            'remark' => (string) ($grade['remark'] ?? ''),
        ];
    }

    protected function refreshStudentResultAggregates(StudentResult $studentResult, string $assessmentType, int $userId): void
    {
        $studentResult->load('items');
        $items = $studentResult->items;
        $totalScore = round((float) $items->sum('total_score'), 2);
        $average = $items->count() > 0 ? round($totalScore / $items->count(), 2) : 0;

        $updates = [
            'total_score' => $totalScore,
            'average_score' => $average,
            'updated_by' => $userId,
        ];

        $now = now();
        if ($assessmentType === 'full_result') {
            $updates['full_result_imported_at'] = $now;
            $updates['first_test_imported_at'] = $studentResult->first_test_imported_at ?: $now;
            $updates['second_test_imported_at'] = $studentResult->second_test_imported_at ?: $now;
            $updates['exam_imported_at'] = $studentResult->exam_imported_at ?: $now;
        } elseif ($assessmentType === 'first_test') {
            $updates['first_test_imported_at'] = $now;
        } elseif ($assessmentType === 'second_test') {
            $updates['second_test_imported_at'] = $now;
        } elseif ($assessmentType === 'exam') {
            $updates['exam_imported_at'] = $now;
        }

        $studentResult->update($updates);
    }
}
