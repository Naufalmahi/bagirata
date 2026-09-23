<?php

namespace App\Http\Requests\Groups;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        $group = $this->route('group');

        return $this->user()?->can('manageRoles', $group) ?? false;
    }

    public function rules(): array
    {
        $group = $this->route('group');
        $role = $this->route('role');

        return [
            'name' => ['required', 'string', 'max:60', Rule::unique('group_roles', 'name')
                ->where('group_id', $group->id)
                ->ignore($role->id)],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', Rule::in(\App\Enums\Permission::all())],
        ];
    }
}
