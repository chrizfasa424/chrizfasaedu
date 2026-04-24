<?php

namespace App\Services;

use App\Models\GradingScale;

class GradingService
{
    public function gradeForScore(float $score, int $schoolId, ?int $classId = null, ?string $section = null): array
    {
        $score = round($score, 2);

        $scales = GradingScale::query()
            ->where('school_id', $schoolId)
            ->where('is_active', true)
            ->when($classId, fn ($q) => $q->where(function ($inner) use ($classId) {
                $inner->where('class_id', $classId)->orWhereNull('class_id');
            }))
            ->when($section, fn ($q) => $q->where(function ($inner) use ($section) {
                $inner->where('section', $section)->orWhereNull('section');
            }))
            ->orderByDesc('class_id')
            ->orderByDesc('section')
            ->orderBy('sort_order')
            ->get();

        foreach ($scales as $scale) {
            if ($score >= (float) $scale->min_score && $score <= (float) $scale->max_score) {
                return [
                    'grade' => $scale->grade,
                    'point' => (float) ($scale->point ?? 0),
                    'remark' => $scale->remark ?: '',
                ];
            }
        }

        $system = (string) config('ems.grading_system', 'waec');
        $config = (array) config("ems.grading.$system", []);
        foreach ($config as $band) {
            if ($score >= (float) $band['min'] && $score <= (float) $band['max']) {
                return [
                    'grade' => (string) $band['grade'],
                    'point' => (float) ($band['point'] ?? 0),
                    'remark' => (string) ($band['remark'] ?? ''),
                ];
            }
        }

        return [
            'grade' => 'F',
            'point' => 0.0,
            'remark' => 'Fail',
        ];
    }

    public function interpretationForSchool(int $schoolId, ?int $classId = null, ?string $section = null): array
    {
        $scales = GradingScale::query()
            ->where('school_id', $schoolId)
            ->where('is_active', true)
            ->when($classId, fn ($q) => $q->where(function ($inner) use ($classId) {
                $inner->where('class_id', $classId)->orWhereNull('class_id');
            }))
            ->when($section, fn ($q) => $q->where(function ($inner) use ($section) {
                $inner->where('section', $section)->orWhereNull('section');
            }))
            ->orderByDesc('class_id')
            ->orderByDesc('section')
            ->orderBy('sort_order')
            ->get();

        if ($scales->isNotEmpty()) {
            return $scales->map(fn ($scale) => [
                'min' => (float) $scale->min_score,
                'max' => (float) $scale->max_score,
                'grade' => $scale->grade,
                'remark' => $scale->remark,
                'point' => (float) ($scale->point ?? 0),
            ])->all();
        }

        $system = (string) config('ems.grading_system', 'waec');

        return array_map(function (array $band) {
            return [
                'min' => (float) $band['min'],
                'max' => (float) $band['max'],
                'grade' => (string) $band['grade'],
                'remark' => (string) ($band['remark'] ?? ''),
                'point' => (float) ($band['point'] ?? 0),
            ];
        }, (array) config("ems.grading.$system", []));
    }
}

