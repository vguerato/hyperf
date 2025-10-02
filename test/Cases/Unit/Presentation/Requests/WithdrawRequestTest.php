<?php

declare(strict_types=1);

namespace HyperfTest\Cases\Unit\Presentation\Requests;

use App\Modules\Transaction\Application\DTOs\WithdrawDto;
use App\Modules\Transaction\Application\DTOs\WithdrawPixDto;
use PHPUnit\Framework\TestCase;

class WithdrawRequestTest extends TestCase
{
    public function testToDtoCreatesCorrectDtoForImmediateWithdraw()
    {
        $pixDto = new WithdrawPixDto(
            type: 'email',
            key: 'test@example.com'
        );

        $dto = new WithdrawDto(
            accountId: 'acc-123',
            method: 'PIX',
            amount: 150.75,
            scheduled: false,
            scheduledFor: null,
            pix: $pixDto
        );

        $this->assertEquals('acc-123', $dto->accountId);
        $this->assertEquals('PIX', $dto->method);
        $this->assertEquals(150.75, $dto->amount);
        $this->assertFalse($dto->scheduled);
        $this->assertNull($dto->scheduledFor);
        $this->assertInstanceOf(WithdrawPixDto::class, $dto->pix);
        $this->assertEquals('email', $dto->pix->type);
        $this->assertEquals('test@example.com', $dto->pix->key);
    }

    public function testToDtoCreatesCorrectDtoForScheduledWithdraw()
    {
        $pixDto = new WithdrawPixDto(
            type: 'email',
            key: 'scheduled@example.com'
        );

        $dto = new WithdrawDto(
            accountId: 'acc-456',
            method: 'PIX',
            amount: 200.00,
            scheduled: true,
            scheduledFor: '2026-01-15 14:00:00',
            pix: $pixDto
        );

        $this->assertTrue($dto->scheduled);
        $this->assertNotNull($dto->scheduledFor);
        $this->assertEquals('2026-01-15 14:00:00', $dto->scheduledFor);
    }
}

