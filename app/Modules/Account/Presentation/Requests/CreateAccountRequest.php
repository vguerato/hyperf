<?php

declare(strict_types=1);

namespace App\Modules\Account\Presentation\Requests;

use App\Modules\Account\Application\DTOs\AccountDataDTO;
use Hyperf\Validation\Request\FormRequest;

class CreateAccountRequest extends FormRequest
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
            'name' => 'required|string|max:255',
        ];
    }

    /**
     * @return AccountDataDTO
     */
    public function toDto(): AccountDataDTO
    {
        return new AccountDataDTO(
            name: $this->input('name'),
        );
    }
}
