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

namespace App\Core\Exception;

use App\Core\Exception\UnauthorizedResourceAccessException;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\Validation\ValidationException;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Throwable;

class AppExceptionHandler extends ExceptionHandler
{
    #[Inject]
    protected StdoutLoggerInterface $streamLogger;

    #[Inject]
    protected LoggerInterface $logger;

    public function __construct()
    {
    }

    private function getStatusCode(Throwable $throwable): int
    {
        $code = $throwable->getCode();
        if (is_int($code) && ($code >= 400 && $code < 500)) {
            return $code;
        }

        return 500;
    }

    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        $statusCode = $this->getStatusCode($throwable);
        $message = $statusCode !== 500 ? $throwable->getMessage() : 'Internal Server Error';

        if ($throwable instanceof ValidationException) {
            $message = $throwable->validator->errors()->first();
            $statusCode = 422;
        }

        $this->logger->error(sprintf('%s[%s] in %s', $throwable->getMessage(), $throwable->getLine(), $throwable->getFile()));
        $this->logger->error($throwable->getTraceAsString());

        return $response
            ->withStatus($statusCode)
            ->withHeader('Content-Type', 'application/json')
            ->withBody(new SwooleStream(json_encode([
                'errorMessage' => $message,
                'statusCode' => $statusCode,
            ])));
    }

    public function isValid(Throwable $throwable): bool
    {
        return true;
    }
}
