<?php

namespace Database\Seeders;

use App\Models\School;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SchoolSeeder extends Seeder
{
    public function run(): void
    {
        // Create demo school
        $school = School::create([
            'name' => 'Greenfield International Academy',
            'slug' => 'greenfield-international-academy',
            'code' => 'GIA001',
            'email' => 'info@greenfieldacademy.ng',
            'phone' => '+2348012345678',
            'address' => '25 Education Lane, Victoria Island',
            'city' => 'Lagos',
            'state' => 'Lagos',
            'country' => 'Nigeria',
            'motto' => 'Excellence in Education',
            'school_type' => 'combined',
            'ownership' => 'private',
            'established_year' => 2010,
            'subscription_plan' => 'premium',
            'subscription_expires_at' => now()->addYear(),
            'settings' => [
                'grading_system' => 'waec',
                'currency_symbol' => '₦',
                'result_approval_required' => true,
                'online_admission_enabled' => true,
                'sms_notifications_enabled' => true,
            ],
        ]);

        // Super Admin
        User::create([
            'school_id' => null,
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'email' => 'superadmin@chrizfasa.ng',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
            'email_verified_at' => now(),
        ]);

        // School Admin
        User::create([
            'school_id' => $school->id,
            'first_name' => 'Adeyemi',
            'last_name' => 'Johnson',
            'email' => 'admin@greenfieldacademy.ng',
            'password' => Hash::make('password'),
            'role' => 'school_admin',
            'email_verified_at' => now(),
        ]);

        // Principal
        $principalUser = User::create([
            'school_id' => $school->id,
            'first_name' => 'Mrs. Folake',
            'last_name' => 'Adeyemo',
            'email' => 'principal@greenfieldacademy.ng',
            'password' => Hash::make('password'),
            'role' => 'principal',
            'email_verified_at' => now(),
        ]);

        \App\Models\Staff::create([
            'school_id' => $school->id,
            'user_id' => $principalUser->id,
            'staff_id_number' => 'GIA/STF/001',
            'designation' => 'Principal',
            'department' => 'Administration',
            'gender' => 'female',
            'basic_salary' => 350000,
            'date_of_employment' => '2015-01-15',
        ]);
    }
}
