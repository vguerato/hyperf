<?php

namespace App\Modules\Transaction\Application\Jobs;

use App\Modules\Transaction\Application\Services\WithdrawService;
use Hyperf\AsyncQueue\Job;

use function Hyperf\Support\make;

class ProcessWithdrawJob extends Job
{
    public function __construct(
        public string $withdrawId
    ) {}

    public function handle(): void
    {
        $withdrawService = make(WithdrawService::class);
        $withdrawService->processWithdraw($this->withdrawId);
    }
}


