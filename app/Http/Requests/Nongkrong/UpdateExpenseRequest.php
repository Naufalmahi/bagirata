<?php

namespace App\Http\Requests\Nongkrong;

use App\Models\NongkrongSession;

class UpdateExpenseRequest extends ExpenseRequest
{
    protected function sessionParam(): NongkrongSession
    {
        return $this->route('expense')->session;
    }

    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('expense')) ?? false;
    }

    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'remove_receipt' => ['sometimes', 'boolean'],
        ]);
    }
}
