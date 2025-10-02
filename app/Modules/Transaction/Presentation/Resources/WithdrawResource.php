<?php

namespace App\Modules\Transaction\Presentation\Resources;

use App\Modules\Transaction\Domain\Enums\TransactionMethod;
use Hyperf\Resource\Json\JsonResource;

class WithdrawResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        $resource = [
            'id' => $this->id,
            'method' => $this->method,
            'amount' => $this->amount,
            'scheduled' => $this->scheduled,
            'scheduled_for' => $this->scheduledFor,
            'status' => $this->status,
            'done' => $this->done,
            'error' => $this->error,
            'error_reason' => $this->errorReason,
            'done_at' => $this->doneAt,
            'denied_at' => $this->deniedAt,
        ];

        if ($this->method === TransactionMethod::PIX->value && $this->pix) {
            $resource['pix'] = new PixResource($this->pix);
        }

        return $resource;
    }
}
