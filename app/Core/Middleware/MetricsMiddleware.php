<?php

declare(strict_types=1);

namespace App\Core\Middleware;

use Hyperf\Metric\Contract\MetricFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class MetricsMiddleware implements MiddlewareInterface
{
    public function __construct(
        private MetricFactoryInterface $metricFactory
    ) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $startTime = microtime(true);
        
        $path = $request->getUri()->getPath();
        $method = $request->getMethod();
        
        // Counter: Total de requisições
        $this->metricFactory->makeCounter('http_requests_total', ['method', 'path'])
            ->with($method, $path)
            ->add(1);

        try {
            $response = $handler->handle($request);
            $statusCode = $response->getStatusCode();
            
            // Counter: Requisições por status code
            $this->metricFactory->makeCounter('http_responses_total', ['method', 'path', 'status_code'])
                ->with($method, $path, (string) $statusCode)
                ->add(1);
            
            return $response;
            
        } catch (\Throwable $e) {
            // Counter: Erros
            $this->metricFactory->makeCounter('http_errors_total', ['method', 'path', 'error'])
                ->with($method, $path, get_class($e))
                ->add(1);
            
            throw $e;
            
        } finally {
            $duration = microtime(true) - $startTime;
            
            // Histogram: Duração das requisições
            $this->metricFactory->makeHistogram('http_request_duration_seconds', ['method', 'path'])
                ->with($method, $path)
                ->put($duration);
        }
    }
}

