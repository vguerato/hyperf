<?php

declare(strict_types=1);

namespace HyperfTest\Cases\Unit\Application\DTOs;

use App\Modules\Transaction\Application\DTOs\WithdrawDto;
use App\Modules\Transaction\Application\DTOs\WithdrawPixDto;
use PHPUnit\Framework\TestCase;

class WithdrawDtoTest extends TestCase
{
    public function testCanCreateWithdrawDto()
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
    }

    public function testCanCreateScheduledWithdrawDto()
    {
        $dto = new WithdrawDto(
            accountId: 'acc-123',
            method: 'PIX',
            amount: 100.00,
            scheduled: true,
            scheduledFor: '2026-01-15 14:00:00',
            pix: new WithdrawPixDto('email', 'test@example.com')
        );

        $this->assertTrue($dto->scheduled);
        $this->assertEquals('2026-01-15 14:00:00', $dto->scheduledFor);
    }
}

