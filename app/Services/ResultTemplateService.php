<?php

namespace App\Services;

use App\Models\SchoolClass;
use App\Models\Student;

class ResultTemplateService
{
    public function buildWideTemplateRows(int $schoolId, int $classId, ?int $armId = null, string $assessmentType = 'full_result'): array
    {
        $class = SchoolClass::query()
            ->with(['subjects' => fn ($q) => $q->where('is_active', true)->orderBy('name')])
            ->findOrFail($classId);

        $subjects = $class->subjects->values();
        $rows = [];
        $assessmentType = strtolower(trim($assessmentType));

        $headings = ['admission_number', 'student_name'];
        foreach ($subjects as $subject) {
            $token = $this->columnToken($subject->name);
            if ($assessmentType === 'full_result') {
                $headings[] = "{$token}_exam";
                $headings[] = "{$token}_first_test";
                $headings[] = "{$token}_second_test";
            } else {
                $headings[] = $token;
            }
        }

        if ($assessmentType === 'full_result') {
            $headings[] = 'attendance';
            $headings[] = 'class_teacher_remark';
            $headings[] = 'principal_remark';
            $headings[] = 'promoted_to';
            $headings[] = 'date';
        }

        $rows[] = $headings;

        $students = Student::query()
            ->where('school_id', $schoolId)
            ->where('class_id', $classId)
            ->when($armId, fn ($q) => $q->where('arm_id', $armId))
            ->where('status', 'active')
            ->orderBy('first_name')
            ->take(3)
            ->get();

        foreach ($students as $student) {
            $row = [
                $student->admission_number ?: $student->registration_number,
                $student->full_name,
            ];

            foreach ($subjects as $subject) {
                if ($assessmentType === 'full_result') {
                    $row[] = '';
                    $row[] = '';
                    $row[] = '';
                } else {
                    $row[] = '';
                }
            }

            if ($assessmentType === 'full_result') {
                $row[] = '';
                $row[] = '';
                $row[] = '';
                $row[] = '';
                $row[] = '';
            }

            $rows[] = $row;
        }

        if ($students->isEmpty()) {
            $exampleAdmission = Student::generateAdmissionNumber($schoolId);
            $example = [$exampleAdmission, 'Sample Student'];
            foreach ($subjects as $subject) {
                if ($assessmentType === 'full_result') {
                    $example[] = '';
                    $example[] = '';
                    $example[] = '';
                } else {
                    $example[] = '';
                }
            }

            if ($assessmentType === 'full_result') {
                $example[] = '';
                $example[] = '';
                $example[] = '';
                $example[] = '';
                $example[] = '';
            }
            $rows[] = $example;
        }

        return $rows;
    }

    protected function columnToken(string $value): string
    {
        $token = strtolower(trim($value));
        $token = str_replace('&', ' and ', $token);
        $token = preg_replace('/[^a-z0-9]+/i', '_', $token);
        return trim((string) $token, '_');
    }
}
