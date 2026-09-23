<?php

namespace App\Http\Requests\Groups;

use Illuminate\Foundation\Http\FormRequest;

class StoreInviteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('invite', $this->route('group'));
    }

    public function rules(): array
    {
        return [
            'expires_days' => ['nullable', 'integer', 'min:1', 'max:365'],
            'max_uses' => ['nullable', 'integer', 'min:1', 'max:100000'],
        ];
    }
}
