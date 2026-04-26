<?php

return [

    'paths' => ['api/chat/*'],

    'allowed_methods' => ['*'],

    'allowed_origins' => array_filter([
        env('FRONTEND_URL', 'http://localhost:3000'),
        env('ADDITIONAL_FRONTEND_URL'),
        'https://noctuacore.ai',
        'https://www.noctuacore.ai',
    ]),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
