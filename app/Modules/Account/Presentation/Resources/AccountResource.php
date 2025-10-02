<?php

namespace App\Modules\Account\Presentation\Resources;

use Hyperf\Resource\Json\JsonResource;

class AccountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'balance' => $this->balance,
        ];
    }
}
