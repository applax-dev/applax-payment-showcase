<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
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

    /*
    |--------------------------------------------------------------------------
    | Applax Gate SDK Configuration
    |--------------------------------------------------------------------------
    */

    'gate' => [
        'api_key' => env('GATE_API_KEY'),
        'sandbox' => env('GATE_SANDBOX_MODE', true),
        'webhook_secret' => env('GATE_WEBHOOK_SECRET'),
        'base_url' => env('GATE_BASE_URL', 'https://gate.appla-x.com/api/v0.6'),
        'brand_id' => env('GATE_BRAND_ID', 'f13fc723-6006-4a2e-af43-6a48b037c441'),
    ],

];
