<?php

namespace App\Modules\Account\Domain\Repositories;

use App\Modules\Account\Domain\Entities\Account;
use Hyperf\Collection\Collection;

interface AccountRepositoryInterface
{
    public function createAccount(Account $account): Account;

    public function listAccounts(): Collection;

    public function findAccountById(string $accountId): Account;
    
    public function deductBalance(string $accountId, float $amount): void;
}