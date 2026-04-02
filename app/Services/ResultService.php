<?php

namespace App\Services;

use App\Models\Result;
use App\Models\Student;
use App\Models\ReportCard;

class ResultService
{
    public function computeTermResults(int $classId, int $sessionId, int $termId): int
    {
        $students = Student::active()->inClass($classId)->get();
        $processed = 0;

        foreach ($students as $student) {
            $results = Result::where('student_id', $student->id)
                ->where('session_id', $sessionId)
                ->where('term_id', $termId)
                ->get();

            if ($results->isEmpty()) continue;

            foreach ($results as $result) {
                $total = $result->ca1_score + $result->ca2_score + $result->ca3_score + $result->exam_score;
                $grade = Result::calculateGrade($total);
                $result->update([
                    'total_score' => $total,
                    'grade' => $grade['grade'],
                    'grade_point' => $grade['point'],
                ]);
            }

            $totalScore = $results->sum('total_score');
            $avgScore = $results->count() > 0 ? $totalScore / $results->count() : 0;

            ReportCard::updateOrCreate(
                ['student_id' => $student->id, 'session_id' => $sessionId, 'term_id' => $termId],
                [
                    'school_id' => $student->school_id,
                    'class_id' => $classId,
                    'total_score' => $totalScore,
                    'average_score' => round($avgScore, 2),
                    'total_subjects' => $results->count(),
                    'subjects_passed' => $results->where('total_score', '>=', 40)->count(),
                    'subjects_failed' => $results->where('total_score', '<', 40)->count(),
                    'class_size' => $students->count(),
                ]
            );
            $processed++;
        }

        $this->computePositions($classId, $sessionId, $termId);
        return $processed;
    }

    protected function computePositions(int $classId, int $sessionId, int $termId): void
    {
        $reportCards = ReportCard::where('class_id', $classId)
            ->where('session_id', $sessionId)
            ->where('term_id', $termId)
            ->orderByDesc('average_score')
            ->get();

        $position = 1;
        foreach ($reportCards as $card) {
            $card->update(['position_in_class' => $position++]);
        }
    }
}
