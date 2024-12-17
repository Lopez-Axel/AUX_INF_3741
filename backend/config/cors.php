<?php

return [
    'paths' => ['api/*', 'api/v1/*'],
    'allowed_methods' => ['*'],
    'allowed_origins' => [
        'http://localhost:8081',
        'http://127.0.0.1:8081'
    ],
    'allowed_headers' => ['*'],
    'exposed_headers' => ['*'],
    'max_age' => 0,
    'supports_credentials' => false,
];