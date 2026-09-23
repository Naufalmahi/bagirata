<?php

namespace App\Http\Resources;

use App\Enums\PaymentMethod;
use App\Enums\PaymentReportStatus;
use App\Services\Support\Money;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DebtPaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $status = PaymentReportStatus::tryFrom($this->status) ?? PaymentReportStatus::PENDING;

        return [
            'id' => $this->id,
            'debt_id' => $this->debt_id,
            'amount' => $this->amount,
            'amount_formatted' => Money::format($this->amount),
            'method' => $this->method,
            'method_label' => PaymentMethod::tryFrom($this->method)?->label(),
            'note' => $this->note,
            'proof_photo' => $this->proof_photo ? url('storage/'.$this->proof_photo) : null,
            'status' => $this->status,
            'status_label' => $status->label(),
            'review_note' => $this->review_note,
            'reported_by' => new UserResource($this->whenLoaded('reporter')),
            'reviewed_by' => new UserResource($this->whenLoaded('reviewer')),
            'reviewed_at' => $this->reviewed_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
