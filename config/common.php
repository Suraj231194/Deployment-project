<?php

return [
    'home_hero_slides' => [
        [
            'tag' => 'Diagnostic Innovation',
            'title' => 'A One-Stop Diagnostic Powerhouse for India.',
            'copy' => 'From rapid IVD kits to intelligent instruments, Biogenix helps labs and hospitals scale with speed, quality, and dependable support.',
            'image' => 'upload/corousel/image1.jpg',
        ],
        [
            'tag' => 'Clinical Workflow',
            'title' => 'Precision technologies for modern care delivery.',
            'copy' => 'Integrated catalog, quote, and fulfillment workflows built for high-performance diagnostics teams.',
            'image' => 'upload/corousel/image3.jpg',
        ],
        [
            'tag' => 'Lucknow Operations',
            'title' => 'Fast logistics support with trusted service execution.',
            'copy' => 'Same-day assistance for priority requirements with transparent communication and support.',
            'image' => 'upload/corousel/hema1.jpg',
        ],
    ],
    'home_page_category_slugs' => [
        'diagnostics',
        'biochemistry',
        'molecular-diagnostics',
        'laboratory-equipment',
        'consumables',
    ],
    'b2b_designation_options' => [
        'dealer' => 'Dealer',
        'distributor' => 'Distributor',
        'sales' => 'Sales',
        'lab' => 'Laboratory',
        'hospital' => 'Hospital',
    ],
    'email_notifications' => [
        'provider' => env('EMAIL_NOTIFICATION_PROVIDER', 'log'),
        'from_name' => env('EMAIL_NOTIFICATION_FROM_NAME', env('APP_NAME', 'Biogenix')),
        'from_email' => env('EMAIL_NOTIFICATION_FROM_EMAIL', env('MAIL_FROM_ADDRESS', 'noreply@biogenix.com')),
        'brevo' => [
            'api_key' => env('BREVO_API_KEY', ''),
            'base_url' => env('BREVO_BASE_URL', 'https://api.brevo.com/v3'),
            'timeout_seconds' => (int) env('BREVO_TIMEOUT_SECONDS', 15),
            'verify_ssl' => env('BREVO_VERIFY_SSL', true)
        ],
    ],
    'signup_email_otp' => [
        'expiry_minutes' => 10,
        'resend_cooldown_seconds' => 60,
        'verified_window_minutes' => 30,
        'max_attempts' => 5,
    ],
    'meeting_hours' => [
        'start_time' => '09:00',
        'end_time' => '18:00',
        'timezone_label' => 'IST',
    ],
    'frequently_bought_together_limit' => 4,
];
