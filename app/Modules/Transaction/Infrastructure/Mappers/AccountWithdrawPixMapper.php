<?php

namespace App\Modules\Transaction\Infrastructure\Mappers;

use App\Modules\Transaction\Domain\ValueObjects\AccountWithdrawPix;
use App\Modules\Transaction\Infrastructure\Models\AccountWithdrawPix as AccountWithdrawPixModel;

class AccountWithdrawPixMapper
{
    public function toEntity(?AccountWithdrawPixModel $accountWithdrawPixModel): ?AccountWithdrawPix
    {
        if (!$accountWithdrawPixModel) {
            return null;
        }

        return new AccountWithdrawPix(
            type: $accountWithdrawPixModel->type,
            key: $accountWithdrawPixModel->key,
            withdrawId: $accountWithdrawPixModel->account_withdraw_id,
        );
    }

    public function toPersistence(AccountWithdrawPix $accountWithdrawPix, string $withdrawId): AccountWithdrawPixModel
    {
        return new AccountWithdrawPixModel([
            'account_withdraw_id' => $withdrawId,
            'type' => $accountWithdrawPix->type,
            'key' => $accountWithdrawPix->key,
        ]);
    }

    public function toSerializable(AccountWithdrawPix $accountWithdrawPix, string $withdrawId): array
    {
        return [
            'account_withdraw_id' => $withdrawId,
            'type' => $accountWithdrawPix->type,
            'key' => $accountWithdrawPix->key,
        ];
    }
}