<?php

declare(strict_types=1);

namespace App\Core\Exception;

use Hyperf\Server\Exception\ServerException;
use Throwable;

class InsufficientFundsException extends ServerException
{
    public function __construct(
        string $message = 'Insufficient funds for this operation.',
        int $code = 400,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}

