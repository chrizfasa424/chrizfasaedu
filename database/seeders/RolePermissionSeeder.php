<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Academic
            'view-students', 'create-students', 'edit-students', 'delete-students',
            'view-classes', 'manage-classes',
            'view-subjects', 'manage-subjects',
            'view-attendance', 'record-attendance',
            'view-timetable', 'manage-timetable',

            // Results
            'enter-scores', 'view-results', 'approve-results', 'publish-results',
            'generate-report-cards',

            // Admission
            'view-admissions', 'process-admissions', 'approve-admissions',

            // Financial
            'view-fees', 'manage-fees',
            'view-invoices', 'generate-invoices',
            'view-payments', 'record-payments', 'verify-payments',
            'view-financial-reports',

            // Staff
            'view-staff', 'manage-staff', 'manage-salaries',

            // Communication
            'send-announcements', 'send-sms', 'send-email',

            // Library
            'manage-library',

            // System
            'manage-settings', 'view-audit-logs', 'manage-schools',
            'manage-subscriptions', 'view-reports',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdmin->givePermissionTo(Permission::all());

        $schoolAdmin = Role::firstOrCreate(['name' => 'school_admin']);
        $schoolAdmin->givePermissionTo(array_filter($permissions, fn($p) => $p !== 'manage-schools' && $p !== 'manage-subscriptions'));

        $principal = Role::firstOrCreate(['name' => 'principal']);
        $principal->givePermissionTo([
            'view-students', 'view-classes', 'view-subjects', 'view-attendance',
            'view-timetable', 'view-results', 'approve-results', 'publish-results',
            'view-admissions', 'approve-admissions', 'view-fees', 'view-invoices',
            'view-payments', 'view-financial-reports', 'view-staff', 'send-announcements',
            'view-reports', 'generate-report-cards',
        ]);

        $teacher = Role::firstOrCreate(['name' => 'teacher']);
        $teacher->givePermissionTo([
            'view-students', 'view-classes', 'view-subjects',
            'view-attendance', 'record-attendance', 'view-timetable',
            'enter-scores', 'view-results',
        ]);

        $accountant = Role::firstOrCreate(['name' => 'accountant']);
        $accountant->givePermissionTo([
            'view-fees', 'manage-fees', 'view-invoices', 'generate-invoices',
            'view-payments', 'record-payments', 'verify-payments', 'view-financial-reports',
        ]);

        Role::firstOrCreate(['name' => 'parent']);
        Role::firstOrCreate(['name' => 'student']);
        Role::firstOrCreate(['name' => 'librarian'])->givePermissionTo(['manage-library']);
    }
}
