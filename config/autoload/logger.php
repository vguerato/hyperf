<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

use function Hyperf\Support\env;

return [
    'default' => [
        'handler' => ['stream'],
    ],

    'stream' => [
        'handler' => [
            'class' => Monolog\Handler\SyslogUdpHandler::class,
            'constructor' => [
                'host' => env('SYSLOG_HOST', 'promtail'),
                'port' => env('SYSLOG_PORT', 1514),
                'facility' => LOG_USER,
                'level' => \Monolog\Level::Debug,
            ],
        ],
        'formatter' => [
            'class' => Monolog\Formatter\JsonFormatter::class,
            'constructor' => [
                'batchMode' => Monolog\Formatter\JsonFormatter::BATCH_MODE_NEWLINES,
                'appendNewline' => true,
            ],
        ],
    ],

    'daily' => [
        'handler' => [
            'class' => Monolog\Handler\StreamHandler::class,
            'constructor' => [
                'stream' => BASE_PATH . '/runtime/logs/hyperf-debug.log',
                'level' => Monolog\Level::Error,
            ],
        ],
        'formatter' => [
            'class' => Monolog\Formatter\JsonFormatter::class,
            'constructor' => [
                'batchMode' => Monolog\Formatter\JsonFormatter::BATCH_MODE_JSON,
                'appendNewline' => true,
            ],
        ],
    ],
];
