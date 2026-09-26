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

    public function reportPayment(User $user, Debt $debt): bool
    {
        return $debt->canDebtorReport($user);
    }

    public function reviewPayment(User $user, Debt $debt): bool
    {
        return $debt->canCreditorReview($user);
    }
}
