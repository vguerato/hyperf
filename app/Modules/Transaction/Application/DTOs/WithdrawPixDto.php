<?php

namespace App\Modules\Transaction\Application\DTOs;

class WithdrawPixDto
{
    public function __construct(
        public string $type,
        public string $key,
    ) {}
}