<?php

namespace App\Services;

use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentResult;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ResultImportValidatorService
{
    protected array $schoolRegistrationIndex = [];

    public function __construct(private readonly GradingService $gradingService)
    {
    }

    public function validate(string $absoluteFilePath, array $context): array
    {
        $sheet = IOFactory::load($absoluteFilePath)->getActiveSheet();
        $rawRows = $sheet->toArray(null, true, true, false);
        $headerRowIndex = $this->detectHeaderRowIndex($rawRows);
        $assessmentType = strtolower(trim((string) ($context['assessment_type'] ?? 'full_result')));
        $schoolId = (int) $context['school_id'];

        if ($headerRowIndex === null) {
            return $this->response([], [
                $this->invalidFormatError($assessmentType, null),
            ], 0, 'unknown');
        }

        $headers = $this->normalizeHeaders($rawRows[$headerRowIndex] ?? []);
        $dataRows = array_slice($rawRows, $headerRowIndex + 1);
        $format = $assessmentType === 'full_result'
            ? $this->detectFormat($headers)
            : $this->detectComponentFormat($headers, $assessmentType);

        if ($format === 'unknown') {
            return $this->response([], [
                $this->invalidFormatError($assessmentType, $headerRowIndex + 1),
            ], count($dataRows), 'unknown');
        }

        $this->schoolRegistrationIndex = $this->buildSchoolRegistrationIndex(
            Student::withTrashed()
                ->where('school_id', $schoolId)
                ->get()
        );

        $class = SchoolClass::query()
            ->with(['subjects' => fn ($q) => $q->where('is_active', true)->orderBy('name')])
            ->findOrFail((int) $context['class_id']);

        $subjects = $class->subjects->values();
        if ($subjects->isEmpty()) {
            return $this->response([], [
                $this->error(null, null, 'No subjects are assigned to this class. Assign subjects before import.'),
            ], 0, $format);
        }

        $scopeStudents = Student::query()
            ->where('school_id', $schoolId)
            ->where('class_id', (int) $context['class_id'])
            ->when(!empty($context['arm_id']), fn ($q) => $q->where('arm_id', (int) $context['arm_id']))
            ->where('status', 'active')
            ->get();

        $unassignedSchoolStudents = Student::query()
            ->where('school_id', $schoolId)
            ->whereNull('class_id')
            ->where('status', 'active')
            ->get();

        if ($scopeStudents->isEmpty()) {
            return $this->response([], [
                $this->error(null, null, 'No active students found in the selected class/arm scope.'),
            ], 0, $format);
        }

        [$studentMaps, $nameConflicts] = $this->buildStudentMaps($scopeStudents, $unassignedSchoolStudents);
        $subjectLookup = $this->buildSubjectLookup($subjects);

        if ($assessmentType === 'full_result') {
            return $format === 'wide'
                ? $this->validateWideFormat($headers, $dataRows, $headerRowIndex, $context, $subjects->all(), $subjectLookup, $studentMaps, $nameConflicts)
                : $this->validateNormalizedFormat($headers, $dataRows, $headerRowIndex, $context, $subjects->all(), $subjectLookup, $studentMaps, $nameConflicts);
        }

        return $format === 'component_wide'
            ? $this->validateComponentWideFormat($headers, $dataRows, $headerRowIndex, $context, $subjects->all(), $subjectLookup, $studentMaps, $nameConflicts, $assessmentType)
            : $this->validateComponentNormalizedFormat($headers, $dataRows, $headerRowIndex, $context, $subjects->all(), $subjectLookup, $studentMaps, $nameConflicts, $assessmentType);
    }

    protected function validateWideFormat(
        array $headers,
        array $dataRows,
        int $headerRowIndex,
        array $context,
        array $subjects,
        array $subjectLookup,
        array $studentMaps,
        array $nameConflicts
    ): array {
        $errors = [];
        $normalizedRows = [];
        $componentMap = [];
        $expectedSubjectIds = collect($subjects)->pluck('id')->sort()->values()->all();
        $expectedSubjectNames = collect($subjects)->pluck('name')->sort()->values()->all();
        $subjectIdToName = [];
        foreach ($subjects as $subject) {
            $subjectIdToName[$subject->id] = $subject->name;
        }
        $foundAssignedSubjectNames = [];
        $unexpectedSubjectColumns = [];

        $registrationColumn = $this->pickFirstHeader($headers, [
            'admission_number',
            'registration_number',
            'reg_no',
            'student_number',
            'student_id',
        ]);
        $nameColumn = $this->pickFirstHeader($headers, ['student_name', 'full_name', 'name']);

        if ($registrationColumn === null && $nameColumn === null) {
            $errors[] = $this->error(null, null, 'Required student column missing. Include admission/registration number or student name column.');
            return $this->response([], $errors, 0, 'wide');
        }

        foreach ($headers as $columnIndex => $header) {
            if ($header === '') {
                continue;
            }

            [$subjectToken, $component] = $this->extractWideHeaderComponent($header);
            if ($component === null || $subjectToken === null) {
                continue;
            }

            $subjectId = $subjectLookup[$subjectToken] ?? null;
            if ($subjectId === null) {
                $unexpectedSubjectColumns[$header] = $this->humanizeSubjectToken($subjectToken);
                $errors[] = $this->error($headerRowIndex + 1, $header, "Subject column '{$header}' is not assigned to the selected class.");
                continue;
            }

            $componentMap[$subjectId][$component] = $columnIndex;
            if (isset($subjectIdToName[$subjectId])) {
                $subjectName = $subjectIdToName[$subjectId];
                $foundAssignedSubjectNames[$subjectName] = $subjectName;
            }
        }

        foreach ($subjects as $subject) {
            $subjectId = $subject->id;
            foreach (['exam', 'first_test', 'second_test'] as $requiredComponent) {
                if (!isset($componentMap[$subjectId][$requiredComponent])) {
                    $errors[] = $this->error(
                        $headerRowIndex + 1,
                        $subject->name,
                        "Subject '{$subject->name}' is missing required component column '{$requiredComponent}'."
                    );
                }
            }
        }

        $foundNames = array_values($foundAssignedSubjectNames);
        sort($foundNames);
        $missingNames = array_values(array_diff($expectedSubjectNames, $foundNames));
        sort($missingNames);
        $unexpectedNames = array_values(array_unique(array_values($unexpectedSubjectColumns)));
        sort($unexpectedNames);

        if ($missingNames !== [] || $unexpectedNames !== []) {
            $errors[] = $this->subjectMismatchError($headerRowIndex + 1, $expectedSubjectNames, $foundNames, $missingNames, $unexpectedNames);
        }

        if ($errors) {
            return $this->response([], $errors, count($dataRows), 'wide');
        }

        $seenStudentsInFile = [];
        $scoreLimits = $this->scoreLimits();

        foreach ($dataRows as $offset => $rawRow) {
            $sheetRow = $headerRowIndex + 2 + $offset;
            if ($this->rowIsEmpty($rawRow)) {
                continue;
            }

            $reg = $registrationColumn !== null ? trim((string) ($rawRow[$registrationColumn] ?? '')) : '';
            $fullName = $nameColumn !== null ? trim((string) ($rawRow[$nameColumn] ?? '')) : '';

            $student = $this->resolveStudent($reg, $fullName, (int) $context['school_id'], $studentMaps, $nameConflicts, $sheetRow, $errors);
            if (!$student) {
                continue;
            }

            if (isset($seenStudentsInFile[$student->id])) {
                $errors[] = $this->error($sheetRow, 'student', "Duplicate student row detected for '{$student->full_name}'.");
                continue;
            }
            $seenStudentsInFile[$student->id] = true;

            $items = [];
            foreach ($subjects as $subject) {
                $subjectId = $subject->id;
                $exam = $this->parseScore($rawRow[$componentMap[$subjectId]['exam']] ?? null, $sheetRow, "{$subject->name}_exam", $errors, $scoreLimits['exam']);
                $first = $this->parseScore($rawRow[$componentMap[$subjectId]['first_test']] ?? null, $sheetRow, "{$subject->name}_first_test", $errors, $scoreLimits['first_test']);
                $second = $this->parseScore($rawRow[$componentMap[$subjectId]['second_test']] ?? null, $sheetRow, "{$subject->name}_second_test", $errors, $scoreLimits['second_test']);

                if ($exam === null || $first === null || $second === null) {
                    continue;
                }

                $total = round($exam + $first + $second, 2);
                if ($total > $scoreLimits['total']) {
                    $errors[] = $this->error($sheetRow, $subject->name, "Row {$sheetRow}: {$subject->name} total exceeds allowed score range.");
                    continue;
                }

                $grade = $this->gradingService->gradeForScore(
                    $total,
                    (int) $context['school_id'],
                    (int) $context['class_id'],
                    (string) ($context['section'] ?? '')
                );

                $items[$subjectId] = [
                    'subject_id' => $subjectId,
                    'exam_score' => $exam,
                    'first_test_score' => $first,
                    'second_test_score' => $second,
                    'total_score' => $total,
                    'grade' => $grade['grade'],
                    'remark' => $grade['remark'],
                ];
            }

            if (count($items) !== count($expectedSubjectIds)) {
                $errors[] = $this->error($sheetRow, 'subjects', "Row {$sheetRow}: incomplete subject scores for '{$student->full_name}'.");
                continue;
            }

            $normalizedRows[$student->id] = [
                'student_id' => $student->id,
                'promoted_to' => $this->extractRowValue($headers, $rawRow, ['promoted_to', 'promoted_to_class']),
                'attendance_present' => 0,
                'attendance_total' => 0,
                'class_teacher_remark' => $this->extractRowValue($headers, $rawRow, ['class_teacher_remark', 'class_teacher_remarks']),
                'principal_remark' => $this->extractRowValue($headers, $rawRow, ['principal_remark', 'principal_remarks']),
                'principal_signature' => $this->extractRowValue($headers, $rawRow, ['principal_signature']),
                'signed_at' => $this->parseOptionalDate($this->extractRowValue($headers, $rawRow, ['date', 'signed_at'])),
                'items' => $items,
            ];
        }

        $errors = array_merge($errors, $this->validateExistingScopeDuplicates(array_keys($normalizedRows), $context));

        return $this->response(array_values($normalizedRows), $errors, count($dataRows), 'wide');
    }

    protected function validateNormalizedFormat(
        array $headers,
        array $dataRows,
        int $headerRowIndex,
        array $context,
        array $subjects,
        array $subjectLookup,
        array $studentMaps,
        array $nameConflicts
    ): array {
        $errors = [];
        $normalizedRows = [];
        $expectedSubjectIds = collect($subjects)->pluck('id')->sort()->values()->all();
        $expectedSubjectNames = collect($subjects)->pluck('name')->sort()->values()->all();
        $subjectIdToName = [];
        foreach ($subjects as $subject) {
            $subjectIdToName[$subject->id] = $subject->name;
        }
        $foundAssignedSubjectNames = [];
        $unexpectedFileSubjects = [];
        $scoreLimits = $this->scoreLimits();

        $registrationColumn = $this->pickFirstHeader($headers, [
            'admission_number',
            'registration_number',
            'reg_no',
            'student_number',
            'student_id',
        ]);
        $nameColumn = $this->pickFirstHeader($headers, ['student_name', 'full_name', 'name']);
        $subjectColumn = $this->pickFirstHeader($headers, ['subject', 'subject_name']);
        $examColumn = $this->pickFirstHeader($headers, ['exam', 'exam_score', 'examination']);
        $firstColumn = $this->pickFirstHeader($headers, ['first_test', 'ca1', 'test1']);
        $secondColumn = $this->pickFirstHeader($headers, ['second_test', 'ca2', 'test2']);

        if ($subjectColumn === null || $examColumn === null || $firstColumn === null || $secondColumn === null) {
            $errors[] = $this->error(
                $headerRowIndex + 1,
                'headers',
                'Required normalized columns missing. Expected subject, exam, first_test, second_test headers.'
            );
            return $this->response([], $errors, count($dataRows), 'normalized');
        }

        if ($registrationColumn === null && $nameColumn === null) {
            $errors[] = $this->error(
                $headerRowIndex + 1,
                'headers',
                'Required student columns missing. Provide admission/registration number or student name.'
            );
            return $this->response([], $errors, count($dataRows), 'normalized');
        }

        $seenStudentSubjectKeys = [];
        $lastReg = '';
        $lastName = '';

        foreach ($dataRows as $offset => $rawRow) {
            $sheetRow = $headerRowIndex + 2 + $offset;
            if ($this->rowIsEmpty($rawRow)) {
                continue;
            }

            $reg = $registrationColumn !== null ? trim((string) ($rawRow[$registrationColumn] ?? '')) : '';
            $name = $nameColumn !== null ? trim((string) ($rawRow[$nameColumn] ?? '')) : '';
            $subjectName = trim((string) ($rawRow[$subjectColumn] ?? ''));

            if ($reg !== '') {
                $lastReg = $reg;
            }
            if ($name !== '') {
                $lastName = $name;
            }
            if ($reg === '' && $name === '') {
                $reg = $lastReg;
                $name = $lastName;
            }

            $student = $this->resolveStudent($reg, $name, (int) $context['school_id'], $studentMaps, $nameConflicts, $sheetRow, $errors);
            if (!$student) {
                continue;
            }

            if ($subjectName === '') {
                // Allow non-subject continuation rows only for optional summary values.
                continue;
            }

            $cleanSubjectDisplay = trim(preg_replace('/\s+/', ' ', $subjectName) ?? $subjectName);

            $subjectToken = $this->normalizeSubjectToken($subjectName);
            $subjectId = $subjectLookup[$subjectToken] ?? null;
            if ($subjectId === null) {
                $unexpectedFileSubjects[$cleanSubjectDisplay] = $cleanSubjectDisplay;
                $errors[] = $this->error($sheetRow, 'subject', "Subject '{$subjectName}' is not assigned to the selected class.");
                continue;
            }
            if (isset($subjectIdToName[$subjectId])) {
                $foundAssignedSubjectNames[$subjectIdToName[$subjectId]] = $subjectIdToName[$subjectId];
            }

            $dedupeKey = $student->id . ':' . $subjectId;
            if (isset($seenStudentSubjectKeys[$dedupeKey])) {
                $errors[] = $this->error($sheetRow, 'subject', "Duplicate subject row detected for {$student->full_name} - {$subjectName}.");
                continue;
            }
            $seenStudentSubjectKeys[$dedupeKey] = true;

            $exam = $this->parseScore($rawRow[$examColumn] ?? null, $sheetRow, 'exam', $errors, $scoreLimits['exam']);
            $first = $this->parseScore($rawRow[$firstColumn] ?? null, $sheetRow, 'first_test', $errors, $scoreLimits['first_test']);
            $second = $this->parseScore($rawRow[$secondColumn] ?? null, $sheetRow, 'second_test', $errors, $scoreLimits['second_test']);

            if ($exam === null || $first === null || $second === null) {
                continue;
            }

            $total = round($exam + $first + $second, 2);
            if ($total > $scoreLimits['total']) {
                $errors[] = $this->error($sheetRow, 'total', "Row {$sheetRow}: {$subjectName} total exceeds allowed score range.");
                continue;
            }

            $grade = $this->gradingService->gradeForScore(
                $total,
                (int) $context['school_id'],
                (int) $context['class_id'],
                (string) ($context['section'] ?? '')
            );

            $normalizedRows[$student->id] ??= [
                'student_id' => $student->id,
                'promoted_to' => null,
                'attendance_present' => 0,
                'attendance_total' => 0,
                'class_teacher_remark' => null,
                'principal_remark' => null,
                'principal_signature' => null,
                'signed_at' => null,
                'items' => [],
            ];

            $normalizedRows[$student->id]['items'][$subjectId] = [
                'subject_id' => $subjectId,
                'exam_score' => $exam,
                'first_test_score' => $first,
                'second_test_score' => $second,
                'total_score' => $total,
                'grade' => $grade['grade'],
                'remark' => $grade['remark'],
            ];

            $normalizedRows[$student->id]['class_teacher_remark'] = $this->extractRowValue($headers, $rawRow, ['class_teacher_remark', 'class_teacher_remarks']) ?: $normalizedRows[$student->id]['class_teacher_remark'];
            $normalizedRows[$student->id]['principal_remark'] = $this->extractRowValue($headers, $rawRow, ['principal_remark', 'principal_remarks']) ?: $normalizedRows[$student->id]['principal_remark'];
            $normalizedRows[$student->id]['principal_signature'] = $this->extractRowValue($headers, $rawRow, ['principal_signature']) ?: $normalizedRows[$student->id]['principal_signature'];

            $signDate = $this->parseOptionalDate($this->extractRowValue($headers, $rawRow, ['date', 'signed_at']));
            if ($signDate) {
                $normalizedRows[$student->id]['signed_at'] = $signDate;
            }

            $promotedTo = $this->extractRowValue($headers, $rawRow, ['promoted_to', 'promoted_to_class']);
            if ($promotedTo) {
                $normalizedRows[$student->id]['promoted_to'] = $promotedTo;
            }
        }

        foreach ($normalizedRows as $studentId => $row) {
            $found = array_keys($row['items']);
            sort($found);
            if ($found !== $expectedSubjectIds) {
                $student = $studentMaps['id'][$studentId] ?? null;
                $errors[] = $this->error(
                    null,
                    'subjects',
                    "Student {$student?->full_name} is missing one or more required class subjects."
                );
            }
        }

        $foundNames = array_values($foundAssignedSubjectNames);
        sort($foundNames);
        $missingNames = array_values(array_diff($expectedSubjectNames, $foundNames));
        sort($missingNames);
        $unexpectedNames = array_values(array_unique(array_values($unexpectedFileSubjects)));
        sort($unexpectedNames);

        if ($missingNames !== [] || $unexpectedNames !== []) {
            $errors[] = $this->subjectMismatchError($headerRowIndex + 1, $expectedSubjectNames, $foundNames, $missingNames, $unexpectedNames);
        }

        $errors = array_merge($errors, $this->validateExistingScopeDuplicates(array_keys($normalizedRows), $context));

        return $this->response(array_values($normalizedRows), $errors, count($dataRows), 'normalized');
    }

    protected function validateComponentWideFormat(
        array $headers,
        array $dataRows,
        int $headerRowIndex,
        array $context,
        array $subjects,
        array $subjectLookup,
        array $studentMaps,
        array $nameConflicts,
        string $assessmentType
    ): array {
        $errors = [];
        $normalizedRows = [];
        $subjectColumnMap = [];
        $expectedSubjectIds = collect($subjects)->pluck('id')->sort()->values()->all();
        $expectedSubjectNames = collect($subjects)->pluck('name')->sort()->values()->all();
        $subjectIdToName = [];
        foreach ($subjects as $subject) {
            $subjectIdToName[$subject->id] = $subject->name;
        }

        $registrationColumn = $this->pickFirstHeader($headers, [
            'admission_number',
            'registration_number',
            'reg_no',
            'student_number',
            'student_id',
        ]);
        $nameColumn = $this->pickFirstHeader($headers, ['student_name', 'full_name', 'name']);

        if ($registrationColumn === null && $nameColumn === null) {
            $errors[] = $this->error(null, null, 'Required student column missing. Include admission/registration number or student name column.');
            return $this->response([], $errors, 0, 'component_wide');
        }

        $reservedHeaders = [
            'admission_number',
            'registration_number',
            'reg_no',
            'student_number',
            'student_id',
            'student_name',
            'full_name',
            'name',
            'attendance',
            'class_teacher_remark',
            'principal_remark',
            'promoted_to',
            'date',
        ];

        $foundAssignedSubjectNames = [];
        $unexpectedFileSubjects = [];

        foreach ($headers as $columnIndex => $header) {
            if ($header === '' || in_array($header, $reservedHeaders, true)) {
                continue;
            }

            $subjectToken = $this->normalizeSubjectToken($header);
            $subjectId = $subjectLookup[$subjectToken] ?? null;
            if ($subjectId === null) {
                $display = $this->humanizeSubjectToken($subjectToken);
                $unexpectedFileSubjects[$display] = $display;
                $errors[] = $this->error($headerRowIndex + 1, $header, "Subject '{$header}' is not assigned to the selected class.");
                continue;
            }

            $subjectColumnMap[$subjectId] = $columnIndex;
            if (isset($subjectIdToName[$subjectId])) {
                $foundAssignedSubjectNames[$subjectIdToName[$subjectId]] = $subjectIdToName[$subjectId];
            }
        }

        foreach ($subjects as $subject) {
            if (!isset($subjectColumnMap[$subject->id])) {
                $errors[] = $this->error(
                    $headerRowIndex + 1,
                    $subject->name,
                    "Subject '{$subject->name}' column is missing for {$assessmentType} import."
                );
            }
        }

        $foundNames = array_values($foundAssignedSubjectNames);
        sort($foundNames);
        $missingNames = array_values(array_diff($expectedSubjectNames, $foundNames));
        sort($missingNames);
        $unexpectedNames = array_values(array_unique(array_values($unexpectedFileSubjects)));
        sort($unexpectedNames);

        if ($missingNames !== [] || $unexpectedNames !== []) {
            $errors[] = $this->subjectMismatchError($headerRowIndex + 1, $expectedSubjectNames, $foundNames, $missingNames, $unexpectedNames);
        }

        if ($errors) {
            return $this->response([], $errors, count($dataRows), 'component_wide');
        }

        $seenStudentsInFile = [];
        $componentMax = $this->scoreLimitForAssessment($assessmentType);

        foreach ($dataRows as $offset => $rawRow) {
            $sheetRow = $headerRowIndex + 2 + $offset;
            if ($this->rowIsEmpty($rawRow)) {
                continue;
            }

            $reg = $registrationColumn !== null ? trim((string) ($rawRow[$registrationColumn] ?? '')) : '';
            $fullName = $nameColumn !== null ? trim((string) ($rawRow[$nameColumn] ?? '')) : '';

            $student = $this->resolveStudent($reg, $fullName, (int) $context['school_id'], $studentMaps, $nameConflicts, $sheetRow, $errors);
            if (!$student) {
                continue;
            }

            if (isset($seenStudentsInFile[$student->id])) {
                $errors[] = $this->error($sheetRow, 'student', "Duplicate student row detected for '{$student->full_name}'.");
                continue;
            }
            $seenStudentsInFile[$student->id] = true;

            $items = [];
            foreach ($subjects as $subject) {
                $score = $this->parseScore(
                    $rawRow[$subjectColumnMap[$subject->id]] ?? null,
                    $sheetRow,
                    "{$subject->name}_{$assessmentType}",
                    $errors,
                    $componentMax
                );

                if ($score === null) {
                    continue;
                }

                $items[$subject->id] = [
                    'subject_id' => $subject->id,
                    'component_score' => $score,
                ];
            }

            if (count($items) !== count($expectedSubjectIds)) {
                $errors[] = $this->error($sheetRow, 'subjects', "Row {$sheetRow}: incomplete subject scores for '{$student->full_name}'.");
                continue;
            }

            $normalizedRows[$student->id] = [
                'student_id' => $student->id,
                'items' => $items,
            ];
        }

        $errors = array_merge($errors, $this->validateExistingScopeDuplicates(array_keys($normalizedRows), $context));

        return $this->response(array_values($normalizedRows), $errors, count($dataRows), 'component_wide');
    }

    protected function validateComponentNormalizedFormat(
        array $headers,
        array $dataRows,
        int $headerRowIndex,
        array $context,
        array $subjects,
        array $subjectLookup,
        array $studentMaps,
        array $nameConflicts,
        string $assessmentType
    ): array {
        $errors = [];
        $normalizedRows = [];
        $expectedSubjectIds = collect($subjects)->pluck('id')->sort()->values()->all();
        $expectedSubjectNames = collect($subjects)->pluck('name')->sort()->values()->all();
        $subjectIdToName = [];
        foreach ($subjects as $subject) {
            $subjectIdToName[$subject->id] = $subject->name;
        }

        $registrationColumn = $this->pickFirstHeader($headers, [
            'admission_number',
            'registration_number',
            'reg_no',
            'student_number',
            'student_id',
        ]);
        $nameColumn = $this->pickFirstHeader($headers, ['student_name', 'full_name', 'name']);
        $subjectColumn = $this->pickFirstHeader($headers, ['subject', 'subject_name']);
        $scoreColumn = $this->pickFirstHeader($headers, $this->componentScoreColumnCandidates($assessmentType));

        if ($subjectColumn === null || $scoreColumn === null) {
            $errors[] = $this->error(
                $headerRowIndex + 1,
                'headers',
                "Required component columns missing. Expected subject and score/{$assessmentType} headers."
            );
            return $this->response([], $errors, count($dataRows), 'component_normalized');
        }

        if ($registrationColumn === null && $nameColumn === null) {
            $errors[] = $this->error(
                $headerRowIndex + 1,
                'headers',
                'Required student columns missing. Provide admission/registration number or student name.'
            );
            return $this->response([], $errors, count($dataRows), 'component_normalized');
        }

        $componentMax = $this->scoreLimitForAssessment($assessmentType);
        $seenStudentSubjectKeys = [];
        $lastReg = '';
        $lastName = '';
        $foundAssignedSubjectNames = [];
        $unexpectedFileSubjects = [];

        foreach ($dataRows as $offset => $rawRow) {
            $sheetRow = $headerRowIndex + 2 + $offset;
            if ($this->rowIsEmpty($rawRow)) {
                continue;
            }

            $reg = $registrationColumn !== null ? trim((string) ($rawRow[$registrationColumn] ?? '')) : '';
            $name = $nameColumn !== null ? trim((string) ($rawRow[$nameColumn] ?? '')) : '';
            $subjectName = trim((string) ($rawRow[$subjectColumn] ?? ''));

            if ($reg !== '') {
                $lastReg = $reg;
            }
            if ($name !== '') {
                $lastName = $name;
            }
            if ($reg === '' && $name === '') {
                $reg = $lastReg;
                $name = $lastName;
            }

            $student = $this->resolveStudent($reg, $name, (int) $context['school_id'], $studentMaps, $nameConflicts, $sheetRow, $errors);
            if (!$student) {
                continue;
            }

            if ($subjectName === '') {
                continue;
            }

            $subjectToken = $this->normalizeSubjectToken($subjectName);
            $subjectId = $subjectLookup[$subjectToken] ?? null;
            if ($subjectId === null) {
                $display = trim(preg_replace('/\s+/', ' ', $subjectName) ?? $subjectName);
                $unexpectedFileSubjects[$display] = $display;
                $errors[] = $this->error($sheetRow, 'subject', "Subject '{$subjectName}' is not assigned to the selected class.");
                continue;
            }
            if (isset($subjectIdToName[$subjectId])) {
                $foundAssignedSubjectNames[$subjectIdToName[$subjectId]] = $subjectIdToName[$subjectId];
            }

            $dedupeKey = $student->id . ':' . $subjectId;
            if (isset($seenStudentSubjectKeys[$dedupeKey])) {
                $errors[] = $this->error($sheetRow, 'subject', "Duplicate subject row detected for {$student->full_name} - {$subjectName}.");
                continue;
            }
            $seenStudentSubjectKeys[$dedupeKey] = true;

            $score = $this->parseScore($rawRow[$scoreColumn] ?? null, $sheetRow, $assessmentType, $errors, $componentMax);
            if ($score === null) {
                continue;
            }

            $normalizedRows[$student->id] ??= [
                'student_id' => $student->id,
                'items' => [],
            ];

            $normalizedRows[$student->id]['items'][$subjectId] = [
                'subject_id' => $subjectId,
                'component_score' => $score,
            ];
        }

        foreach ($normalizedRows as $studentId => $row) {
            $found = array_keys($row['items']);
            sort($found);
            if ($found !== $expectedSubjectIds) {
                $student = $studentMaps['id'][$studentId] ?? null;
                $errors[] = $this->error(
                    null,
                    'subjects',
                    "Student {$student?->full_name} is missing one or more required class subjects."
                );
            }
        }

        $foundNames = array_values($foundAssignedSubjectNames);
        sort($foundNames);
        $missingNames = array_values(array_diff($expectedSubjectNames, $foundNames));
        sort($missingNames);
        $unexpectedNames = array_values(array_unique(array_values($unexpectedFileSubjects)));
        sort($unexpectedNames);

        if ($missingNames !== [] || $unexpectedNames !== []) {
            $errors[] = $this->subjectMismatchError($headerRowIndex + 1, $expectedSubjectNames, $foundNames, $missingNames, $unexpectedNames);
        }

        $errors = array_merge($errors, $this->validateExistingScopeDuplicates(array_keys($normalizedRows), $context));

        return $this->response(array_values($normalizedRows), $errors, count($dataRows), 'component_normalized');
    }

    protected function validateExistingScopeDuplicates(array $studentIds, array $context): array
    {
        if (($context['import_mode'] ?? 'create_only') !== 'create_only' || empty($studentIds)) {
            return [];
        }

        $assessmentType = strtolower(trim((string) ($context['assessment_type'] ?? 'full_result')));

        $query = StudentResult::query()
            ->whereIn('student_id', $studentIds)
            ->where('session_id', (int) $context['session_id'])
            ->where('term_id', (int) $context['term_id'])
            ->where('exam_type_id', (int) $context['exam_type_id'])
            ->with('student');

        if ($assessmentType !== 'full_result') {
            $column = match ($assessmentType) {
                'first_test' => 'first_test_imported_at',
                'second_test' => 'second_test_imported_at',
                'exam' => 'exam_imported_at',
                default => 'full_result_imported_at',
            };

            $query->where(function ($q) use ($column) {
                $q->whereNotNull($column)
                    ->orWhereNotNull('full_result_imported_at');
            });
        }

        $existing = $query->get();

        $assessmentLabel = match ($assessmentType) {
            'first_test' => 'First Test',
            'second_test' => 'Second Test',
            'exam' => 'Exam',
            default => 'Full Terminal Result',
        };

        return $existing->map(function (StudentResult $row) use ($assessmentLabel) {
            $studentNo = $row->student?->admission_number ?: ('ID ' . $row->student_id);
            return $this->error(
                null,
                'student',
                "{$assessmentLabel} already exists for student {$studentNo}, class scope, term, session, and selected exam type."
            );
        })->all();
    }

    protected function buildStudentMaps($students, $unassignedStudents = null): array
    {
        $idMap = [];
        $regMap = [];
        $nameMap = [];
        $nameConflicts = [];

        foreach ($students as $student) {
            $idMap[$student->id] = $student;
            if ($student->admission_number) {
                $regMap[strtolower(trim((string) $student->admission_number))] = $student;
            }
            if ($student->registration_number) {
                $regMap[strtolower(trim((string) $student->registration_number))] = $student;
            }

            $fullName = $this->normalizeStudentName($student->full_name);
            if (isset($nameMap[$fullName])) {
                $nameConflicts[$fullName] = true;
            } else {
                $nameMap[$fullName] = $student;
            }
        }

        if ($unassignedStudents) {
            foreach ($unassignedStudents as $student) {
                if ($student->admission_number) {
                    $key = strtolower(trim((string) $student->admission_number));
                    $regMap[$key] ??= $student;
                }
                if ($student->registration_number) {
                    $key = strtolower(trim((string) $student->registration_number));
                    $regMap[$key] ??= $student;
                }
            }
        }

        return [
            [
                'id' => $idMap,
                'reg' => $regMap,
                'name' => $nameMap,
            ],
            $nameConflicts,
        ];
    }

    protected function buildSubjectLookup(iterable $subjects): array
    {
        $lookup = [];

        foreach ($subjects as $subject) {
            $lookup[$this->normalizeSubjectToken($subject->name)] = $subject->id;
            if ($subject->code) {
                $lookup[$this->normalizeSubjectToken($subject->code)] = $subject->id;
            }
        }

        return $lookup;
    }

    protected function resolveStudent(
        string $registration,
        string $fullName,
        int $schoolId,
        array $studentMaps,
        array $nameConflicts,
        int $sheetRow,
        array &$errors
    ): ?Student {
        $regKey = strtolower(trim($registration));
        if ($regKey !== '' && !Student::isValidRegistrationNumberForSchool($registration, $schoolId)) {
            $expectedPrefix = Student::admissionPrefixForSchool($schoolId);
            $errors[] = $this->error(
                $sheetRow,
                'student_number',
                "Student number '{$registration}' is invalid. Use format {$expectedPrefix}/YYYY/NNNN (example: {$expectedPrefix}/2026/0001)."
            );
            return null;
        }

        if ($regKey !== '' && isset($studentMaps['reg'][$regKey])) {
            return $studentMaps['reg'][$regKey];
        }

        if ($regKey !== '' && isset($this->schoolRegistrationIndex[$regKey])) {
            $candidate = $this->schoolRegistrationIndex[$regKey];
            if (!is_null($candidate->deleted_at)) {
                $errors[] = $this->error(
                    $sheetRow,
                    'student',
                    "Student {$registration} is archived/deleted. Restore the student record before import."
                );
                return null;
            }

            if ((string) $candidate->status !== 'active') {
                $errors[] = $this->error(
                    $sheetRow,
                    'student',
                    "Student {$registration} is not active. Activate the student record before import."
                );
                return null;
            }

            if (empty($candidate->class_id)) {
                $errors[] = $this->error(
                    $sheetRow,
                    'student',
                    "Student {$registration} is not assigned to any class/arm. Assign class/arm before import."
                );
                return null;
            }

            $errors[] = $this->error(
                $sheetRow,
                'student',
                "Student {$registration} belongs to another class/arm. Import using the student's assigned class/arm."
            );
            return null;
        }

        $nameKey = $this->normalizeStudentName($fullName);
        if ($nameKey !== '' && isset($studentMaps['name'][$nameKey])) {
            if (isset($nameConflicts[$nameKey])) {
                $errors[] = $this->error($sheetRow, 'student_name', "Row {$sheetRow}: student name '{$fullName}' is ambiguous. Use admission number.");
                return null;
            }

            return $studentMaps['name'][$nameKey];
        }

        $identity = $registration !== '' ? $registration : $fullName;
        $errors[] = $this->error($sheetRow, 'student', "Student {$identity} does not belong to the selected class/arm.");

        return null;
    }

    protected function parseScore(mixed $value, int $sheetRow, string $column, array &$errors, float $max): ?float
    {
        $raw = trim((string) $value);
        if ($raw === '') {
            return 0.0;
        }

        $raw = str_replace(',', '', $raw);
        if (!is_numeric($raw)) {
            $errors[] = $this->error($sheetRow, $column, "Row {$sheetRow}: {$column} score must be numeric.");
            return null;
        }

        $score = (float) $raw;
        if ($score < 0 || $score > $max) {
            $errors[] = $this->error($sheetRow, $column, "Row {$sheetRow}: {$column} score must be between 0 and {$max}.");
            return null;
        }

        return round($score, 2);
    }

    protected function parseOptionalDate(?string $value): ?string
    {
        if (!$value) {
            return null;
        }

        try {
            return Carbon::parse($value)->toDateString();
        } catch (\Throwable) {
            return null;
        }
    }

    protected function scoreLimits(): array
    {
        return [
            'first_test' => (float) config('ems.assessment.ca1_max', 20),
            'second_test' => (float) config('ems.assessment.ca2_max', 20),
            'exam' => (float) config('ems.assessment.exam_max', 60),
            'total' => (float) config('ems.assessment.total_max', 100),
        ];
    }

    protected function scoreLimitForAssessment(string $assessmentType): float
    {
        $limits = $this->scoreLimits();

        return match ($assessmentType) {
            'first_test' => (float) ($limits['first_test'] ?? 20),
            'second_test' => (float) ($limits['second_test'] ?? 20),
            'exam' => (float) ($limits['exam'] ?? 60),
            default => (float) ($limits['total'] ?? 100),
        };
    }

    protected function detectHeaderRowIndex(array $rows): ?int
    {
        foreach ($rows as $index => $row) {
            $cells = array_filter(array_map(fn ($v) => trim((string) $v), (array) $row), fn ($v) => $v !== '');
            if (!$cells) {
                continue;
            }

            $joined = strtolower(implode(' ', $cells));
            if (str_contains($joined, 'subject') || str_contains($joined, 'exam') || str_contains($joined, 'test')) {
                return $index;
            }
        }

        return null;
    }

    protected function normalizeHeaders(array $headerRow): array
    {
        return array_map(function ($header) {
            $normalized = strtolower(trim((string) $header));
            $normalized = preg_replace('/[\x{FEFF}\x{200B}]/u', '', $normalized);
            $normalized = preg_replace('/[^a-z0-9]+/i', '_', $normalized);
            return trim((string) $normalized, '_');
        }, $headerRow);
    }

    protected function detectFormat(array $headers): string
    {
        $hasStudentIdentifier = $this->hasAnyHeader($headers, [
            'admission_number',
            'registration_number',
            'reg_no',
            'student_number',
            'student_id',
            'student_name',
            'full_name',
            'name',
        ]);
        $hasSubjectColumn = in_array('subject', $headers, true) || in_array('subject_name', $headers, true);
        $hasExamColumn = in_array('exam', $headers, true)
            || in_array('exam_score', $headers, true)
            || in_array('examination', $headers, true);
        $hasFirstTestColumn = in_array('first_test', $headers, true) || in_array('ca1', $headers, true) || in_array('test1', $headers, true);
        $hasSecondTestColumn = in_array('second_test', $headers, true) || in_array('ca2', $headers, true) || in_array('test2', $headers, true);

        // Prefer normalized mode when explicit row-based subject columns are present.
        if ($hasStudentIdentifier && $hasSubjectColumn && $hasExamColumn && $hasFirstTestColumn && $hasSecondTestColumn) {
            return 'normalized';
        }

        $hasWideExam = false;
        $hasWideFirst = false;
        $hasWideSecond = false;
        foreach ($headers as $header) {
            $header = (string) $header;
            if (preg_match('/_(exam|exam_score|examination)$/', $header)) {
                $hasWideExam = true;
            }
            if (preg_match('/_(first_test|ca1|test1)$/', $header)) {
                $hasWideFirst = true;
            }
            if (preg_match('/_(second_test|ca2|test2)$/', $header)) {
                $hasWideSecond = true;
            }
        }

        if ($hasStudentIdentifier && $hasWideExam && $hasWideFirst && $hasWideSecond) {
            return 'wide';
        }

        return 'unknown';
    }

    protected function detectComponentFormat(array $headers, string $assessmentType): string
    {
        $hasStudentIdentifier = $this->hasAnyHeader($headers, [
            'admission_number',
            'registration_number',
            'reg_no',
            'student_number',
            'student_id',
            'student_name',
            'full_name',
            'name',
        ]);
        $hasSubjectColumn = in_array('subject', $headers, true) || in_array('subject_name', $headers, true);
        $hasScoreColumn = false;
        foreach ($this->componentScoreColumnCandidates($assessmentType) as $candidate) {
            if (in_array($candidate, $headers, true)) {
                $hasScoreColumn = true;
                break;
            }
        }

        if ($hasStudentIdentifier && $hasSubjectColumn && $hasScoreColumn) {
            return 'component_normalized';
        }

        $reservedHeaders = [
            'admission_number',
            'registration_number',
            'reg_no',
            'student_number',
            'student_id',
            'student_name',
            'full_name',
            'name',
            'subject',
            'subject_name',
            'score',
            'first_test',
            'second_test',
            'exam',
            'exam_score',
            'examination',
            'ca1',
            'ca2',
            'test1',
            'test2',
            'attendance',
            'class_teacher_remark',
            'principal_remark',
            'promoted_to',
            'date',
        ];

        $hasWideSubjectColumns = false;
        foreach ($headers as $header) {
            if ($header === '') {
                continue;
            }
            if (!in_array($header, $reservedHeaders, true)) {
                $hasWideSubjectColumns = true;
                break;
            }
        }

        if ($hasStudentIdentifier && $hasWideSubjectColumns) {
            return 'component_wide';
        }

        return 'unknown';
    }

    protected function hasAnyHeader(array $headers, array $candidates): bool
    {
        foreach ($candidates as $candidate) {
            if (in_array($candidate, $headers, true)) {
                return true;
            }
        }

        return false;
    }

    protected function invalidFormatError(string $assessmentType, ?int $rowNumber): array
    {
        $message = match ($assessmentType) {
            'first_test' => 'Result import is not valid. Use this format: Student Number | Full Name | Subject | First Test (or Score).',
            'second_test' => 'Result import is not valid. Use this format: Student Number | Full Name | Subject | Second Test (or Score).',
            'exam' => 'Result import is not valid. Use this format: Student Number | Full Name | Subject | Examination (or Score).',
            default => 'Result import is not valid. Use this format: Student Number | Full Name | Subject | First Test | Second Test | Examination.',
        };

        return $this->error($rowNumber, 'file', $message, [
            'type' => 'invalid_import_format',
            'assessment_type' => $assessmentType,
        ]);
    }

    protected function componentScoreColumnCandidates(string $assessmentType): array
    {
        return match ($assessmentType) {
            'first_test' => ['score', 'first_test', 'ca1', 'test1'],
            'second_test' => ['score', 'second_test', 'ca2', 'test2'],
            'exam' => ['score', 'exam', 'exam_score', 'examination'],
            default => ['score'],
        };
    }

    protected function extractWideHeaderComponent(string $header): array
    {
        $patterns = [
            'first_test' => '/_(first_test|ca1|test1)$/',
            'second_test' => '/_(second_test|ca2|test2)$/',
            'exam' => '/_(exam|exam_score|examination)$/',
        ];

        foreach ($patterns as $component => $pattern) {
            if (preg_match($pattern, $header)) {
                $token = preg_replace($pattern, '', $header);
                $token = $this->normalizeSubjectToken($token ?? '');
                return [$token, $component];
            }
        }

        return [null, null];
    }

    protected function normalizeSubjectToken(string $value): string
    {
        $token = $this->normalizeToken($value);

        $aliases = [
            'mathamatics' => 'mathematics',
            'maths' => 'mathematics',
            'english' => 'english studies',
            'english language' => 'english studies',
            'christian religious study' => 'christian religious studies',
            'history study' => 'history studies',
            'national values' => 'national value',
        ];

        return $aliases[$token] ?? $token;
    }

    protected function normalizeStudentName(string $name): string
    {
        return $this->normalizeToken($name);
    }

    protected function normalizeToken(string $value): string
    {
        $value = strtolower(trim($value));
        $value = str_replace('&', ' and ', $value);
        $value = preg_replace('/[^a-z0-9]+/i', ' ', $value);
        return trim((string) preg_replace('/\s+/', ' ', $value));
    }

    protected function buildSchoolRegistrationIndex($students): array
    {
        $index = [];

        foreach ($students as $student) {
            if (!empty($student->admission_number)) {
                $key = strtolower(trim((string) $student->admission_number));
                $index[$key] = $student;
            }

            if (!empty($student->registration_number)) {
                $key = strtolower(trim((string) $student->registration_number));
                $index[$key] = $student;
            }
        }

        return $index;
    }

    protected function pickFirstHeader(array $headers, array $candidates): ?int
    {
        foreach ($headers as $index => $header) {
            if (in_array($header, $candidates, true)) {
                return $index;
            }
        }

        return null;
    }

    protected function extractRowValue(array $headers, array $row, array $candidates): ?string
    {
        $columnIndex = $this->pickFirstHeader($headers, $candidates);
        if ($columnIndex === null) {
            return null;
        }

        $value = trim((string) ($row[$columnIndex] ?? ''));
        return $value !== '' ? $value : null;
    }

    protected function rowIsEmpty(array $row): bool
    {
        foreach ($row as $cell) {
            if (trim((string) $cell) !== '') {
                return false;
            }
        }

        return true;
    }

    protected function subjectMismatchError(
        ?int $rowNumber,
        array $expectedSubjects,
        array $foundSubjects,
        array $missingSubjects,
        array $unexpectedSubjects
    ): array {
        $message = 'Subject mismatch detected between uploaded file and class subject assignment.';

        return $this->error(
            $rowNumber,
            'subjects',
            $message,
            [
                'type' => 'subject_set_mismatch',
                'expected_subjects' => array_values($expectedSubjects),
                'found_subjects' => array_values($foundSubjects),
                'missing_subjects' => array_values($missingSubjects),
                'unexpected_subjects' => array_values($unexpectedSubjects),
            ]
        );
    }

    protected function humanizeSubjectToken(string $token): string
    {
        $token = str_replace('_', ' ', $token);
        $token = preg_replace('/\s+/', ' ', $token);
        return ucwords(trim((string) $token));
    }

    protected function error(?int $rowNumber, ?string $columnName, string $message, ?array $rawPayload = null): array
    {
        return [
            'row_number' => $rowNumber,
            'column_name' => $columnName,
            'error_message' => $message,
            'raw_payload' => $rawPayload,
        ];
    }

    protected function response(array $rows, array $errors, int $totalRows, string $format): array
    {
        return [
            'rows' => $rows,
            'errors' => $errors,
            'total_rows' => $totalRows,
            'student_count' => count($rows),
            'detected_format' => $format,
        ];
    }
}
