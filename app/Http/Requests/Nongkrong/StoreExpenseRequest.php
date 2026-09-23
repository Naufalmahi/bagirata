<?php

namespace App\Http\Requests\Nongkrong;

use App\Models\NongkrongSession;

class StoreExpenseRequest extends ExpenseRequest
{
    protected function sessionParam(): NongkrongSession
    {
        return $this->route('session');
    }

    public function authorize(): bool
    {
        return $this->user()?->can('addExpense', $this->sessionParam()) ?? false;
    }
}
