<?php

namespace App\Modules\Transaction\Domain\Events;

use App\Modules\Transaction\Domain\Entities\AccountWithdraw;

class WithdrawProcessed
{
    public function __construct(
        public AccountWithdraw $withdraw
    ) {}
}


