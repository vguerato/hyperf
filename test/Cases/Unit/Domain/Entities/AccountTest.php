<?php

declare(strict_types=1);

namespace HyperfTest\Cases\Unit\Domain\Entities;

use App\Modules\Account\Domain\Entities\Account;
use PHPUnit\Framework\TestCase;

class AccountTest extends TestCase
{
    public function testCanCreateAccount()
    {
        $account = new Account(
            id: 'test-id',
            name: 'Test User',
            balance: 100.50
        );

        $this->assertEquals('test-id', $account->id);
        $this->assertEquals('Test User', $account->name);
        $this->assertEquals(100.50, $account->balance);
    }

    public function testCanCreateAccountWithoutId()
    {
        $account = new Account(
            name: 'Test User',
            balance: 0.0
        );

        $this->assertNull($account->id);
        $this->assertEquals('Test User', $account->name);
        $this->assertEquals(0.0, $account->balance);
    }

    public function testBalanceIsFloatType()
    {
        $account = new Account(
            name: 'Test User',
            balance: 100
        );

        $this->assertIsFloat($account->balance);
        $this->assertEquals(100.0, $account->balance);
    }
}

