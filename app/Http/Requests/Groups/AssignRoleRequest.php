<?php

namespace App\Http\Requests\Groups;

use Illuminate\Foundation\Http\FormRequest;

class AssignRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        $group = $this->route('group');

        return $this->user()?->can('manageRoles', $group) ?? false;
    }

    public function rules(): array
    {
        $group = $this->route('group');

        return [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'role_id' => ['required', 'integer', "exists:group_roles,id,group_id,{$group->id}"],
        ];
    }
}
