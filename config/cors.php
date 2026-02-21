<?php

return [

    'paths' => ['api/chat/*'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        env('FRONTEND_URL', 'http://localhost:3000'),
        'https://pack-man.gr',
        'https://www.pack-man.gr',
        'https://noctuacore.ai',
        'https://www.noctuacore.ai',
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
