<?php

namespace Database\Seeders;

use App\Models\School;
use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::first();
        if (!$school) return;

        $subjects = [
            ['English Language', 'ENG', 'Languages', true],
            ['Mathematics', 'MTH', 'Sciences', true],
            ['Basic Science', 'BSC', 'Sciences', true],
            ['Basic Technology', 'BTE', 'Sciences', true],
            ['Social Studies', 'SST', 'Humanities', true],
            ['Civic Education', 'CVE', 'Humanities', true],
            ['Christian Religious Studies', 'CRS', 'Humanities', false],
            ['Islamic Religious Studies', 'IRS', 'Humanities', false],
            ['Agricultural Science', 'AGR', 'Sciences', false],
            ['Home Economics', 'HEC', 'Vocational', false],
            ['Physical & Health Education', 'PHE', 'Sciences', false],
            ['Computer Studies / ICT', 'ICT', 'Sciences', true],
            ['French', 'FRN', 'Languages', false],
            ['Yoruba', 'YOR', 'Languages', false],
            ['Igbo', 'IGB', 'Languages', false],
            ['Hausa', 'HAU', 'Languages', false],
            ['Creative Arts', 'CAR', 'Arts', false],
            ['Music', 'MUS', 'Arts', false],
            ['Physics', 'PHY', 'Sciences', false],
            ['Chemistry', 'CHM', 'Sciences', false],
            ['Biology', 'BIO', 'Sciences', false],
            ['Further Mathematics', 'FMT', 'Sciences', false],
            ['Geography', 'GEO', 'Humanities', false],
            ['Economics', 'ECO', 'Commercial', false],
            ['Government', 'GOV', 'Humanities', false],
            ['Literature in English', 'LIT', 'Languages', false],
            ['Commerce', 'COM', 'Commercial', false],
            ['Accounting', 'ACC', 'Commercial', false],
            ['History', 'HIS', 'Humanities', false],
            ['Technical Drawing', 'TDR', 'Sciences', false],
        ];

        foreach ($subjects as [$name, $code, $dept, $compulsory]) {
            Subject::create([
                'school_id' => $school->id,
                'name' => $name,
                'code' => $code,
                'department' => $dept,
                'is_compulsory' => $compulsory,
            ]);
        }
    }
}
