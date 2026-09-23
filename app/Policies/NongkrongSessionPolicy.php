<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Models\NongkrongSession;
use App\Models\User;
use App\Services\PermissionService;

class NongkrongSessionPolicy
{
    public function view(User $user, NongkrongSession $session): bool
    {
        return $session->isMember($user);
    }

    public function update(User $user, NongkrongSession $session): bool
    {
        return $session->user_id === $user->id;
    }

    public function delete(User $user, NongkrongSession $session): bool
    {
        return $session->user_id === $user->id;
    }

    public function addExpense(User $user, NongkrongSession $session): bool
    {
        if (! $session->isMember($user)) {
            return false;
        }

        // Session dalam group → butuh permission bikin patungan / kelola patungan.
        if ($session->group) {
            return PermissionService::can($user, $session->group, Permission::CREATE_PATUNGAN)
                || PermissionService::can($user, $session->group, Permission::MANAGE_PATUNGAN);
        }

        return true;
    }
}
