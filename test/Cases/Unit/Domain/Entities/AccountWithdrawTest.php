<?php

declare(strict_types=1);

namespace HyperfTest\Cases\Unit\Domain\Entities;

use App\Modules\Transaction\Domain\Entities\AccountWithdraw;
use App\Modules\Transaction\Domain\ValueObjects\AccountWithdrawPix;
use App\Modules\Transaction\Domain\Enums\WithdrawStatus;
use PHPUnit\Framework\TestCase;

class AccountWithdrawTest extends TestCase
{
    public function testCanCreateImmediateWithdraw()
    {
        $pix = new AccountWithdrawPix(
            type: 'email',
            key: 'test@example.com'
        );

        $withdraw = new AccountWithdraw(
            accountId: 'acc-123',
            method: 'PIX',
            amount: 100.50,
            scheduled: false,
            scheduledFor: null,
            pix: $pix
        );

        $this->assertEquals('acc-123', $withdraw->accountId);
        $this->assertEquals('PIX', $withdraw->method);
        $this->assertEquals(100.50, $withdraw->amount);
        $this->assertFalse($withdraw->scheduled);
        $this->assertNull($withdraw->scheduledFor);
        $this->assertEquals(WithdrawStatus::PENDING->value, $withdraw->status);
        $this->assertFalse($withdraw->done);
        $this->assertFalse($withdraw->error);
        $this->assertNotNull($withdraw->pix);
    }

    public function testCanCreateScheduledWithdraw()
    {
        $scheduledFor = '2026-01-15 14:00:00';

        $withdraw = new AccountWithdraw(
            accountId: 'acc-123',
            method: 'PIX',
            amount: 200.00,
            scheduled: true,
            scheduledFor: $scheduledFor,
            pix: new AccountWithdrawPix('email', 'test@example.com')
        );

        $this->assertTrue($withdraw->scheduled);
        $this->assertEquals($scheduledFor, $withdraw->scheduledFor);
        $this->assertEquals(WithdrawStatus::PENDING->value, $withdraw->status);
        $this->assertFalse($withdraw->done);
    }

    public function testCanMarkAsDone()
    {
        $withdraw = new AccountWithdraw(
            accountId: 'acc-123',
            method: 'PIX',
            amount: 100.00,
            scheduled: false,
            scheduledFor: null,
            pix: new AccountWithdrawPix('email', 'test@example.com')
        );

        $this->assertFalse($withdraw->done);
        $this->assertNull($withdraw->doneAt);
        $this->assertEquals(WithdrawStatus::PENDING->value, $withdraw->status);

        $withdraw->done();

        $this->assertEquals(WithdrawStatus::DONE->value, $withdraw->status);
        $this->assertTrue($withdraw->done);
        $this->assertNotNull($withdraw->doneAt);
        $this->assertFalse($withdraw->error);
    }

    public function testCanMarkAsDenied()
    {
        $withdraw = new AccountWithdraw(
            accountId: 'acc-123',
            method: 'PIX',
            amount: 100.00,
            scheduled: false,
            scheduledFor: null,
            pix: new AccountWithdrawPix('email', 'test@example.com')
        );

        $reason = 'Insufficient funds';
        $withdraw->deny($reason);

        $this->assertEquals(WithdrawStatus::DENIED->value, $withdraw->status);
        $this->assertTrue($withdraw->error);
        $this->assertEquals($reason, $withdraw->errorReason);
        $this->assertNotNull($withdraw->deniedAt);
        $this->assertFalse($withdraw->done);
    }
}

