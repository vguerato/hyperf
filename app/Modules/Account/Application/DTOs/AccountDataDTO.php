<?php

namespace App\Modules\Account\Application\DTOs;

class AccountDataDTO
{
    public function __construct(
        public string $name,
    ) {}
}