<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Models\Expense;
use App\Models\User;
use App\Services\PermissionService;

class ExpensePolicy
{
    public function view(User $user, Expense $expense): bool
    {
        return $expense->session->isMember($user);
    }

    public function update(User $user, Expense $expense): bool
    {
        return $this->canManage($user, $expense);
    }

    public function delete(User $user, Expense $expense): bool
    {
        return $this->canManage($user, $expense);
    }

    private function canManage(User $user, Expense $expense): bool
    {
        $session = $expense->session;

        // Creator session / creator expense berhak.
        if ($session->user_id === $user->id || $expense->created_by === $user->id) {
            return true;
        }

        // Dalam group, permission manage_patungan juga nge-bypass.
        if ($session->group) {
            return PermissionService::can($user, $session->group, Permission::MANAGE_PATUNGAN);
        }

        return false;
    }
}
