<?php

namespace App\Modules\Transaction\Domain\Repositories;

use App\Modules\Transaction\Domain\Entities\AccountWithdraw;
use Hyperf\Collection\Collection;

interface AccountWithdrawRepositoryInterface
{
    public function listWithdraws(string $accountId): Collection;

    public function createWithdraw(AccountWithdraw $withdraw): AccountWithdraw;

    public function updateWithdraw(AccountWithdraw $withdraw): AccountWithdraw;
    
    public function findPendingScheduledWithdraws(): Collection;
    
    public function findById(string $withdrawId): ?AccountWithdraw;
}           