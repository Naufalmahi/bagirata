<?php

namespace App\Http\Resources;

use App\Services\Support\Money;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DebtResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'from' => new UserResource($this->whenLoaded('debtor')),
            'to' => new UserResource($this->whenLoaded('creditor')),
            'amount' => $this->amount,
            'amount_formatted' => Money::format($this->amount),
            'status' => $this->status,
            'status_label' => \App\Enums\DebtStatus::tryFrom($this->status)?->label(),
            'can_settle' => $request->user()?->id === $this->from_user_id && $this->status === 'pending',
            'settled_at' => $this->settled_at?->toIso8601String(),
            'settled_by' => new UserResource($this->whenLoaded('settler')),
        ];
    }
}
