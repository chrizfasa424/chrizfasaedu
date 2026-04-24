<?php

namespace Database\Seeders;

use App\Models\ExamType;
use App\Models\School;
use Illuminate\Database\Seeder;

class ExamTypeSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            ['name' => 'First Terminal Examination', 'slug' => 'first-terminal'],
            ['name' => 'Second Terminal Examination', 'slug' => 'second-terminal'],
            ['name' => 'Third Terminal Examination', 'slug' => 'third-terminal'],
        ];

        foreach (School::query()->get() as $school) {
            foreach ($defaults as $entry) {
                ExamType::query()->firstOrCreate(
                    ['school_id' => $school->id, 'slug' => $entry['slug']],
                    [
                        'name' => $entry['name'],
                        'description' => null,
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}

