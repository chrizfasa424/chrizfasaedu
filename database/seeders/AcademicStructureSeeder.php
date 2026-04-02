<?php

namespace Database\Seeders;

use App\Models\School;
use App\Models\AcademicSession;
use App\Models\AcademicTerm;
use App\Models\SchoolClass;
use App\Models\ClassArm;
use Illuminate\Database\Seeder;

class AcademicStructureSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::first();
        if (!$school) return;

        // Create session
        $session = AcademicSession::create([
            'school_id' => $school->id,
            'name' => '2025/2026',
            'slug' => '2025-2026',
            'start_date' => '2025-09-15',
            'end_date' => '2026-07-15',
            'is_current' => true,
        ]);

        // Create terms
        foreach ([
            ['First Term', 'first', '2025-09-15', '2025-12-15'],
            ['Second Term', 'second', '2026-01-10', '2026-04-10'],
            ['Third Term', 'third', '2026-04-25', '2026-07-15'],
        ] as [$name, $term, $start, $end]) {
            AcademicTerm::create([
                'school_id' => $school->id,
                'session_id' => $session->id,
                'name' => $name,
                'term' => $term,
                'start_date' => $start,
                'end_date' => $end,
                'is_current' => $term === 'first',
            ]);
        }

        // Create classes
        $classConfig = [
            ['KG 1', 'kg1', 'Kindergarten', 1],
            ['KG 2', 'kg2', 'Kindergarten', 2],
            ['Primary 1', 'primary_1', 'Primary', 3],
            ['Primary 2', 'primary_2', 'Primary', 4],
            ['Primary 3', 'primary_3', 'Primary', 5],
            ['Primary 4', 'primary_4', 'Primary', 6],
            ['Primary 5', 'primary_5', 'Primary', 7],
            ['Primary 6', 'primary_6', 'Primary', 8],
            ['JSS 1', 'jss_1', 'Junior Secondary', 9],
            ['JSS 2', 'jss_2', 'Junior Secondary', 10],
            ['JSS 3', 'jss_3', 'Junior Secondary', 11],
            ['SSS 1', 'sss_1', 'Senior Secondary', 12],
            ['SSS 2', 'sss_2', 'Senior Secondary', 13],
            ['SSS 3', 'sss_3', 'Senior Secondary', 14],
        ];

        foreach ($classConfig as [$name, $grade, $section, $order]) {
            $class = SchoolClass::create([
                'school_id' => $school->id,
                'name' => $name,
                'grade_level' => $grade,
                'section' => $section,
                'capacity' => 40,
                'order' => $order,
            ]);

            // Add arms A, B, C for JSS and SSS
            if (in_array($section, ['Junior Secondary', 'Senior Secondary'])) {
                foreach (['A', 'B', 'C'] as $arm) {
                    ClassArm::create([
                        'school_id' => $school->id,
                        'class_id' => $class->id,
                        'name' => $arm,
                        'capacity' => 40,
                    ]);
                }
            } else {
                foreach (['A', 'B'] as $arm) {
                    ClassArm::create([
                        'school_id' => $school->id,
                        'class_id' => $class->id,
                        'name' => $arm,
                        'capacity' => 40,
                    ]);
                }
            }
        }
    }
}
