<?php

declare(strict_types=1);

namespace HyperfTest\Cases\Integration;

use App\Core\Exception\InsufficientFundsException;
use App\Core\Exception\UnauthorizedResourceAccessException;
use App\Modules\Account\Domain\Repositories\AccountRepositoryInterface;
use App\Modules\Transaction\Application\Services\WithdrawService;
use App\Modules\Transaction\Domain\Repositories\AccountWithdrawRepositoryInterface;
use App\Modules\Transaction\Application\DTOs\WithdrawDto;
use App\Modules\Transaction\Application\DTOs\WithdrawPixDto;
use Hyperf\DbConnection\Db;
use HyperfTest\DatabaseTestCase;

use function Hyperf\Support\make;

class WithdrawProcessingTest extends DatabaseTestCase
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

    public function testCanProcessImmediateWithdrawSuccessfully()
    {
        $accountId = $this->createAccountWithBalance('Test User', 500.0);

        // Request withdraw
        $dto = new WithdrawDto(
            accountId: $accountId,
            method: 'PIX',
            amount: 100.0,
            scheduled: false,
            scheduledFor: null,
            pix: new WithdrawPixDto('email', 'test@example.com')
        );

        $withdraw = $this->withdrawService->requestWithdraw($dto);

        $this->assertFalse($withdraw->done);
        $this->assertFalse($withdraw->error);

        // Process withdraw
        $this->withdrawService->processWithdraw($withdraw->id);

        // Verify withdraw was processed
        $processedWithdraw = $this->withdrawRepository->findById($withdraw->id);
        $this->assertTrue($processedWithdraw->done);
        $this->assertFalse($processedWithdraw->error);
        $this->assertNotNull($processedWithdraw->doneAt);

        // Verify balance was deducted
        $account = $this->accountRepository->findAccountById($accountId);
        $this->assertEquals(400.0, $account->balance);
    }

    public function testWithdrawFailsWithInsufficientFunds()
    {
        $accountId = $this->createAccountWithBalance('Poor User', 50.0);

        // Request withdraw for more than balance
        $dto = new WithdrawDto(
            accountId: $accountId,
            method: 'PIX',
            amount: 100.0,
            scheduled: false,
            scheduledFor: null,
            pix: new WithdrawPixDto('email', 'test@example.com')
        );

        $withdraw = $this->withdrawService->requestWithdraw($dto);

        // Process withdraw - should fail
        $this->withdrawService->processWithdraw($withdraw->id);

        // Verify withdraw failed
        $processedWithdraw = $this->withdrawRepository->findById($withdraw->id);
        $this->assertFalse($processedWithdraw->done);
        $this->assertTrue($processedWithdraw->error);
        $this->assertNotNull($processedWithdraw->errorReason);
        $this->assertNotNull($processedWithdraw->deniedAt);

        // Verify balance was NOT deducted
        $account = $this->accountRepository->findAccountById($accountId);
        $this->assertEquals(50.0, $account->balance);
    }

    public function testDeductBalanceThrowsExceptionForNonExistentAccount()
    {
        $this->expectException(UnauthorizedResourceAccessException::class);

        $this->accountRepository->deductBalance('non-existent-id', 100.0);
    }

    public function testDeductBalanceThrowsExceptionForInsufficientFunds()
    {
        $accountId = $this->createAccountWithBalance('Test User', 50.0);

        $this->expectException(InsufficientFundsException::class);

        $this->accountRepository->deductBalance($accountId, 100.0);
    }

    public function testCanProcessMultipleWithdrawsForSameAccount()
    {
        $accountId = $this->createAccountWithBalance('Test User', 1000.0);

        // Create and process first withdraw
        $dto1 = new WithdrawDto(
            accountId: $accountId,
            method: 'PIX',
            amount: 100.0,
            scheduled: false,
            scheduledFor: null,
            pix: new WithdrawPixDto('email', 'test1@example.com')
        );

        $withdraw1 = $this->withdrawService->requestWithdraw($dto1);
        $this->withdrawService->processWithdraw($withdraw1->id);

        // Verify first withdraw
        $account = $this->accountRepository->findAccountById($accountId);
        $this->assertEquals(900.0, $account->balance);

        // Create and process second withdraw
        $dto2 = new WithdrawDto(
            accountId: $accountId,
            method: 'PIX',
            amount: 200.0,
            scheduled: false,
            scheduledFor: null,
            pix: new WithdrawPixDto('email', 'test2@example.com')
        );

        $withdraw2 = $this->withdrawService->requestWithdraw($dto2);
        $this->withdrawService->processWithdraw($withdraw2->id);

        // Verify final balance
        $account = $this->accountRepository->findAccountById($accountId);
        $this->assertEquals(700.0, $account->balance);
    }

    public function testAlreadyProcessedWithdrawIsSkipped()
    {
        $accountId = $this->createAccountWithBalance('Test User', 500.0);

        $dto = new WithdrawDto(
            accountId: $accountId,
            method: 'PIX',
            amount: 100.0,
            scheduled: false,
            scheduledFor: null,
            pix: new WithdrawPixDto('email', 'test@example.com')
        );

        $withdraw = $this->withdrawService->requestWithdraw($dto);

        // Process withdraw first time
        $this->withdrawService->processWithdraw($withdraw->id);

        $account = $this->accountRepository->findAccountById($accountId);
        $this->assertEquals(400.0, $account->balance);

        // Try to process again - should be skipped
        $this->withdrawService->processWithdraw($withdraw->id);

        // Balance should remain the same
        $account = $this->accountRepository->findAccountById($accountId);
        $this->assertEquals(400.0, $account->balance);
    }
}

