<?php

namespace App\Modules\Transaction\Application\Cron;

use App\Modules\Transaction\Domain\Repositories\AccountWithdrawRepositoryInterface;
use App\Modules\Transaction\Application\Jobs\ProcessWithdrawJob;
use Hyperf\Crontab\Annotation\Crontab;
use Hyperf\AsyncQueue\Driver\DriverFactory;
use Psr\Log\LoggerInterface;

#[Crontab(
    name: "ProcessScheduledWithdraws",
    rule: "* * * * *",
    callback: "execute",
    memo: "Process pending scheduled withdrawals every minute"
)]
class ProcessScheduledWithdrawsCron
{
    public function __construct(
        private AccountWithdrawRepositoryInterface $withdrawRepository,
        private DriverFactory $driverFactory,
        private LoggerInterface $logger
    ) {}

    public function execute(): void
    {
        $this->logger->info('Starting scheduled withdrawals processing');
        
        try {
            $pendingWithdraws = $this->withdrawRepository->findPendingScheduledWithdraws();
            
            if ($pendingWithdraws->isEmpty()) {
                $this->logger->info('No pending scheduled withdrawals found');
                return;
            }
            
            $this->logger->info('Found pending scheduled withdrawals', [
                'count' => $pendingWithdraws->count(),
            ]);
            
            $driver = $this->driverFactory->get('default');
            
            foreach ($pendingWithdraws as $withdraw) {
                try {
                    // Dispatch job to process the withdraw
                    $driver->push(new ProcessWithdrawJob($withdraw->id), 0);
                    
                    $this->logger->info('Scheduled withdraw job dispatched', [
                        'withdraw_id' => $withdraw->id,
                        'account_id' => $withdraw->accountId,
                        'amount' => $withdraw->amount,
                    ]);
                    
                } catch (\Throwable $e) {
                    $this->logger->error('Failed to dispatch scheduled withdraw job', [
                        'withdraw_id' => $withdraw->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
            
            $this->logger->info('Scheduled withdrawals processing completed');
            
        } catch (\Throwable $e) {
            $this->logger->error('Error processing scheduled withdrawals', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}


