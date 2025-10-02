<?php

namespace App\Modules\Account\Infrastructure\Mappers;

use App\Modules\Account\Domain\Entities\Account;
use App\Modules\Account\Infrastructure\Models\Account as AccountModel;
use Hyperf\Collection\Collection;

class AccountMapper
{
    public function toEntity(AccountModel $accountModel): Account
    {
        return new Account(
            id: $accountModel->id,
            name: $accountModel->name,
            balance: $accountModel->balance,
        );
    }

    public function toPersistence(Account $account): AccountModel 
    {
        return new AccountModel([
            'id' => $account->id,
            'name' => $account->name,
            'balance' => $account->balance,
        ]);
    }

    public function toSerializable(Account $account): array
    {
        return [
            'id' => $account->id,
            'name' => $account->name,
            'balance' => $account->balance,
        ];
    }

    public function toCollection(array|Collection $accounts): Collection
    {
        $cast = fn (AccountModel $model) => $this->toEntity($model);
        
        if ($accounts instanceof Collection) {
            return $accounts->map($cast);
        }

        return Collection::make($accounts)->map($cast);
    }
}