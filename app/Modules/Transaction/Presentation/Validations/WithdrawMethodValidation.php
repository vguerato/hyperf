<?php

namespace App\Modules\Transaction\Presentation\Validations;

use App\Modules\Transaction\Domain\Enums\TransactionMethod;
use Hyperf\Validation\Contract\Rule;

class WithdrawMethodValidation implements Rule
{
    private array $methods;

    public function __construct()
    {
        $this->methods = array_column(TransactionMethod::cases(), 'value');
    }

    public function passes(string $attribute, mixed $value): bool
    {
        return in_array($value, $this->methods);
    }

    public function message(): string
    {
        return 'The method must be one of the following: ' . implode(', ', $this->methods);
    }
}