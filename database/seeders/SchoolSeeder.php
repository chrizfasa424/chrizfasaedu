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
        // Create the main school brand
        $school = School::create([
            'name' => 'ChrizFasa Academy',
            'slug' => 'chrizfasa-academy',
            'code' => 'CFA001',
            'email' => 'info@chrizfasa.ng',
            'phone' => '+2348012345678',
            'address' => '25 Education Lane, Victoria Island',
            'city' => 'Lagos',
            'state' => 'Lagos',
            'country' => 'Nigeria',
            'motto' => 'A modern learning environment for KG, Primary, and Secondary students',
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

        // Primary Admin Account
        User::create([
            'school_id' => $school->id,
            'first_name' => 'Adeyemi',
            'last_name' => 'Johnson',
            'email' => 'admin@chrizfasa.ng',
            'password' => Hash::make('password'),
            'role' => 'school_admin',
            'email_verified_at' => now(),
        ]);

        // Principal
        $principalUser = User::create([
            'school_id' => $school->id,
            'first_name' => 'Mrs. Folake',
            'last_name' => 'Adeyemo',
            'email' => 'principal@chrizfasa.ng',
            'password' => Hash::make('password'),
            'role' => 'principal',
            'email_verified_at' => now(),
        ]);

        \App\Models\Staff::create([
            'school_id' => $school->id,
            'user_id' => $principalUser->id,
            'staff_id_number' => 'CFA/STF/001',
            'designation' => 'Principal',
            'department' => 'Administration',
            'gender' => 'female',
            'basic_salary' => 350000,
            'date_of_employment' => '2015-01-15',
        ]);
    }
}
