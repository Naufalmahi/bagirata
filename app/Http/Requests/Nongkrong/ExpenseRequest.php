<?php

namespace App\Http\Requests\Nongkrong;

use App\Enums\ExpenseCategory;
use App\Enums\SplitType;
use App\Models\NongkrongSession;
use App\Services\CalculationService;
use Illuminate\Foundation\Http\FormRequest;

abstract class ExpenseRequest extends FormRequest
{
    abstract protected function sessionParam(): NongkrongSession;

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'amount' => ['required', 'integer', 'min:1', 'max:999999999999'],
            'paid_by_user_id' => ['required', 'integer', 'exists:users,id'],
            'category' => ['required', 'string', 'in:'.implode(',', ExpenseCategory::casesAsList())],
            'note' => ['nullable', 'string', 'max:500'],
            'receipt_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'discount_type' => ['required', 'string', 'in:fixed,percent'],
            'discount_value' => ['required', 'integer', 'min:0', 'max:999999999999'],
            'service_rate' => ['nullable', 'integer', 'min:0', 'max:100'],
            'tax_rate' => ['nullable', 'integer', 'min:0', 'max:100'],
            'split_type' => ['required', 'string', 'in:'.implode(',', SplitType::casesAsList())],
            'participant_ids' => ['required', 'array', 'min:2', 'max:100'],
            'participant_ids.*' => ['integer', 'distinct', 'exists:users,id'],
            'custom_amounts' => ['required_if:split_type,custom', 'array'],
            'custom_amounts.*' => ['integer', 'min:0', 'max:999999999999'],
            'percentages' => ['required_if:split_type,percentage', 'array'],
            'percentages.*' => ['integer', 'min:0', 'max:100'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $session = $this->sessionParam();
            $input = $this->validated();

            $memberIds = $session->members()->pluck('users.id')->all();

            if (! in_array((int) $input['paid_by_user_id'], $memberIds, true)) {
                $validator->errors()->add('paid_by_user_id', 'Yang bayar harus anggota session ini.');
            }

            $participants = $input['participant_ids'] ?? [];
            $foreign = array_diff($participants, $memberIds);
            if ($foreign) {
                $validator->errors()->add('participant_ids', 'Ada peserta yang bukan anggota session ini.');
            }

            if ($input['discount_type'] === 'percent' && (int) $input['discount_value'] > 100) {
                $validator->errors()->add('discount_value', 'Discount persen maksimal 100.');
            }

            if ($input['discount_type'] === 'fixed' && (int) $input['discount_value'] >= (int) $input['amount']) {
                $validator->errors()->add('discount_value', 'Discount nggak boleh lebih besar dari nominalnya.');
            }

            if ($input['split_type'] === 'percentage' && array_sum($input['percentages'] ?? []) !== 100) {
                $validator->errors()->add('percentages', 'Total persen harus pas 100.');
            }

            if ($input['split_type'] === 'custom') {
                $grandTotal = CalculationService::grandTotal(
                    (int) $input['amount'],
                    $input['discount_type'],
                    (int) $input['discount_value'],
                    (int) ($input['service_rate'] ?? 0),
                    (int) ($input['tax_rate'] ?? 0)
                );
                if ((array_sum($input['custom_amounts'] ?? [])) !== $grandTotal) {
                    $validator->errors()->add('custom_amounts', 'Total nominal harus pas sama grand total (Rp'.number_format($grandTotal, 0, ',', '.').').');
                }
            }
        });
    }
}
