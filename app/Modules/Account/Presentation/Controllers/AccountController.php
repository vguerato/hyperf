<?php

declare(strict_types=1);

namespace App\Modules\Account\Presentation\Controllers;

use App\Core\Controller\AbstractController;
use App\Modules\Account\Application\Services\AccountService;
use App\Modules\Account\Presentation\Requests\CreateAccountRequest;
use App\Modules\Account\Presentation\Resources\AccountResource;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\Controller;

#[Controller(prefix: 'api/v1/accounts')]
class AccountController extends AbstractController
{
    public function __construct(
        private AccountService $accountService,
    ) {}

    #[GetMapping(path: '')]
    public function index()
    {
        $accounts = $this->accountService->listAccounts();

        return AccountResource::collection($accounts)->toResponse();
    }

    #[PostMapping(path: '')]
    public function store(CreateAccountRequest $request)
    {
        $account = $this->accountService->createAccount($request->toDto());
        
        return (new AccountResource($account))
            ->toResponse()
            ->withStatus(201);
    }    
}