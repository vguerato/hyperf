<?php

declare(strict_types=1);

namespace App\Core\Middleware;

use Hyperf\Context\Context;
use Hyperf\Contract\ConfigInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CorsMiddleware implements MiddlewareInterface
{
    public function __construct(
        private ConfigInterface $config
    ) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $corsConfig = $this->config->get('cors', []);
        
        if (!($corsConfig['enable'] ?? false)) {
            return $handler->handle($request);
        }

        if ($request->getMethod() === 'OPTIONS') {
            $response = Context::get(ResponseInterface::class);
            return $this->addCorsHeaders($response, $corsConfig, $request);
        }

        $response = $handler->handle($request);
        return $this->addCorsHeaders($response, $corsConfig, $request);
    }

    private function addCorsHeaders(
        ResponseInterface $response, 
        array $config, 
        ServerRequestInterface $request
    ): ResponseInterface {
        $origin = $request->getHeaderLine('Origin');
        
        $allowOrigins = $config['allow_origins'] ?? ['*'];
        if (in_array('*', $allowOrigins) || in_array($origin, $allowOrigins)) {
            $response = $response->withHeader('Access-Control-Allow-Origin', $origin ?: '*');
        }

        $allowMethods = $config['allow_methods'] ?? ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'];
        $response = $response->withHeader('Access-Control-Allow-Methods', implode(', ', $allowMethods));

        $allowHeaders = $config['allow_headers'] ?? ['Content-Type', 'Authorization', 'X-Requested-With'];
        $response = $response->withHeader('Access-Control-Allow-Headers', implode(', ', $allowHeaders));

        if (!empty($config['expose_headers'])) {
            $response = $response->withHeader('Access-Control-Expose-Headers', implode(', ', $config['expose_headers']));
        }

        if (isset($config['max_age']) && $config['max_age'] > 0) {
            $response = $response->withHeader('Access-Control-Max-Age', (string) $config['max_age']);
        }

        if ($config['allow_credentials'] ?? false) {
            $response = $response->withHeader('Access-Control-Allow-Credentials', 'true');
        }

        return $response;
    }
}
