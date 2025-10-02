<?php

namespace App\Modules\Shared\Application\Services;

use App\Modules\Account\Domain\Repositories\AccountRepositoryInterface;
use App\Core\Exception\UnauthorizedResourceAccessException;

class AccountBalanceService
{
    public function __construct(
        private AccountRepositoryInterface $accountRepository,
    ) {}

    public function getBalance(string $accountId): float
    {
        $account = $this->accountRepository->findAccountById($accountId);
        if (!$account) {
            throw new UnauthorizedResourceAccessException('Account not found or access denied.');
        }

        return $account->balance;
    }
}