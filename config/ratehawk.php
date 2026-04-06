<?php

return [
    /*
    |--------------------------------------------------------------------------
    | RateHawk API B2B v3 Configuration
    |--------------------------------------------------------------------------
    */

    'key_id'  => env('RATEHAWK_KEY_ID', ''),
    'api_key' => env('RATEHAWK_API_KEY', ''),

    'environment' => env('RATEHAWK_ENV', 'sandbox'),

    'base_url' => env('RATEHAWK_ENV', 'sandbox') === 'production'
        ? env('RATEHAWK_PROD_URL', 'https://api.ratehawk.com')
        : env('RATEHAWK_SANDBOX_URL', 'https://api.sandbox.ratehawk.com'),

    // When true, all API calls return realistic mock data (no credentials needed)
    'use_mock' => env('RATEHAWK_USE_MOCK', true),

    // Cache TTL in seconds
    'cache' => [
        'suggestions' => 3600,        // 1 hour
        'search'      => 300,         // 5 minutes (configurable)
        'hotel_page'  => 86400,       // 24 hours (static content)
        'prices'      => 0,           // Never cache prices
    ],

    // HTTP client settings
    'timeout'  => env('RATEHAWK_TIMEOUT', 30),
    'retries'  => 2,

    // Rate limiting: max requests per second to RateHawk API
    'rate_limit_rps' => 10,

    // Language for API responses
    'language' => 'en',

    // Residency for tax/price calculations (ISO 3166-1 alpha-2)
    'residency' => 'US',
];
