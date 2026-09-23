<?php

namespace App\Http\Requests\Groups;

use Illuminate\Foundation\Http\FormRequest;

class ManageInviteRequest extends FormRequest
{
    public function authorize(): bool
    {
        $group = $this->route('group');

        return $this->user()?->can('invite', $group) ?? false;
    }

    public function rules(): array
    {
        return [];
    }
}
