<?php

namespace App\Http\Requests\Debts;

use App\Enums\PaymentMethod;
use App\Models\Debt;
use Illuminate\Foundation\Http\FormRequest;

class StoreDebtPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Debt $debt */
        $debt = $this->route('debt');

        return $this->user() !== null && $debt->from_user_id === $this->user()->id;
    }

    public function rules(): array
    {
        return [
            'amount' => ['required', 'integer', 'min:1', 'max:999999999999'],
            'method' => ['required', 'string', 'in:'.implode(',', PaymentMethod::casesAsList())],
            'note' => ['nullable', 'string', 'max:500'],
            'proof_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            /** @var Debt $debt */
            $debt = $this->route('debt');

            if ($debt->isSettled() || $debt->outstanding() <= 0) {
                $validator->errors()->add('amount', 'Utang ini udah beres. Nggak ada yang perlu dilaporin lagi.');
            } elseif ((int) $this->input('amount') > $debt->outstanding()) {
                $validator->errors()->add('amount', 'Nominal nggak boleh lebih besar dari sisa utang.');
            }

            if ($debt->hasPendingReport()) {
                $validator->errors()->add('amount', 'Masih ada laporan yang nunggu konfirmasi. Sabar yaa.');
            }
        });
    }
}
