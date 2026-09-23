<?php

namespace App\Http\Requests\Debts;

use App\Enums\PaymentReportStatus;
use App\Models\Debt;
use App\Models\DebtPayment;
use Illuminate\Foundation\Http\FormRequest;

class ReviewDebtPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Debt $debt */
        $debt = $this->route('debt');
        $user = $this->user();

        return $user !== null
            && $debt->to_user_id === $user->id
            && $debt->from_user_id !== $user->id;
    }

    public function rules(): array
    {
        return [
            'decision' => ['required', 'string', 'in:'.PaymentReportStatus::CONFIRMED->value.','.PaymentReportStatus::REJECTED->value],
            'review_note' => ['nullable', 'string', 'max:500', 'required_if:decision,rejected'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            /** @var DebtPayment $payment */
            $payment = $this->route('payment');

            if (! $payment->isPending()) {
                $validator->errors()->add('decision', 'Laporan ini udah diproses, nggak bisa diubah lagi.');
            }
        });
    }
}
