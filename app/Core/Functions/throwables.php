<?php

declare(strict_types=1);

namespace App\Core\Functions;

use Throwable;

function getSafeStatusCode(Throwable $throwable): int
{
    $code = $throwable->getCode();
    if (is_int($code) && ($code >= 400 && $code < 500)) {
        return $code;
    }

    return 500;
}

function getSafeMessage(Throwable $throwable, string $safeMessage): string
{
    return getSafeStatusCode($throwable) !== 500 ? $throwable->getMessage() : $safeMessage;
}