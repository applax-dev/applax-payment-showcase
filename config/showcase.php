<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Showcase Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration file controls the behavior and features displayed
    | in the Applax Payment Gateway showcase application.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Payment Methods Configuration
    |--------------------------------------------------------------------------
    |
    | Configure which payment methods are available and displayed in the
    | checkout process. Set 'enabled' to true to show the payment method.
    |
    */
    'payment_methods' => [
        'card' => [
            'enabled' => true,
            'name' => 'Credit/Debit Card',
            'description' => 'Secure card payment with 3D Secure',
            'icon' => 'fas fa-credit-card',
            'demo_functional' => true,
            'notes' => 'Fully functional with test card numbers'
        ],
        'apple_pay' => [
            'enabled' => false,
            'name' => 'Apple Pay',
            'description' => 'Touch ID or Face ID authentication',
            'icon' => 'fab fa-apple',
            'demo_functional' => false,
            'notes' => 'Coming soon - SDK integration in progress'
        ],
        'google_pay' => [
            'enabled' => false,
            'name' => 'Google Pay',
            'description' => 'Fast and secure Google payments',
            'icon' => 'fab fa-google',
            'demo_functional' => false,
            'notes' => 'Coming soon - SDK integration in progress'
        ],
        'paypal' => [
            'enabled' => false,
            'name' => 'PayPal',
            'description' => 'PayPal account payments',
            'icon' => 'fab fa-paypal',
            'demo_functional' => false,
            'notes' => 'Coming soon - SDK integration in progress'
        ],
        'klarna' => [
            'enabled' => false,
            'name' => 'Klarna',
            'description' => 'Buy now, pay later options',
            'icon' => 'fas fa-calendar-alt',
            'demo_functional' => false,
            'notes' => 'Coming soon - SDK integration in progress'
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Demo Configuration
    |--------------------------------------------------------------------------
    |
    | General showcase and demo configuration options.
    |
    */
    'demo' => [
        'auto_reset_data' => env('SHOWCASE_AUTO_RESET', true),
        'reset_interval_hours' => env('SHOWCASE_RESET_HOURS', 24),
        'show_coming_soon_methods' => env('SHOWCASE_SHOW_COMING_SOON', true),
        'test_mode_indicators' => env('SHOWCASE_TEST_INDICATORS', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Feature Flags
    |--------------------------------------------------------------------------
    |
    | Enable or disable specific showcase features.
    |
    */
    'features' => [
        'admin_dashboard' => env('SHOWCASE_ADMIN_DASHBOARD', true),
        'sdk_showcase' => env('SHOWCASE_SDK_SHOWCASE', true),
        'webhook_logs' => env('SHOWCASE_WEBHOOK_LOGS', true),
        'real_time_updates' => env('SHOWCASE_REAL_TIME', true),
        'order_retry' => env('SHOWCASE_ORDER_RETRY', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | UI Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the user interface elements and behavior.
    |
    */
    'ui' => [
        'primary_color' => '#328a75',
        'text_color' => '#3b4151',
        'show_demo_badges' => true,
        'show_test_warnings' => true,
        'auto_hide_alerts' => true,
    ]
];
