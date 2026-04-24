<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost:4200',
        'http://127.0.0.1:4200',
        'https://32db490e1a8e.ngrok-free.app',
    ],

    'allowed_origins_patterns' => ['#^https://.*\.ngrok-free\.app$#'],

    'allowed_headers' => ['*'],

    'exposed_headers' => [
        'Authorization',
        'Content-Type',
        'Content-Disposition',
        'X-CSRF-TOKEN'
    ],

    'max_age' => 0,

    'supports_credentials' => true,
];