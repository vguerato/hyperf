<?php

declare(strict_types=1);

namespace HyperfTest\Cases\Feature\Account;

use Hyperf\Testing\TestCase;
use Hyperf\DbConnection\Db;

class AccountControllerTest extends TestCase
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

    public function testCanListAccounts()
    {
        $response = $this->get('/api/v1/accounts');

        $response->assertStatus(200);
        $json = $response->json();
        $this->assertArrayHasKey('data', $json);
        $this->assertIsArray($json['data']);
    }

    public function testCanCreateAccount()
    {
        $response = $this->json('/api/v1/accounts', [
            'name' => 'Test User',
        ]);

        $response->assertStatus(201);
        $json = $response->json();
        $this->assertArrayHasKey('data', $json);
        $data = $json['data'];
        $this->assertEquals('Test User', $data['name']);
        $this->assertEquals(0.0, $data['balance']);
        $this->assertArrayHasKey('id', $data);
    }

    public function testCannotCreateAccountWithoutName()
    {
        $response = $this->json('/api/v1/accounts', []);

        $response->assertStatus(422);
    }
}
