<?php

namespace App\Policies;

use App\Models\Debt;
use App\Models\User;

class DebtPolicy
{
    public function view(User $user, Debt $debt): bool
    {
<<<<<<< HEAD
        return $debt->session?->isMember($user) ?? false;
    }

    /**
     * Debtor (pihak yang ngutang) yang lapor udah bayar.
     */
    public function report(User $user, Debt $debt): bool
    {
        return $debt->canDebtorReport($user);
    }

    /**
     * Cuma kreditur yang nanuain pembayaran — dan nggak boleh konfirmasi sendiri.
     */
    public function confirm(User $user, Debt $debt): bool
    {
        return $this->review($user, $debt);
    }

    public function reject(User $user, Debt $debt): bool
    {
        return $this->review($user, $debt);
    }

    private function review(User $user, Debt $debt): bool
    {
        return $debt->canCreditorReview($user);
=======
        return $debt->session->isMember($user);
    }

    /**
     * Cuma yang punya kewajiban (debtor) yang boleh konfirmasi udah bayar.
     */
    public function settle(User $user, Debt $debt): bool
    {
        return $debt->from_user_id === $user->id
            && $debt->session->isMember($user)
            && $debt->status === \App\Enums\DebtStatus::PENDING->value;
>>>>>>> 6561da739345e3ff0fdab546ef4f928a872f067a
    }
}
