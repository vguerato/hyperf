<?php

declare(strict_types=1);

namespace HyperfTest\Cases\Integration;

use App\Modules\Account\Domain\Repositories\AccountRepositoryInterface;
use App\Modules\Transaction\Application\Services\WithdrawService;
use App\Modules\Transaction\Domain\Repositories\AccountWithdrawRepositoryInterface;
use App\Modules\Transaction\Application\DTOs\WithdrawDto;
use App\Modules\Transaction\Application\DTOs\WithdrawPixDto;
use Hyperf\DbConnection\Db;
use HyperfTest\DatabaseTestCase;
use Carbon\Carbon;

use function Hyperf\Support\make;

class ScheduledWithdrawsTest extends DatabaseTestCase
{
    private WithdrawService $withdrawService;
    private AccountRepositoryInterface $accountRepository;
    private AccountWithdrawRepositoryInterface $withdrawRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withdrawService = make(WithdrawService::class);
        $this->accountRepository = make(AccountRepositoryInterface::class);
        $this->withdrawRepository = make(AccountWithdrawRepositoryInterface::class);
    }

    private function createAccountWithBalance(string $name, float $balance): string
    {
        $account = new \App\Modules\Account\Domain\Entities\Account($name);
        $saved = $this->accountRepository->createAccount($account);
        
        Db::table('accounts')
            ->where('id', $saved->id)
            ->update(['balance' => $balance]);

        return $saved->id;
    }

    public function testCanCreateScheduledWithdraw()
    {
        $accountId = $this->createAccountWithBalance('Test User', 1000.0);

        $scheduledFor = Carbon::now()->addDay()->format('Y-m-d H:i:s');

        $dto = new WithdrawDto(
            accountId: $accountId,
            method: 'PIX',
            amount: 200.0,
            scheduled: true,
            scheduledFor: $scheduledFor,
            pix: new WithdrawPixDto('email', 'test@example.com')
        );

        $withdraw = $this->withdrawService->requestWithdraw($dto);

        $this->assertTrue($withdraw->scheduled);
        $this->assertEquals($scheduledFor, $withdraw->scheduledFor);
        $this->assertFalse($withdraw->done);
        $this->assertFalse($withdraw->error);
    }

    public function testCanFindPendingScheduledWithdraws()
    {
        $accountId = $this->createAccountWithBalance('Test User', 1000.0);

        // Create scheduled withdraw for the past (ready to process)
        $pastDate = Carbon::now()->subMinutes(5)->format('Y-m-d H:i:s');
        $dto1 = new WithdrawDto(
            accountId: $accountId,
            method: 'PIX',
            amount: 100.0,
            scheduled: true,
            scheduledFor: $pastDate,
            pix: new WithdrawPixDto('email', 'test1@example.com')
        );
        $this->withdrawService->requestWithdraw($dto1);

        // Create scheduled withdraw for the future (not ready)
        $futureDate = Carbon::now()->addDay()->format('Y-m-d H:i:s');
        $dto2 = new WithdrawDto(
            accountId: $accountId,
            method: 'PIX',
            amount: 200.0,
            scheduled: true,
            scheduledFor: $futureDate,
            pix: new WithdrawPixDto('email', 'test2@example.com')
        );
        $this->withdrawService->requestWithdraw($dto2);

        // Find pending withdraws
        $pendingWithdraws = $this->withdrawRepository->findPendingScheduledWithdraws();

        // Should find only the one scheduled for the past
        $this->assertGreaterThanOrEqual(1, $pendingWithdraws->count());
        
        // Verify all returned withdraws are ready to process
        foreach ($pendingWithdraws as $withdraw) {
            $this->assertTrue($withdraw->scheduled);
            $this->assertFalse($withdraw->done);
            $this->assertFalse($withdraw->error);
            $this->assertLessThanOrEqual(Carbon::now(), Carbon::parse($withdraw->scheduledFor));
        }
    }

    public function testScheduledWithdrawIsNotProcessedBeforeScheduledTime()
    {
        $accountId = $this->createAccountWithBalance('Test User', 1000.0);

        // Create scheduled withdraw for the future
        $futureDate = Carbon::now()->addHour()->format('Y-m-d H:i:s');
        $dto = new WithdrawDto(
            accountId: $accountId,
            method: 'PIX',
            amount: 100.0,
            scheduled: true,
            scheduledFor: $futureDate,
            pix: new WithdrawPixDto('email', 'test@example.com')
        );

        $withdraw = $this->withdrawService->requestWithdraw($dto);

        // Try to find pending - should not find this one
        $pendingWithdraws = $this->withdrawRepository->findPendingScheduledWithdraws();
        
        $futureWithdrawFound = $pendingWithdraws->first(function ($w) use ($withdraw) {
            return $w->id === $withdraw->id;
        });

        $this->assertNull($futureWithdrawFound);
    }

    public function testProcessedScheduledWithdrawIsNotReturnedAsPending()
    {
        $accountId = $this->createAccountWithBalance('Test User', 1000.0);

        // Create and process a scheduled withdraw
        $pastDate = Carbon::now()->subMinutes(5)->format('Y-m-d H:i:s');
        $dto = new WithdrawDto(
            accountId: $accountId,
            method: 'PIX',
            amount: 100.0,
            scheduled: true,
            scheduledFor: $pastDate,
            pix: new WithdrawPixDto('email', 'test@example.com')
        );

        $withdraw = $this->withdrawService->requestWithdraw($dto);
        
        // Process it
        $this->withdrawService->processWithdraw($withdraw->id);

        // Should not be in pending list anymore
        $pendingWithdraws = $this->withdrawRepository->findPendingScheduledWithdraws();
        
        $processedWithdrawFound = $pendingWithdraws->first(function ($w) use ($withdraw) {
            return $w->id === $withdraw->id;
        });

        $this->assertNull($processedWithdrawFound);
    }
}

