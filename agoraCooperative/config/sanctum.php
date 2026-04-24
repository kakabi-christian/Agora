<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Stateful Domains
    |--------------------------------------------------------------------------
    |
    | Requests from the following domains / hosts will receive stateful API
    | authentication cookies. This must include all frontends qui utilisent
    | ton API, en dev et prod (localhost, ngrok, etc.).
    |
    */

    'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', sprintf(
        '%s%s',
        'localhost,localhost:3000,127.0.0.1,127.0.0.1:8000,::1',
        env('APP_URL') ? ','.parse_url(env('APP_URL'), PHP_URL_HOST) : ''
    ))),

    /*
    |--------------------------------------------------------------------------
    | Sanctum Guards
    |--------------------------------------------------------------------------
    |
    | Les guards qui seront vérifiés lors de l'authentification d'une requête.
    |
    */

    'guard' => ['web'],

    /*
    |--------------------------------------------------------------------------
    | Expiration Minutes
    |--------------------------------------------------------------------------
    |
    | Durée de validité des tokens. Null = tokens sans expiration.
    |
    */

    'expiration' => 120, // 2 heures

    /*
    |--------------------------------------------------------------------------
    | Sanctum Middleware
    |--------------------------------------------------------------------------
    |
    | Middleware utilisé par Sanctum pour gérer l'authentification et les cookies.
    |
    */

    'middleware' => [
        'verify_csrf_token' => App\Http\Middleware\VerifyCsrfToken::class,
        'encrypt_cookies'   => App\Http\Middleware\EncryptCookies::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Options additionnelles pour Angular + ngrok
    |--------------------------------------------------------------------------
    |
    | Assurer que les cookies sont sécurisés et compatibles avec les SPA
    | via HTTPS (ngrok) et avecCredentials.
    |
    */

    'prefix' => 'sanctum',             // Préfixe pour les routes Sanctum
    'cookie' => 'laravel_session',     // Nom du cookie de session
];
