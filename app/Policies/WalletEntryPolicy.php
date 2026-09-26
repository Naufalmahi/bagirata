<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WalletEntry;

class WalletEntryPolicy
{
    /**
     * Hanya owner wallet yang boleh menyetujui/menolak entry.
     *
     * Penting:dicek lewat wallet milik entry itu sendiri, bukan lewat group di
     * URL. Kalau cuma group-nya yang dicek, admin group A bisa menyetujui entry
     * group B.
     */
    public function approveEntry(User $user, WalletEntry $entry): bool
    {
        return $entry->wallet?->group?->user_id === $user->id;
    }

    public function rejectEntry(User $user, WalletEntry $entry): bool
    {
        return $this->approveEntry($user, $entry);
    }

    public function view(User $user, WalletEntry $entry): bool
    {
        $group = $entry->wallet?->group;

        if (! $group) {
            return false;
        }

        return $group->user_id === $user->id || $group->isMember($user);
    }
}
