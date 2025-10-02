<?php

declare(strict_types=1);

namespace App\Modules\Shared\Application\Services;

use App\Modules\Shared\Domain\Contracts\EmailTemplateInterface;
use Psr\Log\LoggerInterface;

class EmailService
{
    public function __construct(
        private LoggerInterface $logger
    ) {}

    /**
     * Send email using a template
     */
    public function send(EmailTemplateInterface $template): void
    {
        $recipient = $template->getRecipient();
        $subject = $template->getSubject();
        $body = $this->renderTemplate($template);

        $this->sendEmail($recipient, $subject, $body);
    }

    /**
     * Render template by replacing placeholders
     */
    private function renderTemplate(EmailTemplateInterface $template): string
    {
        $viewPath = $template->getViewPath();
        $data = $template->getData();
        
        $fullPath = BASE_PATH . '/app/Modules/Shared/Infrastructure/Views/' . $viewPath . '.html';
        
        if (!file_exists($fullPath)) {
            throw new \RuntimeException("Email template not found: {$fullPath}");
        }
        
        $contents = file_get_contents($fullPath);
        
        if ($contents === false) {
            throw new \RuntimeException("Failed to read email template: {$fullPath}");
        }
        
        foreach ($data as $key => $value) {
            $placeholder = '{{' . $key . '}}';
            $contents = str_replace($placeholder, $value, $contents);
        }
        
        return $contents;
    }

    /**
     * Send email via SMTP
     */
    private function sendEmail(string $to, string $subject, string $body): void
    {
        try {
            $mailer = $this->getMailer();
            
            $from = \Hyperf\Config\config('mail.from.address', 'noreply@tecnofit.com');
            
            $email = (new \Symfony\Component\Mime\Email())
                ->from($from)
                ->to($to)
                ->subject($subject)
                ->html($body);

            $mailer->send($email);
            
            $this->logger->info('Email sent successfully', [
                'to' => $to,
                'subject' => $subject,
            ]);
            
        } catch (\Throwable $e) {
            $this->logger->error('Failed to send email', [
                'to' => $to,
                'subject' => $subject,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Get configured mailer instance
     */
    private function getMailer(): \Symfony\Component\Mailer\MailerInterface
    {
        $config = \Hyperf\Config\config('mail.mailers.smtp');
        
        $dsn = sprintf(
            'smtp://%s:%s@%s:%d',
            $config['username'] ?? '',
            $config['password'] ?? '',
            $config['host'],
            $config['port']
        );
        
        $transport = \Symfony\Component\Mailer\Transport::fromDsn($dsn);
        
        return new \Symfony\Component\Mailer\Mailer($transport);
    }
}

