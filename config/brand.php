<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Single Brand Mode
    |--------------------------------------------------------------------------
    |
    | This installation is intended to run as one branded KG, Primary, and
    | Secondary school system rather than a multi-tenant SaaS. When enabled,
    | the app always resolves one active school as the main brand context.
    |
    */
    'single_school_mode' => env('SINGLE_SCHOOL_MODE', true),

    /*
    |--------------------------------------------------------------------------
    | Default School
    |--------------------------------------------------------------------------
    |
    | Optionally pin the application to a specific school record. When null,
    | the first active school will be used automatically.
    |
    */
    'default_school_id' => env('DEFAULT_SCHOOL_ID'),
];
