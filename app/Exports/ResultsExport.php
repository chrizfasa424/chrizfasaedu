<?php

namespace App\Exports;

use App\Models\AcademicTerm;
use App\Models\ReportCard;
use App\Models\Result;
use App\Models\SchoolClass;
use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ResultsExport implements FromArray, WithStyles, WithColumnWidths, WithTitle
{
    private int $classId;
    private int $termId;
    private ?int $studentId; // null = all students (admin), set = single student
    private int $schoolId;

    public array $rows = [];

    public function __construct(int $classId, int $termId, int $schoolId, ?int $studentId = null)
    {
        $this->classId   = $classId;
        $this->termId    = $termId;
        $this->schoolId  = $schoolId;
        $this->studentId = $studentId;
    }

    public function title(): string
    {
        $term  = AcademicTerm::with('session')->find($this->termId);
        $class = SchoolClass::find($this->classId);
        $label = $class?->grade_level?->label() ?? $class?->name ?? 'Class';
        return substr("{$label} - {$term?->name}", 0, 31); // Excel sheet name max 31 chars
    }

    public function array(): array
    {
        $term     = AcademicTerm::with('session')->find($this->termId);
        $class    = SchoolClass::find($this->classId);
        $label    = $class?->grade_level?->label() ?? $class?->name ?? '';
        $termName = $term?->name . ' — ' . ($term?->session?->name ?? '');

        // ── School info rows ────────────────────────────────────────────────────
        $rows = [];
        $rows[] = ["RESULT SHEET — {$label} — {$termName}", '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''];
        $rows[] = ['', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''];

        // ── Header row ──────────────────────────────────────────────────────────
        $rows[] = [
            'Student No.',
            'Full Name',
            'Subject',
            'First Test',
            'Second Test',
            'Examination',
            'Total Grades',
            'Position',
            'Grade',
            'Remarks',
            'Total First Test',
            'Total Second Test',
            'Total Examination',
            'Overall Total',
            'Mark Average',
            'Class Average',
        ];

        // ── Load students ───────────────────────────────────────────────────────
        $studentsQuery = Student::where('class_id', $this->classId)
            ->where('status', 'active')
            ->orderBy('first_name');

        if ($this->studentId) {
            $studentsQuery->where('id', $this->studentId);
        }

        $students = $studentsQuery->get();

        $studentNumber = 1;
        foreach ($students as $student) {
            $results = Result::with('subject')
                ->where('student_id', $student->id)
                ->where('term_id', $this->termId)
                ->get()
                ->sortBy('subject.name');

            if ($results->isEmpty()) {
                $studentNumber++;
                continue;
            }

            $reportCard = ReportCard::where('student_id', $student->id)
                ->where('term_id', $this->termId)
                ->first();

            $totalCa1     = $reportCard?->total_ca1     ?? $results->sum('ca1_score');
            $totalCa2     = $reportCard?->total_ca2     ?? $results->sum('ca2_score');
            $totalExam    = $reportCard?->total_exam    ?? $results->sum('exam_score');
            $overallTotal = $reportCard?->total_score   ?? $results->sum('total_score');
            $markAverage  = $reportCard?->average_score ?? ($results->count() ? round($overallTotal / $results->count(), 2) : 0);
            $classAverage = $reportCard?->class_average ?? 0;

            $isFirst = true;
            foreach ($results as $result) {
                if ($isFirst) {
                    $rows[] = [
                        $studentNumber,
                        $student->full_name,
                        $result->subject?->name ?? '',
                        number_format((float) $result->ca1_score, 0),
                        number_format((float) $result->ca2_score, 0),
                        number_format((float) $result->exam_score, 0),
                        number_format((float) $result->total_score, 0),
                        $result->position_in_subject ?? '',
                        $result->grade ?? '',
                        $result->teacher_remark ?? '',
                        number_format((float) $totalCa1, 0),
                        number_format((float) $totalCa2, 0),
                        number_format((float) $totalExam, 0),
                        number_format((float) $overallTotal, 0),
                        number_format((float) $markAverage, 2),
                        number_format((float) $classAverage, 2),
                    ];
                    $isFirst = false;
                } else {
                    $rows[] = [
                        '',
                        '',
                        $result->subject?->name ?? '',
                        number_format((float) $result->ca1_score, 0),
                        number_format((float) $result->ca2_score, 0),
                        number_format((float) $result->exam_score, 0),
                        number_format((float) $result->total_score, 0),
                        $result->position_in_subject ?? '',
                        $result->grade ?? '',
                        $result->teacher_remark ?? '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                    ];
                }
            }

            // Blank spacer row between students
            $rows[] = array_fill(0, 16, '');
            $studentNumber++;
        }

        $this->rows = $rows; // store for style indexing
        return $rows;
    }

    public function styles(Worksheet $sheet): array
    {
        // Title row
        $sheet->mergeCells('A1:P1');
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 13, 'color' => ['argb' => 'FF1E3A5F']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'FFE8EEF7']],
        ]);

        // Header row (row 3)
        $sheet->getStyle('A3:P3')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 10],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'FF2D4E8A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'wrapText' => true],
            'borders'   => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF6882B2']],
            ],
        ]);

        // Summary columns header highlight (K3:P3)
        $sheet->getStyle('K3:P3')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'FF1A3460']],
        ]);

        // Freeze header
        $sheet->freezePane('C4');

        // Auto-filter on header row
        $sheet->setAutoFilter('A3:P3');

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 12,  // Student No.
            'B' => 22,  // Full Name
            'C' => 28,  // Subject
            'D' => 11,  // First Test
            'E' => 12,  // Second Test
            'F' => 13,  // Examination
            'G' => 13,  // Total Grades
            'H' => 10,  // Position
            'I' => 8,   // Grade
            'J' => 14,  // Remarks
            'K' => 14,  // Total First Test
            'L' => 15,  // Total Second Test
            'M' => 16,  // Total Examination
            'N' => 13,  // Overall Total
            'O' => 13,  // Mark Average
            'P' => 13,  // Class Average
        ];
    }
}
