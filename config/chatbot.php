<?php

return [

    'pegasus' => [
        'base_url' => env('PEGASUS_API_URL'),
        'api_key' => env('PEGASUS_API_KEY'),
        'timeout' => env('PEGASUS_TIMEOUT', 30),
    ],

    'chat' => [
        'rate_limit' => env('CHAT_RATE_LIMIT', 20),
    ],

];
