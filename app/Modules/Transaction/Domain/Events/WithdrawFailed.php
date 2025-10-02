<?php

namespace App\Modules\Transaction\Domain\Events;

use App\Modules\Transaction\Domain\Entities\AccountWithdraw;

class WithdrawFailed
{
    public function __construct(
        public AccountWithdraw $withdraw
    ) {}
}


