<?php

declare(strict_types=1);

namespace App\Core\Exception;

use Hyperf\Server\Exception\ServerException;
use Throwable;

class UnauthorizedResourceAccessException extends ServerException
{
    public function __construct(
        string $message = 'Not authorized to access this resource.',
        int $code = 403,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}

