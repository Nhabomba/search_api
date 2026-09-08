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
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
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

    'open_meteo' => [
        'geocoding_url' => env('OPEN_METEO_GEOCODING_URL', 'https://geocoding-api.open-meteo.com/v1/search'),
        'forecast_url' => env('OPEN_METEO_FORECAST_URL', 'https://api.open-meteo.com/v1/forecast'),
        'language' => env('OPEN_METEO_LANGUAGE', 'pt'),
        'geocoding_count' => (int) env('OPEN_METEO_GEOCODING_COUNT', 1),
        'geocoding_min_population' => (int) env('OPEN_METEO_GEOCODING_MIN_POPULATION', 10_000),
        'geocoding_format' => env('OPEN_METEO_GEOCODING_FORMAT', 'json'),
        'forecast_timezone' => env('OPEN_METEO_FORECAST_TIMEZONE', 'auto'),
        'forecast_current_fields' => array_values(array_filter(array_map(
            'trim',
            explode(',', (string) env(
                'OPEN_METEO_FORECAST_CURRENT',
                'temperature_2m,relative_humidity_2m,apparent_temperature,weather_code,wind_speed_10m',
            )),
        ))),
        'timeout' => (int) env('OPEN_METEO_TIMEOUT', 30),
        'connect_timeout' => (int) env('OPEN_METEO_CONNECT_TIMEOUT', 10),
        'verify_ssl' => filter_var(env('OPEN_METEO_VERIFY_SSL', true), FILTER_VALIDATE_BOOLEAN),
        'force_ipv4' => filter_var(env('OPEN_METEO_FORCE_IPV4', true), FILTER_VALIDATE_BOOLEAN),
        'cache_ttl' => (int) env('OPEN_METEO_CACHE_TTL', 300),
    ],

    'weather_history' => [
        'pagination_enabled' => filter_var(env('WEATHER_HISTORY_PAGINATION_ENABLED', true), FILTER_VALIDATE_BOOLEAN),
        'per_page' => (int) env('WEATHER_HISTORY_PER_PAGE', 15),
    ],

];
