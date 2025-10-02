<?php

declare(strict_types=1);

namespace App\Modules\Transaction\Presentation\Requests;

use App\Modules\Transaction\Application\DTOs\WithdrawDto;
use App\Modules\Transaction\Application\DTOs\WithdrawPixDto;
use App\Modules\Transaction\Domain\Enums\TransactionMethod;
use App\Modules\Transaction\Presentation\Validations\WithdrawMethodValidation;
use Hyperf\Validation\Request\FormRequest;

class WithdrawRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'method' => ['required', new WithdrawMethodValidation()],
            'amount' => 'required|numeric|gt:0',
            'schedule' => 'nullable|after_or_equal:tomorrow|before_or_equal:+7 days',
            'pix' => 'required_if:method,PIX|array',
            'pix.type' => 'required_if:method,PIX|string|in:email',
            'pix.key' => 'required_if:method,PIX|string|email',
        ];
    }

    /**
     * @return WithdrawDto
     */
    public function toDto(): WithdrawDto
    {
        $pixDto = null;
        if ($this->input('method') === TransactionMethod::PIX->value) {
            $pixDto = new WithdrawPixDto(
                type: $this->input('pix.type'),
                key: $this->input('pix.key'),
            );
        }

        $schedule = $this->input('schedule');
        $scheduledFor = !empty($schedule) ? date('Y-m-d H:i:s', strtotime($schedule)) : null;
        
        return new WithdrawDto(
            accountId: $this->route('accountId'),
            method: $this->input('method'),
            amount: (float) $this->input('amount'),
            scheduled: !empty($scheduledFor),
            scheduledFor: $scheduledFor,
            pix: $pixDto,
        );
    }
}
