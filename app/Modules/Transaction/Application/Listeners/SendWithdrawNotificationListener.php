<?php

namespace App\Modules\Transaction\Application\Listeners;

use App\Modules\Transaction\Domain\Events\WithdrawProcessed;
use App\Modules\Transaction\Domain\Events\WithdrawFailed;
use App\Modules\Transaction\Application\Jobs\SendWithdrawEmailJob;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Event\Annotation\Listener;
use Hyperf\AsyncQueue\Driver\DriverFactory;
use Hyperf\Context\ApplicationContext;

#[Listener]
class SendWithdrawNotificationListener implements ListenerInterface
{
    public function listen(): array
    {
        return [
            WithdrawProcessed::class,
            WithdrawFailed::class,
        ];
    }

    public function process(object $event): void
    {
        if (!$event instanceof WithdrawProcessed && !$event instanceof WithdrawFailed) {
            return;
        }

        $withdraw = $event->withdraw;
        if ($withdraw->pix?->type !== 'email') {
            return;
        }

        $driverFactory = ApplicationContext::getContainer()->get(DriverFactory::class);
        $driver = $driverFactory->get('email');
        $driver->push(new SendWithdrawEmailJob($withdraw), 0);
    }
}


