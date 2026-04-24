<?php

return [
    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'paystack' => [
        'public' => env('PAYSTACK_PUBLIC_KEY'),
        'secret' => env('PAYSTACK_SECRET_KEY'),
        'url' => env('PAYSTACK_PAYMENT_URL', 'https://api.paystack.co'),
        'merchant_email' => env('PAYSTACK_MERCHANT_EMAIL'),
    ],

    'flutterwave' => [
        'public' => env('FLUTTERWAVE_PUBLIC_KEY'),
        'secret' => env('FLUTTERWAVE_SECRET_KEY'),
        'hash' => env('FLUTTERWAVE_SECRET_HASH'),
        'encryption' => env('FLUTTERWAVE_ENCRYPTION_KEY'),
        'url' => env('FLUTTERWAVE_BASE_URL', 'https://api.flutterwave.com/v3'),
    ],

    'payment_gateways' => [
        'ssl_verify' => env('PAYMENT_GATEWAY_SSL_VERIFY'),
        'ca_bundle' => env('PAYMENT_GATEWAY_CA_BUNDLE'),
    ],

    'sms' => [
        'driver' => env('SMS_DRIVER', 'termii'),
    ],

    'termii' => [
        'api_key' => env('TERMII_API_KEY'),
        'sender_id' => env('TERMII_SENDER_ID', 'EMS'),
        'base_url' => env('TERMII_BASE_URL', 'https://api.ng.termii.com'),
    ],

    'twilio' => [
        'sid' => env('TWILIO_SID'),
        'auth_token' => env('TWILIO_AUTH_TOKEN'),
        'from' => env('TWILIO_FROM'),
    ],

    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
        'base_url' => env('OPENAI_BASE_URL', 'https://api.openai.com/v1'),
        'timeout' => env('OPENAI_TIMEOUT', 30),
    ],
];
