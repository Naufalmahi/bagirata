<?php

namespace App\Http\Resources;

use App\Services\Support\Money;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'category' => $this->category,
            'category_label' => $this->category()->label(),
            'amount' => $this->amount,
            'amount_formatted' => Money::format($this->amount),
            'discount_type' => $this->discount_type,
            'discount_value' => $this->discount_value,
            'discount_amount' => $this->discountAmount(),
            'service_rate' => $this->service_rate,
            'service_amount' => $this->serviceAmount(),
            'tax_rate' => $this->tax_rate,
            'tax_amount' => $this->taxAmount(),
            'grand_total' => $this->grandTotal(),
            'grand_total_formatted' => Money::format($this->grandTotal()),
            'paid_by' => new UserResource($this->whenLoaded('payer')),
            'note' => $this->note,
            'receipt_photo' => $this->receipt_photo
                ? url('storage/'.$this->receipt_photo)
                : null,
            'splits' => $this->whenLoaded('splits', fn () => $this->splits->map(fn ($split) => [
                'user' => new UserResource($split->user),
                'split_type' => $split->split_type,
                'custom_amount' => $split->custom_amount,
                'percentage' => $split->percentage,
                'share_amount' => $split->share_amount,
                'share_formatted' => Money::format($split->share_amount),
            ])),
            'can_edit' => $request->user() && ($this->session->user_id === $request->user()->id || $this->created_by === $request->user()->id),
            'created_by' => new UserResource($this->whenLoaded('creator')),
            'deleted_at' => $this->deleted_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
