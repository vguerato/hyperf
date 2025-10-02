<?php

namespace App\Modules\Transaction\Infrastructure\Mappers;

use App\Modules\Transaction\Domain\Entities\AccountWithdraw;
use App\Modules\Transaction\Infrastructure\Models\AccountWithdraw as AccountWithdrawModel;
use Hyperf\Collection\Collection;
use Hyperf\Di\Annotation\Inject;

class AccountWithdrawMapper
{
    #[Inject]
    private AccountWithdrawPixMapper $accountWithdrawPixMapper;

    public function toEntity(AccountWithdrawModel $accountWithdrawModel): AccountWithdraw
    {
        // Garante que o relacionamento pix está carregado
        if (!$accountWithdrawModel->relationLoaded('pix')) {
            $accountWithdrawModel->load('pix');
        }

        return new AccountWithdraw(
            id: $accountWithdrawModel->id,
            accountId: $accountWithdrawModel->account_id,
            method: $accountWithdrawModel->method,
            amount: $accountWithdrawModel->amount,
            scheduled: $accountWithdrawModel->scheduled,
            scheduledFor: $accountWithdrawModel->scheduled_for,
            status: $accountWithdrawModel->status,
            done: $accountWithdrawModel->done,
            error: $accountWithdrawModel->error,
            errorReason: $accountWithdrawModel->error_reason,
            doneAt: $accountWithdrawModel->done_at,
            deniedAt: $accountWithdrawModel->denied_at,
            pix: $this->accountWithdrawPixMapper->toEntity($accountWithdrawModel->pix),
        );
    }

    public function toPersistence(AccountWithdraw $accountWithdraw): AccountWithdrawModel 
    {
        return new AccountWithdrawModel([
            'id' => $accountWithdraw->id,
            'account_id' => $accountWithdraw->accountId,
            'method' => $accountWithdraw->method,
            'amount' => $accountWithdraw->amount,
            'scheduled' => $accountWithdraw->scheduled,
            'scheduled_for' => $accountWithdraw->scheduledFor,
            'status' => $accountWithdraw->status,
            'done' => $accountWithdraw->done,
            'error' => $accountWithdraw->error,
            'error_reason' => $accountWithdraw->errorReason,
            'done_at' => $accountWithdraw->doneAt,
            'denied_at' => $accountWithdraw->deniedAt,
        ]);
    }

    public function toSerializable(AccountWithdraw $accountWithdraw): array
    {
        return [
            'id' => $accountWithdraw->id,
            'account_id' => $accountWithdraw->accountId,
            'method' => $accountWithdraw->method,
            'amount' => $accountWithdraw->amount,
            'scheduled' => $accountWithdraw->scheduled,
            'scheduled_for' => $accountWithdraw->scheduledFor,
            'status' => $accountWithdraw->status,
            'done' => $accountWithdraw->done,
            'error' => $accountWithdraw->error,
            'error_reason' => $accountWithdraw->errorReason,
            'done_at' => $accountWithdraw->doneAt,
            'denied_at' => $accountWithdraw->deniedAt,
        ];
    }

    public function toCollection(array|Collection $accountWithdraws): Collection
    {
        $cast = fn (AccountWithdrawModel $model) => $this->toEntity($model);
        
        if ($accountWithdraws instanceof Collection) {
            return $accountWithdraws->map($cast);
        }

        return Collection::make($accountWithdraws)->map($cast);
    }
}