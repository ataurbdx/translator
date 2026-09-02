<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default & Fallback Locales
    |--------------------------------------------------------------------------
    |
    | The default locale used when no locale is explicitly requested.
    | Fallback locale is returned whenever a translation key is missing.
    |
    */
    'default_locale' => env('TRANSLATOR_ENGINE_DEFAULT_LOCALE', 'en'),
    'fallback_locale' => env('TRANSLATOR_ENGINE_FALLBACK_LOCALE', 'en'),

    /*
    |--------------------------------------------------------------------------
    | Supported Locales
    |--------------------------------------------------------------------------
    |
    | List of active locales supported across the application and API.
    |
    */
    'supported_locales' => [
        'en' => [
            'name' => 'English',
            'native' => 'English',
            'direction' => 'ltr',
            'currency' => 'USD',
            'currency_symbol' => '$',
        ],
        'bn' => [
            'name' => 'Bengali',
            'native' => 'বাংলা',
            'direction' => 'ltr',
            'currency' => 'BDT',
            'currency_symbol' => '৳',
            'group_style' => 'south_asian',
        ],
        'ar' => [
            'name' => 'Arabic',
            'native' => 'العربية',
            'direction' => 'rtl',
            'currency' => 'AED',
            'currency_symbol' => 'د.إ',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Database Table Names & Prefix
    |--------------------------------------------------------------------------
    |
    | Unified table prefix for all database tables managed by TranslatorEngine.
    |
    */
    'tables' => [
        'prefix' => 'translator_engine_',
        'languages' => 'translator_engine_languages',
        'settings' => 'translator_engine_settings',
        'statics' => 'translator_engine_statics',
        'dynamics' => 'translator_engine_dynamics',
        'locales' => 'translator_engine_locales',
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Settings
    |--------------------------------------------------------------------------
    |
    | High-performance multi-tier caching settings for static keys & locales.
    |
    */
    'cache' => [
        'enabled' => env('TRANSLATOR_ENGINE_CACHE_ENABLED', true),
        'driver' => env('TRANSLATOR_ENGINE_CACHE_DRIVER', null), // null defaults to cache.default
        'prefix' => 'translator_engine_',
        'ttl' => env('TRANSLATOR_ENGINE_CACHE_TTL', 86400), // 24 hours in seconds
    ],

    /*
    |--------------------------------------------------------------------------
    | Cultural & Local Formatting
    |--------------------------------------------------------------------------
    |
    | Storage and file fallback options for cultural localization rules.
    |
    */
    'local' => [
        'driver' => env('TRANSLATOR_ENGINE_LOCAL_DRIVER', 'hybrid'), // 'database', 'file', or 'hybrid'
        'export_path' => resource_path('lang/locales'),
    ],

    /*
    |--------------------------------------------------------------------------
    | AI Automation Settings
    |--------------------------------------------------------------------------
    |
    | Credentials and default driver for AI-powered auto-translations.
    |
    */
    'ai' => [
        'default_provider' => env('TRANSLATOR_ENGINE_AI_PROVIDER', 'gemini'), // 'gemini', 'openai', 'deepl'
        'providers' => [
            'gemini' => [
                'api_key' => env('GEMINI_API_KEY', ''),
                'model' => env('GEMINI_TRANSLATION_MODEL', 'gemini-1.5-flash'),
            ],
            'openai' => [
                'api_key' => env('OPENAI_API_KEY', ''),
                'model' => env('OPENAI_TRANSLATION_MODEL', 'gpt-4o-mini'),
            ],
            'deepl' => [
                'api_key' => env('DEEPL_API_KEY', ''),
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Headless API Settings (Flutter, Next.js, React, MERN)
    |--------------------------------------------------------------------------
    |
    | REST API endpoints and caching headers for cross-platform clients.
    |
    */
    'api' => [
        'enabled' => env('TRANSLATOR_ENGINE_API_ENABLED', true),
        'prefix' => 'api/v1/translator-engine',
        'middleware' => ['api'],
        'etag' => true,
    ],

];
