<?php

namespace App\Modules\Transaction\Domain\ValueObjects;

class AccountWithdrawPix
{
    public function __construct(
        public string $type,
        public string $key,
        public ?string $withdrawId = null,
    ) {}
}