<?php

namespace App\Modules\Transaction\Presentation\Resources;

use Hyperf\Resource\Json\JsonResource;

class PixResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'key' => $this->key,
        ];
    }
}
