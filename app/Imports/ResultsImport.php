<?php

namespace App\Imports;

use App\Models\Result;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class ResultsImport implements ToCollection, SkipsEmptyRows
{
    public array $errors  = [];
    public int   $saved   = 0;
    public int   $skipped = 0;

    private int $classId;
    private int $sessionId;
    private int $termId;
    private int $schoolId;

    public function __construct(int $classId, int $sessionId, int $termId)
    {
        $this->classId   = $classId;
        $this->sessionId = $sessionId;
        $this->termId    = $termId;
        $this->schoolId  = Auth::user()->school_id;
    }

    public function collection(Collection $rows)
    {
        $headers = null;
        $dataStartRow = 1; // row index where data begins

        foreach ($rows as $index => $row) {
            $values = $row->toArray();

            // ── Detect header row ─────────────────────────────────────────
            // First non-empty row is the header. Normalize keys: strip BOM,
            // trim whitespace, lowercase, replace spaces/hyphens with underscore.
            if ($headers === null) {
                $headers = array_map(function ($h) {
                    $h = preg_replace('/[\x{FEFF}\x{200B}]/u', '', (string) $h); // strip BOM
                    $h = strtolower(trim($h));
                    $h = preg_replace('/[\s\-]+/', '_', $h);
                    return $h;
                }, $values);
                $dataStartRow = $index + 1;
                continue;
            }

            // ── Map row values to normalized headers ──────────────────────
            $row = array_combine($headers, $values);

            $rowNum   = $index + 1; // human-readable row number (header = row 1)

            $regNo    = trim((string) ($row['registration_number'] ?? $row['reg_no'] ?? $row['admission_number'] ?? ''));
            $subjName = trim((string) ($row['subject'] ?? $row['subject_name'] ?? ''));
            $exam     = (float) ($row['exam'] ?? $row['exam_score'] ?? 0);
            $test1    = (float) ($row['first_test'] ?? $row['ca1'] ?? $row['test1'] ?? 0);
            $test2    = (float) ($row['second_test'] ?? $row['ca2'] ?? $row['test2'] ?? 0);

            if (empty($regNo) || empty($subjName)) {
                $this->errors[] = "Row {$rowNum}: registration_number and subject are required.";
                $this->skipped++;
                continue;
            }

            // ── Find student ──────────────────────────────────────────────
            $student = Student::where('school_id', $this->schoolId)
                ->where(function ($q) use ($regNo) {
                    $q->where('registration_number', $regNo)
                      ->orWhere('admission_number', $regNo);
                })->first();

            if (!$student) {
                $this->errors[] = "Row {$rowNum}: Student '{$regNo}' not found.";
                $this->skipped++;
                continue;
            }

            // ── Find subject ──────────────────────────────────────────────
            $subject = Subject::where('school_id', $this->schoolId)
                ->whereRaw('LOWER(TRIM(name)) = ?', [strtolower($subjName)])
                ->first();

            if (!$subject) {
                $this->errors[] = "Row {$rowNum}: Subject '{$subjName}' not found in the system.";
                $this->skipped++;
                continue;
            }

            // ── Save result ───────────────────────────────────────────────
            $total     = $exam + $test1 + $test2;
            $gradeInfo = Result::calculateGrade($total);

            Result::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'subject_id' => $subject->id,
                    'session_id' => $this->sessionId,
                    'term_id'    => $this->termId,
                ],
                [
                    'school_id'      => $this->schoolId,
                    'class_id'       => $this->classId,
                    'arm_id'         => $student->arm_id,
                    'ca1_score'      => $test1,
                    'ca2_score'      => $test2,
                    'ca3_score'      => 0,
                    'exam_score'     => $exam,
                    'total_score'    => $total,
                    'grade'          => $gradeInfo['grade'],
                    'grade_point'    => $gradeInfo['point'],
                    'teacher_remark' => $gradeInfo['remark'],
                ]
            );

            $this->saved++;
        }
    }
}
