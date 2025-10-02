<?php

declare(strict_types=1);

namespace App\Modules\Shared\Domain\Contracts;

interface EmailTemplateInterface
{
    /**
     * Get the email subject
     */
    public function getSubject(): string;

    /**
     * Get the email recipient
     */
    public function getRecipient(): string;

    /**
     * Get the template view path
     */
    public function getViewPath(): string;

    /**
     * Get the data for the template
     */
    public function getData(): array;
}

