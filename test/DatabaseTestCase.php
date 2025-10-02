<?php

declare(strict_types=1);

namespace HyperfTest;

use Hyperf\DbConnection\Db;
use PHPUnit\Framework\TestCase;

abstract class DatabaseTestCase extends TestCase
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

    protected function refreshDatabase(): void
    {
        Db::rollBack();
        Db::beginTransaction();
    }
}

