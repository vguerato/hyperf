<?php

namespace App\Modules\Transaction\Infrastructure\Repositories;

use App\Modules\Transaction\Domain\Entities\AccountWithdraw;
use App\Modules\Transaction\Domain\Repositories\AccountWithdrawRepositoryInterface;
use App\Modules\Transaction\Infrastructure\Mappers\AccountWithdrawMapper;
use App\Modules\Transaction\Infrastructure\Mappers\AccountWithdrawPixMapper;
use Hyperf\Di\Annotation\Inject;
use App\Modules\Transaction\Infrastructure\Models\AccountWithdraw as AccountWithdrawModel;
use App\Modules\Transaction\Infrastructure\Models\AccountWithdrawPix as AccountWithdrawPixModel;
use Hyperf\Collection\Collection;
use Hyperf\DbConnection\Db;

class AccountWithdrawRepository implements AccountWithdrawRepositoryInterface
{
    #[Inject]
    private AccountWithdrawMapper $accountWithdrawMapper;

    #[Inject]
    private AccountWithdrawPixMapper $accountWithdrawPixMapper;

    public function listWithdraws(string $accountId): Collection
    {
        $models = AccountWithdrawModel::query()
            ->where('account_id', $accountId)
            ->with('pix')
            ->get();

        return $this->accountWithdrawMapper->toCollection($models);
    }

    public function createWithdraw(AccountWithdraw $accountWithdraw): AccountWithdraw
    {
        $model = AccountWithdrawModel::create($this->accountWithdrawMapper->toSerializable($accountWithdraw));

        if ($accountWithdraw->pix) {
            $this->accountWithdrawPixMapper
                ->toPersistence($accountWithdraw->pix, $model->id)
                ->save();
        }

        return $this->accountWithdrawMapper->toEntity($model);
    }

    public function updateWithdraw(AccountWithdraw $accountWithdraw): AccountWithdraw
    {
        $model = AccountWithdrawModel::query()->sharedLock()->find($accountWithdraw->id);
            
        Db::transaction(fn () => $model->update([
            'status' => $accountWithdraw->status,
            'done' => $accountWithdraw->done,
            'error' => $accountWithdraw->error,
            'error_reason' => $accountWithdraw->errorReason,
            'done_at' => $accountWithdraw->doneAt,
            'denied_at' => $accountWithdraw->deniedAt,
        ]));

        return $this->accountWithdrawMapper->toEntity($model);
    }
    
    public function findPendingScheduledWithdraws(): Collection
    {
        $models = AccountWithdrawModel::query()
            ->where('scheduled', true)
            ->where('done', false)
            ->where('error', false)
            ->where('scheduled_for', '<=', \Carbon\Carbon::now())
            ->with('pix')
            ->get();

        return $this->accountWithdrawMapper->toCollection($models);
    }
    
    public function findById(string $withdrawId): ?AccountWithdraw
    {
        $model = AccountWithdrawModel::query()
            ->with('pix')
            ->find($withdrawId);
        
        if (!$model) {
            return null;
        }
        
        return $this->accountWithdrawMapper->toEntity($model);
    }
}