<?php

namespace App\Policies;

use App\Models\Debt;
use App\Models\User;

class DebtPolicy
{
    public function view(User $user, Debt $debt): bool
    {
        return $debt->session->isMember($user);
    }

    public function settle(User $user, Debt $debt): bool
    {
        return $debt->from_user_id === $user->id
            && $debt->session->isMember($user)
            && $debt->status === \App\Enums\DebtStatus::PENDING->value;
    }
}
