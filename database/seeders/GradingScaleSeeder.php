<?php

namespace Database\Seeders;

use App\Models\GradingScale;
use App\Models\School;
use Illuminate\Database\Seeder;

class GradingScaleSeeder extends Seeder
{
    public function run(): void
    {
        $bands = (array) config('ems.grading.waec', []);

        foreach (School::query()->get() as $school) {
            foreach ($bands as $index => $band) {
                GradingScale::query()->firstOrCreate(
                    [
                        'school_id' => $school->id,
                        'class_id' => null,
                        'section' => null,
                        'min_score' => (float) $band['min'],
                        'max_score' => (float) $band['max'],
                    ],
                    [
                        'grade' => (string) $band['grade'],
                        'point' => (float) ($band['point'] ?? 0),
                        'remark' => (string) ($band['remark'] ?? ''),
                        'sort_order' => $index + 1,
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}

