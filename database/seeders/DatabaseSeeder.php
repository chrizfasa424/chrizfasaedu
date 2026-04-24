<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            SchoolSeeder::class,
            AcademicStructureSeeder::class,
            SubjectSeeder::class,
            ExamTypeSeeder::class,
            GradingScaleSeeder::class,
        ]);
    }
}
