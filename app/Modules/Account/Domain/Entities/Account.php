<?php

namespace App\Modules\Account\Domain\Entities;

class Account
{
    public function __construct(
        public string $name,
        public ?string $id = null,
        public float $balance = 0,
    ) {}
}