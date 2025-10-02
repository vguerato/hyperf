<?php

namespace App\Modules\Transaction\Domain\Entities;

use App\Modules\Transaction\Domain\ValueObjects\AccountWithdrawPix;
use App\Modules\Transaction\Domain\Enums\WithdrawStatus;

class AccountWithdraw
{
    public function __construct(
        public string $accountId,
        public string $method,
        public float $amount,
        public bool $scheduled,
        public ?string $scheduledFor,
        public string $status = WithdrawStatus::PENDING->value,
        public bool $done = false,
        public bool $error = false,
        public ?string $errorReason = null,
        public ?string $doneAt = null,
        public ?string $deniedAt = null,
        public ?string $id = null,
        public ?AccountWithdrawPix $pix = null,
    ) {}

    public function done(): void
    {
        $this->status = WithdrawStatus::DONE->value;
        $this->done = true;
        $this->doneAt = date('Y-m-d H:i:s');
    }

    public function deny($errorReason): void
    {
        $this->status = WithdrawStatus::DENIED->value;
        $this->error = true;
        $this->errorReason = $errorReason;
        $this->deniedAt = date('Y-m-d H:i:s');
    }
}