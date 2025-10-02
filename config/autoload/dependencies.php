<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
return [
    App\Modules\Account\Domain\Repositories\AccountRepositoryInterface::class => App\Modules\Account\Infrastructure\Repositories\AccountRepository::class,
    App\Modules\Transaction\Domain\Repositories\AccountWithdrawRepositoryInterface::class => App\Modules\Transaction\Infrastructure\Repositories\AccountWithdrawRepository::class,
];
