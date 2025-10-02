<?php

declare(strict_types=1);

namespace HyperfTest\Cases\Feature\Transaction;

use Hyperf\Testing\TestCase;
use Hyperf\DbConnection\Db;

class WithdrawControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Db::beginTransaction();
    }

    protected function tearDown(): void
    {
        Db::rollBack();
        parent::tearDown();
    }

    private function createAccountWithBalance(float $balance = 1000.0): string
    {
        $response = $this->json('/api/v1/accounts', [
            'name' => 'Test User',
        ]);

        $json = $response->json();
        $accountId = $json['data']['id'];

        // Update balance directly in database
        Db::table('accounts')
            ->where('id', $accountId)
            ->update(['balance' => $balance]);

        return $accountId;
    }

    public function testCanListWithdraws()
    {
        $accountId = $this->createAccountWithBalance();

        $response = $this->get("/account/{$accountId}/withdraw");

        $response->assertStatus(200);
        $json = $response->json();
        $this->assertArrayHasKey('data', $json);
        $this->assertIsArray($json['data']);
    }

    public function testCanCreateImmediateWithdraw()
    {
        $accountId = $this->createAccountWithBalance(500.0);

        $response = $this->json("/account/{$accountId}/withdraw", [
            'method' => 'PIX',
            'amount' => 100.50,
            'pix' => [
                'type' => 'email',
                'key' => 'test@example.com',
            ],
        ]);

        $response->assertStatus(201);
        $json = $response->json();
        $this->assertArrayHasKey('data', $json);
        $data = $json['data'];
        $this->assertEquals('PIX', $data['method']);
        $this->assertEquals(100.50, $data['amount']);
        $this->assertFalse($data['scheduled']);
        $this->assertEquals('pending', $data['status']);
        $this->assertArrayHasKey('pix', $data);
        $this->assertEquals('email', $data['pix']['type']);
        $this->assertEquals('test@example.com', $data['pix']['key']);
    }

    public function testCanCreateScheduledWithdraw()
    {
        $accountId = $this->createAccountWithBalance(500.0);

        $response = $this->json("/account/{$accountId}/withdraw", [
            'method' => 'PIX',
            'amount' => 150.00,
            'schedule' => date('Ymd Hi', strtotime('+1 day')),
            'pix' => [
                'type' => 'email',
                'key' => 'scheduled@example.com',
            ],
        ]);

        $response->assertStatus(201);
        $json = $response->json();
        $this->assertArrayHasKey('data', $json);
        $data = $json['data'];
        $this->assertTrue($data['scheduled']);
        $this->assertNotNull($data['scheduled_for']);
        $this->assertEquals('pending', $data['status']);
        $this->assertFalse($data['done']);
    }

    public function testCannotCreateWithdrawWithoutMethod()
    {
        $accountId = $this->createAccountWithBalance();

        $response = $this->json("/account/{$accountId}/withdraw", [
            'amount' => 100.00,
        ]);

        $response->assertStatus(422);
    }

    public function testCannotCreateWithdrawWithNegativeAmount()
    {
        $accountId = $this->createAccountWithBalance();

        $response = $this->json("/account/{$accountId}/withdraw", [
            'method' => 'PIX',
            'amount' => -50.00,
            'pix' => [
                'type' => 'email',
                'key' => 'test@example.com',
            ],
        ]);

        $response->assertStatus(422);
    }

    public function testCannotCreateWithdrawWithZeroAmount()
    {
        $accountId = $this->createAccountWithBalance();

        $response = $this->json("/account/{$accountId}/withdraw", [
            'method' => 'PIX',
            'amount' => 0,
            'pix' => [
                'type' => 'email',
                'key' => 'test@example.com',
            ],
        ]);

        $response->assertStatus(422);
    }

    public function testCannotCreateWithdrawWithoutPixData()
    {
        $accountId = $this->createAccountWithBalance();

        $response = $this->json("/account/{$accountId}/withdraw", [
            'method' => 'PIX',
            'amount' => 100.00,
        ]);

        $response->assertStatus(422);
    }

    public function testCannotCreateWithdrawWithInvalidPixType()
    {
        $accountId = $this->createAccountWithBalance();

        $response = $this->json("/account/{$accountId}/withdraw", [
            'method' => 'PIX',
            'amount' => 100.00,
            'pix' => [
                'type' => 'phone',
                'key' => '11999999999',
            ],
        ]);

        $response->assertStatus(422);
    }

    public function testCannotCreateWithdrawWithInvalidEmail()
    {
        $accountId = $this->createAccountWithBalance();

        $response = $this->json("/account/{$accountId}/withdraw", [
            'method' => 'PIX',
            'amount' => 100.00,
            'pix' => [
                'type' => 'email',
                'key' => 'invalid-email',
            ],
        ]);

        $response->assertStatus(422);
    }
}

