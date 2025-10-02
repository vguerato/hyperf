<?php

namespace App\Modules\Account\Infrastructure\Repositories;

use App\Core\Exception\InsufficientFundsException;
use App\Core\Exception\UnauthorizedResourceAccessException;
use App\Modules\Account\Domain\Entities\Account;
use App\Modules\Account\Domain\Repositories\AccountRepositoryInterface;
use App\Modules\Account\Infrastructure\Mappers\AccountMapper;
use Hyperf\Di\Annotation\Inject;
use App\Modules\Account\Infrastructure\Models\Account as AccountModel;
use Hyperf\Collection\Collection;
use Psr\Log\LoggerInterface;

class AccountRepository implements AccountRepositoryInterface
{
    #[Inject]
    private AccountMapper $accountMapper;

    #[Inject]
    private LoggerInterface $logger;

    /**
     * @param Account $account
     * @return Account
     */
    public function createAccount(Account $account): Account
    {
        $this->logger->info('Creating account', [
            'account' => $this->accountMapper->toSerializable($account),
        ]);

        $model = AccountModel::create($this->accountMapper->toSerializable($account));

        return $this->accountMapper->toEntity($model);
    }

    /**
     * @return Collection
     */
    public function listAccounts(): Collection
    {
        $models = AccountModel::all();

        return $this->accountMapper->toCollection($models);
    }

    public function findAccountById(string $accountId): Account
    {
        $model = AccountModel::find($accountId);

        return $this->accountMapper->toEntity($model);
    }
    
    public function deductBalance(string $accountId, float $amount): void
    {
        \Hyperf\DbConnection\Db::transaction(function () use ($accountId, $amount) {
            $model = AccountModel::query()
                ->lockForUpdate()
                ->find($accountId);
            
            if (!$model) {
                throw new UnauthorizedResourceAccessException();
            }
            
            $newBalance = bcsub((string) $model->balance, (string) $amount, 2);
            
            if ($newBalance < 0) {
                throw new InsufficientFundsException();
            }
            
            $model->balance = (float) $newBalance;
            $model->save();
            
            $this->logger->info('Balance deducted', [
                'account_id' => $accountId,
                'amount' => $amount,
                'new_balance' => $newBalance,
            ]);
        });
    }
}