<?php

namespace App\Http\Requests\Groups;

use Illuminate\Foundation\Http\FormRequest;

class StoreTreasuryEntryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => 'required|in:in,out',
            'category' => 'required|string|max:50',
            'amount' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'proof_photo' => 'nullable|image|max:2048',
        ];
    }
}
