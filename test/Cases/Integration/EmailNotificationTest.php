<?php

declare(strict_types=1);

namespace HyperfTest\Cases\Integration;

use App\Modules\Transaction\Application\Email\WithdrawPixEmailTemplate;
use App\Modules\Transaction\Domain\Entities\AccountWithdraw;
use App\Modules\Transaction\Domain\ValueObjects\AccountWithdrawPix;
use App\Modules\Shared\Application\Services\EmailService;
use PHPUnit\Framework\TestCase;

use function Hyperf\Support\make;

class EmailNotificationTest extends TestCase
{
    private EmailService $emailService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->emailService = make(EmailService::class);
    }

    public function testCanCreateSuccessEmailTemplate()
    {
        $pix = new AccountWithdrawPix(
            type: 'email',
            key: 'test@example.com'
        );

        $withdraw = new AccountWithdraw(
            id: 'withdraw-123',
            accountId: 'acc-123',
            method: 'PIX',
            amount: 150.75,
            scheduled: false,
            scheduledFor: null,
            pix: $pix
        );

        $withdraw->done();

        $template = new WithdrawPixEmailTemplate($withdraw);

        $this->assertEquals('Saque PIX Realizado com Sucesso', $template->getSubject());
        $this->assertEquals('test@example.com', $template->getRecipient());
        $this->assertEquals('emails/withdraw-pix', $template->getViewPath());
        
        $data = $template->getData();
        $this->assertArrayHasKey('subject', $data);
        $this->assertArrayHasKey('amount', $data);
        $this->assertArrayHasKey('pixKey', $data);
        $this->assertArrayHasKey('status', $data);
        $this->assertEquals('150,75', $data['amount']);
        $this->assertStringContainsString('realizado com sucesso', $data['status']);
    }

    public function testCanCreateFailureEmailTemplate()
    {
        $pix = new AccountWithdrawPix(
            type: 'email',
            key: 'test@example.com'
        );

        $withdraw = new AccountWithdraw(
            id: 'withdraw-123',
            accountId: 'acc-123',
            method: 'PIX',
            amount: 100.00,
            scheduled: false,
            scheduledFor: null,
            pix: $pix
        );

        $withdraw->deny('Insufficient funds');

        $template = new WithdrawPixEmailTemplate($withdraw);

        $this->assertEquals('Saque PIX - Falha no Processamento', $template->getSubject());
        $this->assertEquals('test@example.com', $template->getRecipient());
        
        $data = $template->getData();
        $this->assertStringContainsString('não pôde ser processado', $data['status']);
        $this->assertNotEmpty($data['errorSection']);
        $this->assertStringContainsString('Insufficient funds', $data['errorSection']);
    }

    public function testEmailTemplateEscapesHtmlInPixKey()
    {
        $pix = new AccountWithdrawPix(
            type: 'email',
            key: '<script>alert("xss")</script>@example.com'
        );

        $withdraw = new AccountWithdraw(
            id: 'withdraw-123',
            accountId: 'acc-123',
            method: 'PIX',
            amount: 100.00,
            scheduled: false,
            scheduledFor: null,
            pix: $pix
        );

        $withdraw->done();

        $template = new WithdrawPixEmailTemplate($withdraw);
        $data = $template->getData();

        // Should be escaped
        $this->assertStringContainsString('&lt;script&gt;', $data['pixKey']);
        $this->assertStringNotContainsString('<script>', $data['pixKey']);
    }
}

