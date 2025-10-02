<?php

declare(strict_types=1);

namespace App\Modules\Transaction\Application\Email;

use App\Modules\Shared\Domain\Contracts\EmailTemplateInterface;
use App\Modules\Transaction\Domain\Entities\AccountWithdraw;

class WithdrawPixEmailTemplate implements EmailTemplateInterface
{
    public function __construct(
        private AccountWithdraw $withdraw
    ) {
        if (!$this->withdraw->pix) {
            throw new \InvalidArgumentException('PIX data is required for PIX email template.');
        }
    }

    public function getSubject(): string
    {
        return $this->withdraw->done 
            ? 'Saque PIX Realizado com Sucesso' 
            : 'Saque PIX - Falha no Processamento';
    }

    public function getRecipient(): string
    {
        return $this->withdraw->pix->key;
    }

    public function getViewPath(): string
    {
        return 'emails/withdraw-pix';
    }

    public function getData(): array
    {
        $isSuccess = $this->withdraw->done;
        
        return [
            'subject' => $this->getSubject(),
            'status' => $isSuccess ? 'realizado com sucesso' : 'não pôde ser processado',
            'dateTime' => $isSuccess ? $this->withdraw->doneAt : $this->withdraw->deniedAt,
            'amount' => number_format($this->withdraw->amount, 2, ',', '.'),
            'pixType' => ucfirst($this->withdraw->pix->type),
            'pixKey' => htmlspecialchars($this->withdraw->pix->key),
            'year' => date('Y'),
            'headerColor' => $isSuccess ? '#4CAF50' : '#f44336',
            'statusBadgeBackground' => $isSuccess ? '#e8f5e9' : '#ffebee',
            'statusBadgeColor' => $isSuccess ? '#2e7d32' : '#c62828',
            'errorSection' => $this->buildErrorSection(),
            'messageTitle' => $this->getMessageTitle(),
            'messageBody' => $this->getMessageBody(),
        ];
    }
    
    private function buildErrorSection(): string
    {
        if (!$this->withdraw->error || !$this->withdraw->errorReason) {
            return '';
        }
        
        return sprintf(
            '<p><strong>⚠️ Motivo da Falha:</strong><br>%s</p>',
            htmlspecialchars($this->withdraw->errorReason)
        );
    }
    
    private function getMessageTitle(): string
    {
        return $this->withdraw->done 
            ? 'O valor foi transferido para sua chave PIX.'
            : 'Infelizmente não foi possível processar seu saque.';
    }
    
    private function getMessageBody(): string
    {
        return $this->withdraw->done
            ? 'O processamento pode levar alguns minutos para aparecer em sua conta.'
            : 'Por favor, verifique o motivo acima e tente novamente.';
    }
}

