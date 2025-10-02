<?php

declare(strict_types=1);

use function Hyperf\Support\env;

return [
    'default' => [
        'driver' => Hyperf\AsyncQueue\Driver\RedisDriver::class,
        'redis' => [
            'pool' => 'default',
        ],
        'channel' => 'queue',
        'timeout' => 2,
        'retry_seconds' => [5, 10, 30, 60, 120],
        'handle_timeout' => 10,
        'processes' => 1,
        'concurrent' => [
            'limit' => 10,
        ],
    ],
    'email' => [
        'driver' => Hyperf\AsyncQueue\Driver\RedisDriver::class,
        'redis' => [
            'pool' => 'default',
        ],
        'channel' => 'email',
        'timeout' => 2,
        'retry_seconds' => [5, 10, 30],
        'handle_timeout' => 30,
        'processes' => 1,
        'concurrent' => [
            'limit' => 5,
        ],
    ],
];


