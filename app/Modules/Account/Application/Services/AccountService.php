<?php

namespace App\Modules\Account\Application\Services;

use App\Modules\Account\Application\DTOs\AccountDataDTO;
use App\Modules\Account\Domain\Entities\Account;
use App\Modules\Account\Domain\Repositories\AccountRepositoryInterface;

class AccountService
{
    public function __construct(
        private AccountRepositoryInterface $accountRepository,
    ) {}

    public function createAccount(AccountDataDTO $accountDataDTO): Account
    {
        $account = new Account(
            name: $accountDataDTO->name,
        );

        return $this->accountRepository->createAccount($account);
    }

    public function listAccounts(): array
    {
        return $this->accountRepository
            ->listAccounts()
            ->toArray();
    }
}