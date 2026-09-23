<?php

namespace App\Http\Requests\Nongkrong;

use Illuminate\Foundation\Http\FormRequest;

class StoreSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'date' => ['required', 'date'],
            'description' => ['nullable', 'string', 'max:500'],
            'group_id' => ['nullable', 'integer', 'exists:groups,id'],
            'channel_id' => ['nullable', 'integer', 'exists:channels,id'],
            'member_ids' => ['required', 'array', 'min:1', 'max:100'],
            'member_ids.*' => ['integer', 'distinct', 'exists:users,id'],
        ];
    }
}
