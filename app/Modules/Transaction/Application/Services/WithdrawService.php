<?php

namespace App\Modules\Transaction\Application\Services;


use App\Modules\Transaction\Domain\Repositories\AccountWithdrawRepositoryInterface;
use App\Modules\Transaction\Domain\Entities\AccountWithdraw;
use App\Modules\Transaction\Application\DTOs\WithdrawDto;
use App\Modules\Transaction\Application\DTOs\WithdrawPixDto;
use App\Modules\Transaction\Domain\ValueObjects\AccountWithdrawPix;
use App\Modules\Account\Domain\Repositories\AccountRepositoryInterface;
use App\Modules\Transaction\Domain\Events\WithdrawProcessed;
use App\Modules\Transaction\Domain\Events\WithdrawFailed;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Hyperf\Metric\Contract\MetricFactoryInterface;
use Throwable;

class WithdrawService
{
    public function __construct(
        private AccountWithdrawRepositoryInterface $accountWithdrawRepository,
        private AccountRepositoryInterface $accountRepository,
        private EventDispatcherInterface $eventDispatcher,
        private LoggerInterface $logger,
        private MetricFactoryInterface $metricFactory,
    ) {}
    
    public function listWithdraws(string $accountId): \Hyperf\Collection\Collection
    {
        return $this->accountWithdrawRepository->listWithdraws($accountId);
    }

    public function requestWithdraw(WithdrawDto $WithdrawDto): AccountWithdraw
    {
        $this->metricFactory->makeCounter('withdraws_requested_total', ['method', 'scheduled'])
            ->with($WithdrawDto->method, $WithdrawDto->scheduled ? 'true' : 'false')
            ->add(1);
        
        $pix = null;
        if ($WithdrawDto->pix instanceof WithdrawPixDto) {
            $pix = new AccountWithdrawPix(
                type: $WithdrawDto->pix->type,
                key: $WithdrawDto->pix->key,
            );
        }
        
        $withdraw = new AccountWithdraw(
            accountId: $WithdrawDto->accountId,
            method: $WithdrawDto->method,
            amount: $WithdrawDto->amount,
            scheduled: $WithdrawDto->scheduled,
            scheduledFor: $WithdrawDto->scheduledFor,
            pix: $pix,
        );

        return $this->accountWithdrawRepository->createWithdraw($withdraw);
    }

    /**
     * Process a withdraw by withdrawId
     * This method is called by ProcessWithdrawJob
     */
    public function processWithdraw(string $withdrawId): void
    {
        $startTime = microtime(true);

        $withdraw = null;
        
        try {
            $withdraw = $this->accountWithdrawRepository->findById($withdrawId);
            
            if (!$withdraw) {
                $this->logger->error('Withdraw not found', ['withdraw_id' => $withdrawId]);
                return;
            }
            
            if ($withdraw->done || $withdraw->error) {
                $this->logger->info('Withdraw already processed', ['withdraw_id' => $withdrawId]);
                return;
            }
            
            $this->accountRepository->deductBalance($withdraw->accountId, $withdraw->amount);
            
            $withdraw->done();
            $this->accountWithdrawRepository->updateWithdraw($withdraw);
            
            $this->logger->info('Withdraw processed successfully', [
                'withdraw_id' => $withdrawId,
                'account_id' => $withdraw->accountId,
                'amount' => $withdraw->amount,
            ]);
            
            $this->metricFactory->makeCounter('withdraws_processed_total', ['method', 'status'])
                ->with($withdraw->method, 'success')
                ->add(1);
            
            $this->eventDispatcher->dispatch(new WithdrawProcessed($withdraw));
            
        } catch (Throwable $e) {
            $this->logger->error('Failed to process withdraw', [
                'withdraw_id' => $withdrawId,
                'error' => $e->getMessage(),
            ]);
                
            if ($withdraw) {
                $this->metricFactory->makeCounter('withdraws_processed_total', ['method', 'status'])
                    ->with($withdraw->method, 'failed')
                    ->add(1);

                $this->metricFactory->makeCounter('withdraws_failed_by_reason', ['method', 'reason'])
                    ->with($withdraw->method, get_class($e))
                    ->add(1);
                
                $withdraw->deny($e->getMessage());
                $this->accountWithdrawRepository->updateWithdraw($withdraw);
                
                $this->eventDispatcher->dispatch(new WithdrawFailed($withdraw));
            }
        } finally {
            $duration = microtime(true) - $startTime;
            
            // Métrica: Tempo de processamento de saque
            $this->metricFactory->makeHistogram('withdraw_processing_duration_seconds', ['method'])
                ->with($withdraw->method ?? 'unknown')
                ->put($duration);
        }
    }
}