<?php

namespace App\Services;

use App\Models\StudentResult;
use App\Models\StudentResultItem;
use Illuminate\Support\Collection;

class ResultSheetRankingService
{
    public function recomputeScope(int $schoolId, int $classId, ?int $armId, int $sessionId, int $termId, int $examTypeId): void
    {
        $scope = StudentResult::query()
            ->where('school_id', $schoolId)
            ->where('class_id', $classId)
            ->where('session_id', $sessionId)
            ->where('term_id', $termId)
            ->where('exam_type_id', $examTypeId)
            ->when($armId, fn ($q) => $q->where('arm_id', $armId))
            ->with('items')
            ->get();

        if ($scope->isEmpty()) {
            return;
        }

        // Subject-level position is intentionally disabled.
        // Position is only computed at class level using overall total score.
        $this->clearSubjectPositions($scope);
        $this->recomputeClassPositions($scope);
    }

    protected function clearSubjectPositions(Collection $sheetResults): void
    {
        StudentResultItem::query()
            ->whereIn('student_result_id', $sheetResults->pluck('id'))
            ->update(['subject_position' => null]);
    }

    protected function recomputeClassPositions(Collection $sheetResults): void
    {
        $sorted = $sheetResults->sortBy([
            ['total_score', 'desc'],
            ['student_id', 'asc'],
        ])->values();

        $this->applyCompetitionRanks($sorted, 'class_position', fn ($result) => (float) $result->total_score);

        $classAverage = round((float) $sorted->avg('average_score'), 2);
        foreach ($sorted as $result) {
            $result->update(['class_average' => $classAverage]);
        }
    }

    protected function applyCompetitionRanks(Collection $items, string $column, callable $scoreResolver): void
    {
        $rank = 0;
        $position = 0;
        $lastScore = null;

        foreach ($items as $item) {
            $position++;
            $score = $scoreResolver($item);

            if ($lastScore === null || $score < $lastScore) {
                $rank = $position;
            }

            $item->update([$column => $rank]);
            $lastScore = $score;
        }
    }
}
