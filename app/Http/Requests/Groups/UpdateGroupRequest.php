<?php

namespace App\Http\Requests\Groups;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        $group = $this->route('group');

        return $this->user()?->can('update', $group) ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:60'],
            'description' => ['nullable', 'string', 'max:500'],
        ];
    }
}
