<?php

namespace App\Services;

use App\Models\ReportCard;
use App\Models\SchoolHoliday;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\StudentResult;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class AttendanceSheetImportService
{
    public function importMonthlySheet(UploadedFile $file, array $context, int $userId): array
    {
        $sheet = IOFactory::load($file->getRealPath())->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, false);
        $headerRowIndex = $this->detectHeaderRowIndex($rows);

        if ($headerRowIndex === null) {
            return [
                'rows_read' => 0,
                'students_matched' => 0,
                'records_written' => 0,
                'errors' => [
                    $this->error(null, 'file', 'Attendance import is not valid. Use the attendance template with Student Number, Full Name, day columns (1-31), and P/A marks.'),
                ],
            ];
        }

        $headers = $this->normalizeHeaders((array) ($rows[$headerRowIndex] ?? []));
        $studentNumberColumn = $this->pickFirstHeader($headers, ['student_number', 'admission_number', 'registration_number', 'reg_no']);
        $fullNameColumn = $this->pickFirstHeader($headers, ['full_name', 'student_name', 'name']);
        $dayColumnMap = $this->extractDayColumnMap($headers);

        if ($studentNumberColumn === null || $fullNameColumn === null || empty($dayColumnMap)) {
            return [
                'rows_read' => 0,
                'students_matched' => 0,
                'records_written' => 0,
                'errors' => [
                    $this->error($headerRowIndex + 1, 'file', 'Attendance import is not valid. Required columns: Student Number, Full Name, day columns 1-31.'),
                ],
            ];
        }

        $schoolId = (int) $context['school_id'];
        $classId = (int) $context['class_id'];
        $armId = !empty($context['arm_id']) ? (int) $context['arm_id'] : null;
        $sessionId = (int) $context['session_id'];
        $termId = (int) $context['term_id'];
        $year = (int) $context['year'];
        $month = (int) $context['month'];
        $daysInMonth = Carbon::create($year, $month, 1)->daysInMonth;
        $monthStart = Carbon::create($year, $month, 1)->toDateString();
        $monthEnd = Carbon::create($year, $month, $daysInMonth)->toDateString();
        $holidayDateSet = $this->loadHolidayDateSet($schoolId, $sessionId, $termId, $monthStart, $monthEnd);

        $students = Student::query()
            ->where('school_id', $schoolId)
            ->where('class_id', $classId)
            ->when($armId, fn ($q) => $q->where('arm_id', $armId))
            ->where('status', 'active')
            ->get();

        if ($students->isEmpty()) {
            return [
                'rows_read' => count(array_slice($rows, $headerRowIndex + 1)),
                'students_matched' => 0,
                'records_written' => 0,
                'errors' => [
                    $this->error(null, null, 'No active students found in the selected class/arm scope.'),
                ],
            ];
        }

        $studentMap = $this->buildStudentNumberMap($students);
        $errors = [];
        $rowsRead = 0;
        $recordsWritten = 0;
        $excludedMarks = 0;
        $matchedStudentIds = [];

        foreach (array_slice($rows, $headerRowIndex + 1) as $offset => $row) {
            $sheetRow = $headerRowIndex + 2 + $offset;
            $studentNumber = trim((string) ($row[$studentNumberColumn] ?? ''));
            $fullName = trim((string) ($row[$fullNameColumn] ?? ''));

            if ($studentNumber === '' && $fullName === '') {
                continue;
            }

            if ($this->isSummaryOrFooterRow($studentNumber, $fullName)) {
                continue;
            }

            $rowsRead++;

            if ($studentNumber === '') {
                $errors[] = $this->error($sheetRow, 'student_number', 'Student number is required for attendance import rows.');
                continue;
            }

            $student = $studentMap[strtolower($studentNumber)] ?? null;
            if (!$student) {
                $errors[] = $this->error($sheetRow, 'student', "Student {$studentNumber} does not belong to the selected class/arm.");
                continue;
            }

            $matchedStudentIds[$student->id] = $student->id;

            foreach ($dayColumnMap as $day => $columnIndex) {
                if ($day < 1 || $day > $daysInMonth) {
                    continue;
                }

                $mark = strtoupper(trim((string) ($row[$columnIndex] ?? '')));
                if ($mark === '') {
                    continue;
                }

                $dateObject = Carbon::create($year, $month, $day);
                if ($this->isExcludedAttendanceDate($dateObject, $holidayDateSet)) {
                    $excludedMarks++;
                    continue;
                }

                $status = match ($mark) {
                    'P', 'PRESENT' => 'present',
                    'A', 'ABSENT' => 'absent',
                    default => null,
                };

                if ($status === null) {
                    $errors[] = $this->error($sheetRow, (string) $day, "Invalid attendance mark '{$mark}' on day {$day}. Use only P or A.");
                    continue;
                }

                $date = $dateObject->toDateString();

                StudentAttendance::query()->updateOrCreate(
                    [
                        'student_id' => (int) $student->id,
                        'date' => $date,
                    ],
                    [
                        'school_id' => $schoolId,
                        'class_id' => $classId,
                        'arm_id' => $armId,
                        'session_id' => $sessionId,
                        'term_id' => $termId,
                        'status' => $status,
                        'recorded_by' => $userId,
                    ]
                );

                $recordsWritten++;
            }
        }

        $this->syncAttendanceToResultSummaries(
            [
                'school_id' => $schoolId,
                'class_id' => $classId,
                'arm_id' => $armId,
                'session_id' => $sessionId,
                'term_id' => $termId,
            ],
            array_values($matchedStudentIds),
            $userId
        );

        return [
            'rows_read' => $rowsRead,
            'students_matched' => count($matchedStudentIds),
            'records_written' => $recordsWritten,
            'excluded_marks' => $excludedMarks,
            'errors' => $errors,
        ];
    }

    public function syncAttendanceToResultSummaries(array $context, array $studentIds = [], ?int $updatedBy = null): void
    {
        $schoolId = (int) $context['school_id'];
        $classId = (int) $context['class_id'];
        $armId = !empty($context['arm_id']) ? (int) $context['arm_id'] : null;
        $sessionId = (int) $context['session_id'];
        $termId = (int) $context['term_id'];

        $attendanceQuery = StudentAttendance::query()
            ->where('school_id', $schoolId)
            ->where('class_id', $classId)
            ->when($armId, fn ($q) => $q->where('arm_id', $armId))
            ->where('session_id', $sessionId)
            ->where('term_id', $termId)
            ->when(!empty($studentIds), fn ($q) => $q->whereIn('student_id', $studentIds));

        $grouped = $attendanceQuery
            ->select('student_id', 'status', DB::raw('count(*) as total'))
            ->groupBy('student_id', 'status')
            ->get()
            ->groupBy('student_id');

        $targetStudentIds = !empty($studentIds)
            ? collect($studentIds)->map(fn ($id) => (int) $id)->unique()->values()
            : $grouped->keys()->map(fn ($id) => (int) $id)->values();

        foreach ($targetStudentIds as $studentId) {
            $statusRows = $grouped->get((string) $studentId, collect());
            $presentCount = (int) $statusRows->where('status', 'present')->sum('total')
                + (int) $statusRows->where('status', 'late')->sum('total');
            $absentCount = (int) $statusRows->where('status', 'absent')->sum('total');
            $totalCount = (int) $statusRows->sum('total');

            $resultUpdate = [
                'attendance_present' => $presentCount,
                'attendance_total' => $totalCount,
            ];

            if ($updatedBy) {
                $resultUpdate['updated_by'] = $updatedBy;
            }

            StudentResult::query()
                ->where('school_id', $schoolId)
                ->where('class_id', $classId)
                ->when($armId, fn ($q) => $q->where('arm_id', $armId))
                ->where('session_id', $sessionId)
                ->where('term_id', $termId)
                ->where('student_id', $studentId)
                ->update($resultUpdate);

            ReportCard::query()
                ->where('school_id', $schoolId)
                ->where('class_id', $classId)
                ->where('session_id', $sessionId)
                ->where('term_id', $termId)
                ->where('student_id', $studentId)
                ->update([
                    'attendance_present' => $presentCount,
                    'attendance_absent' => $absentCount,
                    'attendance_total' => $totalCount,
                ]);
        }
    }

    protected function detectHeaderRowIndex(array $rows): ?int
    {
        foreach ($rows as $index => $row) {
            $headers = $this->normalizeHeaders((array) $row);
            $hasStudentNumber = $this->hasAnyHeader($headers, ['student_number', 'admission_number', 'registration_number', 'reg_no']);
            $hasFullName = $this->hasAnyHeader($headers, ['full_name', 'student_name', 'name']);
            $hasDayColumns = count($this->extractDayColumnMap($headers)) >= 3;

            if ($hasStudentNumber && $hasFullName && $hasDayColumns) {
                return $index;
            }
        }

        return null;
    }

    protected function normalizeHeaders(array $headers): array
    {
        return array_map(function ($header) {
            $value = strtolower(trim((string) $header));
            $value = preg_replace('/[\x{FEFF}\x{200B}]/u', '', $value);
            $value = preg_replace('/[^a-z0-9]+/i', '_', (string) $value);
            return trim((string) $value, '_');
        }, $headers);
    }

    protected function extractDayColumnMap(array $headers): array
    {
        $dayColumns = [];

        foreach ($headers as $columnIndex => $header) {
            if ($header === '' || !ctype_digit((string) $header)) {
                continue;
            }

            $day = (int) $header;
            if ($day >= 1 && $day <= 31) {
                $dayColumns[$day] = $columnIndex;
            }
        }

        ksort($dayColumns);

        return $dayColumns;
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

    protected function pickFirstHeader(array $headers, array $candidates): ?int
    {
        foreach ($headers as $index => $header) {
            if (in_array($header, $candidates, true)) {
                return $index;
            }
        }

        return null;
    }

    protected function buildStudentNumberMap(Collection $students): array
    {
        $map = [];

        foreach ($students as $student) {
            if ($student->admission_number) {
                $map[strtolower(trim((string) $student->admission_number))] = $student;
            }

            if ($student->registration_number) {
                $map[strtolower(trim((string) $student->registration_number))] = $student;
            }
        }

        return $map;
    }

    protected function isSummaryOrFooterRow(string $studentNumber, string $fullName): bool
    {
        $candidate = strtolower(trim($studentNumber !== '' ? $studentNumber : $fullName));
        if ($candidate === '') {
            return false;
        }

        $candidate = preg_replace('/\s+/', ' ', $candidate);
        $summaryPrefixes = [
            'student class summary',
            'class summary',
            'attendance summary',
            'summary',
            'grand total',
            'class total',
            'totals',
            'total',
        ];

        foreach ($summaryPrefixes as $prefix) {
            if (str_starts_with((string) $candidate, $prefix)) {
                return true;
            }
        }

        return false;
    }

    protected function error(?int $rowNumber, ?string $column, string $message): array
    {
        return [
            'row' => $rowNumber,
            'column' => $column,
            'message' => $message,
        ];
    }

    protected function loadHolidayDateSet(int $schoolId, int $sessionId, int $termId, string $monthStart, string $monthEnd): array
    {
        $dates = SchoolHoliday::query()
            ->withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->whereBetween('holiday_date', [$monthStart, $monthEnd])
            ->where(function ($query) use ($sessionId) {
                $query->whereNull('session_id')
                    ->orWhere('session_id', $sessionId);
            })
            ->where(function ($query) use ($termId) {
                $query->whereNull('term_id')
                    ->orWhere('term_id', $termId);
            })
            ->pluck('holiday_date')
            ->map(fn ($date) => Carbon::parse($date)->toDateString())
            ->all();

        return array_fill_keys($dates, true);
    }

    protected function isExcludedAttendanceDate(Carbon $date, array $holidayDateSet): bool
    {
        if ($date->isWeekend()) {
            return true;
        }

        return isset($holidayDateSet[$date->toDateString()]);
    }
}
