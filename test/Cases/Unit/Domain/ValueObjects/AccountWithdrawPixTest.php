<?php

declare(strict_types=1);

namespace HyperfTest\Cases\Unit\Domain\ValueObjects;

use App\Modules\Transaction\Domain\ValueObjects\AccountWithdrawPix;
use PHPUnit\Framework\TestCase;

class AccountWithdrawPixTest extends TestCase
{
    public function testCanCreatePixValueObject()
    {
        $pix = new AccountWithdrawPix(
            type: 'email',
            key: 'fulano@email.com'
        );

        $this->assertEquals('email', $pix->type);
        $this->assertEquals('fulano@email.com', $pix->key);
        $this->assertNull($pix->withdrawId);
    }

    public function testCanCreatePixValueObjectWithWithdrawId()
    {
        $pix = new AccountWithdrawPix(
            type: 'email',
            key: 'fulano@email.com',
            withdrawId: 'withdraw-123'
        );

        $this->assertEquals('email', $pix->type);
        $this->assertEquals('fulano@email.com', $pix->key);
        $this->assertEquals('withdraw-123', $pix->withdrawId);
    }
}

