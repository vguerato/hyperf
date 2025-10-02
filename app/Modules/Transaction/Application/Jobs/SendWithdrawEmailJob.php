<?php

namespace App\Modules\Transaction\Application\Jobs;

use App\Modules\Transaction\Domain\Entities\AccountWithdraw;
use App\Modules\Shared\Application\Services\EmailService;
use App\Modules\Transaction\Application\Email\WithdrawPixEmailTemplate;
use Hyperf\AsyncQueue\Job;
use Psr\Log\LoggerInterface;

use function Hyperf\Support\make;

class SendWithdrawEmailJob extends Job
{
    public function __construct(
        public AccountWithdraw $withdraw
    ) {}

    public function handle(): void
    {
        $emailService = make(EmailService::class);
        $logger = make(LoggerInterface::class);
        
        try {
            // Create appropriate template based on withdraw method
            $template = $this->createEmailTemplate($this->withdraw);
            
            if (!$template) {
                $logger->warning('Cannot send email: No template available for this withdraw method', [
                    'withdraw_id' => $this->withdraw->id,
                    'method' => $this->withdraw->method,
                ]);
                return;
            }
            
            $emailService->send($template);
            
            $logger->info('Withdraw notification email sent', [
                'withdraw_id' => $this->withdraw->id,
                'recipient' => $template->getRecipient(),
                'method' => $this->withdraw->method,
            ]);
            
        } catch (\Throwable $e) {
            $logger->error('Failed to send withdraw notification email', [
                'withdraw_id' => $this->withdraw->id,
                'error' => $e->getMessage(),
            ]);
            
            // Don't throw - email failure shouldn't affect withdraw status
        }
    }

    /**
     * Factory method to create appropriate email template
     * This makes it easy to add new withdraw methods in the future
     */
    private function createEmailTemplate(AccountWithdraw $withdraw): ?\App\Modules\Shared\Domain\Contracts\EmailTemplateInterface
    {
        return match($withdraw->method) {
            'PIX' => new WithdrawPixEmailTemplate($withdraw),
            // Future: 'TED' => new WithdrawTedEmailTemplate($withdraw),
            // Future: 'DOC' => new WithdrawDocEmailTemplate($withdraw),
            default => null,
        };
    }
}
