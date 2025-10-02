<?php

declare(strict_types=1);

use function Hyperf\Support\env;

return [
    'default' => 'smtp',
    
    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'noreply@tecnofit.com'),
        'name' => env('MAIL_FROM_NAME', 'Tecnofit'),
    ],

    'mailers' => [
        'smtp' => [
            'transport' => 'smtp',
            'host' => env('MAIL_HOST', 'mailhog'),
            'port' => env('MAIL_PORT', 1025),
            'encryption' => env('MAIL_ENCRYPTION', null),
            'username' => env('MAIL_USERNAME'),
            'password' => env('MAIL_PASSWORD'),
            'timeout' => null,
        ],
    ],
];


