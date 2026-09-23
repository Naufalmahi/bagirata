<?php

namespace App\Http\Resources;

use App\Enums\DebtStatus;
use App\Services\Support\Money;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DebtResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $status = \App\Enums\DebtStatus::tryFrom($this->status) ?? \App\Enums\DebtStatus::PENDING;

        return [
            'id' => $this->id,
            'session_id' => $this->nongkrong_session_id,
            'source' => $this->whenLoaded('session', fn () => $this->session->name),
            'from' => new UserResource($this->whenLoaded('debtor')),
            'to' => new UserResource($this->whenLoaded('creditor')),
            'amount' => $this->amount,
            'amount_formatted' => Money::format($this->amount),
            'paid_amount' => $this->paid_amount,
            'paid_amount_formatted' => Money::format($this->paid_amount),
            'remaining_amount' => $this->outstanding(),
            'remaining_formatted' => Money::format($this->outstanding()),
            'status' => $this->status,
            'status_label' => $status->label(),
            'can_report' => $request->user() ? $this->canDebtorReport($request->user()) : false,
            'can_review' => $request->user() ? $this->canCreditorReview($request->user()) : false,
            'has_pending_report' => $this->hasPendingReport(),
            'payments' => DebtPaymentResource::collection($this->whenLoaded('payments')),
            'settled_at' => $this->settled_at?->toIso8601String(),
            'settled_by' => new UserResource($this->whenLoaded('settler')),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
