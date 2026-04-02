<?php

return [
    /*
    |--------------------------------------------------------------------------
    | ChrizFasa EMS Configuration
    |--------------------------------------------------------------------------
    */

    'name' => env('APP_NAME', 'ChrizFasa EMS'),
    'country' => env('SCHOOL_COUNTRY', 'Nigeria'),
    'currency' => env('SCHOOL_CURRENCY', 'NGN'),
    'currency_symbol' => env('SCHOOL_CURRENCY_SYMBOL', '₦'),

    // Grading System
    'grading_system' => env('SCHOOL_GRADING_SYSTEM', 'waec'),
    'grading' => [
        'waec' => [
            ['min' => 75, 'max' => 100, 'grade' => 'A1', 'point' => 1, 'remark' => 'Excellent'],
            ['min' => 70, 'max' => 74, 'grade' => 'B2', 'point' => 2, 'remark' => 'Very Good'],
            ['min' => 65, 'max' => 69, 'grade' => 'B3', 'point' => 3, 'remark' => 'Good'],
            ['min' => 60, 'max' => 64, 'grade' => 'C4', 'point' => 4, 'remark' => 'Credit'],
            ['min' => 55, 'max' => 59, 'grade' => 'C5', 'point' => 5, 'remark' => 'Credit'],
            ['min' => 50, 'max' => 54, 'grade' => 'C6', 'point' => 6, 'remark' => 'Credit'],
            ['min' => 45, 'max' => 49, 'grade' => 'D7', 'point' => 7, 'remark' => 'Pass'],
            ['min' => 40, 'max' => 44, 'grade' => 'E8', 'point' => 8, 'remark' => 'Pass'],
            ['min' => 0,  'max' => 39, 'grade' => 'F9', 'point' => 9, 'remark' => 'Fail'],
        ],
    ],

    // Assessment breakdown
    'assessment' => [
        'ca1_max' => 20,
        'ca2_max' => 20,
        'ca3_max' => 20,
        'exam_max' => 60,
        'total_max' => 100,
    ],

    // Nigerian states for dropdowns
    'states' => [
        'Abia', 'Adamawa', 'Akwa Ibom', 'Anambra', 'Bauchi', 'Bayelsa', 'Benue',
        'Borno', 'Cross River', 'Delta', 'Ebonyi', 'Edo', 'Ekiti', 'Enugu',
        'FCT', 'Gombe', 'Imo', 'Jigawa', 'Kaduna', 'Kano', 'Katsina', 'Kebbi',
        'Kogi', 'Kwara', 'Lagos', 'Nasarawa', 'Niger', 'Ogun', 'Ondo', 'Osun',
        'Oyo', 'Plateau', 'Rivers', 'Sokoto', 'Taraba', 'Yobe', 'Zamfara',
    ],

    // Fee categories
    'fee_categories' => [
        'tuition' => 'Tuition Fee',
        'development_levy' => 'Development Levy',
        'ict' => 'ICT Fee',
        'uniform' => 'Uniform Fee',
        'exam' => 'Examination Fee',
        'pta' => 'PTA Levy',
        'transport' => 'Transport Fee',
        'hostel' => 'Hostel/Boarding Fee',
        'other' => 'Other',
    ],

    // Subscription plans
    'plans' => [
        'basic' => ['name' => 'Basic', 'price' => 50000, 'students' => 200, 'staff' => 20, 'storage' => 2],
        'standard' => ['name' => 'Standard', 'price' => 150000, 'students' => 500, 'staff' => 50, 'storage' => 5],
        'premium' => ['name' => 'Premium', 'price' => 350000, 'students' => 1500, 'staff' => 150, 'storage' => 20],
        'enterprise' => ['name' => 'Enterprise', 'price' => 750000, 'students' => 999999, 'staff' => 9999, 'storage' => 100],
    ],
];
