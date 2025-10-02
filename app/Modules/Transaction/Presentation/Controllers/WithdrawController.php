<?php

declare(strict_types=1);

namespace App\Modules\Transaction\Presentation\Controllers;

use App\Core\Controller\AbstractController;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\Controller;
use App\Modules\Transaction\Application\Services\WithdrawService;
use App\Modules\Transaction\Application\Jobs\ProcessWithdrawJob;
use App\Modules\Transaction\Presentation\Requests\WithdrawRequest;
use App\Modules\Transaction\Presentation\Resources\WithdrawResource;
use Hyperf\AsyncQueue\Driver\DriverFactory;
use Psr\Log\LoggerInterface;

#[Controller(prefix: '/account/{accountId}')]
class WithdrawController extends AbstractController
{
    public function __construct(
        private WithdrawService $withdrawService,
        private DriverFactory $driverFactory,
        private LoggerInterface $logger
    ) {}

    #[GetMapping(path: 'withdraw')]
    public function index(string $accountId)
    {
        $withdraws = $this->withdrawService->listWithdraws($accountId);
        
        return WithdrawResource::collection($withdraws)->toResponse();
    }

    #[PostMapping(path: 'withdraw')]
    public function store(WithdrawRequest $request, string $accountId)
    {
        try {
            $dto = $request->toDto();
            
            // Create withdraw record
            $withdraw = $this->withdrawService->requestWithdraw($dto);
            
            $this->logger->info('Withdraw requested', [
                'withdraw_id' => $withdraw->id,
                'account_id' => $accountId,
                'amount' => $withdraw->amount,
                'scheduled' => $withdraw->scheduled,
            ]);
            
            // If not scheduled, dispatch immediate processing
            if (!$withdraw->scheduled) {
                $driver = $this->driverFactory->get('default');
                $driver->push(new ProcessWithdrawJob($withdraw->id), 0);
                
                $this->logger->info('Immediate withdraw job dispatched', [
                    'withdraw_id' => $withdraw->id,
                ]);
            }
            
            return (new WithdrawResource($withdraw))
                ->toResponse()
                ->withStatus(201);
                
        } catch (\Throwable $e) {
            $this->logger->error('Failed to create withdraw', [
                'account_id' => $accountId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }    
}