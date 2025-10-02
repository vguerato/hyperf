<?php

namespace App\Modules\Transaction\Application\DTOs;

class WithdrawDto
{
    public function __construct(
        public string $accountId,
        public string $method,
        public float $amount,
        public bool $scheduled,
        public ?string $scheduledFor,
        public ?WithdrawPixDto $pix,
    ) {}
}