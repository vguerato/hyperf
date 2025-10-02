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
use Hyperf\Metric\Adapter\Prometheus\Constants;
use Hyperf\Metric\Adapter\Prometheus\MetricFactory;

use function Hyperf\Support\env;

return [
    // To disable hyperf/metric temporarily, set default driver to noop.
    'default' => env('METRIC_DRIVER', 'prometheus'),
    'use_standalone_process' => env('TELEMETRY_USE_STANDALONE_PROCESS', true),
    'enable_default_metric' => env('TELEMETRY_ENABLE_DEFAULT_TELEMETRY', true),
    'default_metric_interval' => (float) env('DEFAULT_METRIC_INTERVAL', 5),
    'metric' => [
        'prometheus' => [
            'driver' => MetricFactory::class,
            'mode' => Constants::SCRAPE_MODE,
            'namespace' => env('APP_NAME', 'skeleton'),
            'redis_config' => env('PROMETHEUS_REDIS_CONFIG', 'default'),
            'redis_prefix' => env('PROMETHEUS_REDIS_PREFIX', 'prometheus:'),
            'redis_gather_key_suffix' => env('PROMETHEUS_REDIS_GATHER_KEY_SUFFIX', ':metric_keys'),
            'scrape_host' => env('PROMETHEUS_SCRAPE_HOST', '0.0.0.0'),
            'scrape_port' => env('PROMETHEUS_SCRAPE_PORT', '9502'),
            'scrape_path' => env('PROMETHEUS_SCRAPE_PATH', '/metrics'),
            'push_host' => env('PROMETHEUS_PUSH_HOST', '0.0.0.0'),
            'push_port' => env('PROMETHEUS_PUSH_PORT', '9091'),
            'push_interval' => (float) env('PROMETHEUS_PUSH_INTERVAL', 5),
        ],
    ],
];
