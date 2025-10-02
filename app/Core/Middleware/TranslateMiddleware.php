<?php

declare(strict_types=1);

namespace App\Core\Middleware;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Contract\TranslatorInterface;
use Hyperf\Contract\ConfigInterface;

class TranslateMiddleware implements MiddlewareInterface
{
    #[Inject]
    protected TranslatorInterface $translator;
    
    #[Inject]
    protected ConfigInterface $config;

    public function __construct(protected ContainerInterface $container)
    {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $fallbackLocale = $this->config->get('locale.fallback_locale');

        $locale = $request->getHeaderLine('Accept-Language') ?: $fallbackLocale;
        
        $this->translator->setLocale($locale);

        return $handler->handle($request);
    }

}
