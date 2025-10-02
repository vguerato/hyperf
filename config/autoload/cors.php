<?php

declare(strict_types=1);

return [
    'enable' => true,
    'allow_origins' => ['*'],
    'allow_methods' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
    'allow_headers' => ['Content-Type', 'Authorization', 'X-Requested-With'],
    'expose_headers' => [],
    'max_age' => 0,
    'allow_credentials' => false,
];

