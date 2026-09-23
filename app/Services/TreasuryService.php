<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Group;
use App\Models\GroupWallet;
use App\Models\User;
use App\Models\WalletEntry;
use Illuminate\Support\Facades\DB;

class TreasuryService
{
    public static function getOrCreateWallet(Group $group): GroupWallet
    {
        return $group->wallet ?? GroupWallet::create([
            'group_id' => $group->id,
            'name' => 'Kas ' . $group->name,
            'balance' => 0,
        ]);
    }

    public static function recordEntry(GroupWallet $wallet, User $user, array $data, ?string $receiptPath = null): WalletEntry
    {
        return DB::transaction(function () use ($wallet, $user, $data, $receiptPath) {
            $isOwner = $wallet->group->user_id === $user->id;
            
            // Auto-approve if created by owner or under a certain threshold, else pending
            $status = $isOwner ? 'approved' : ($data['status'] ?? 'pending');
            $approvedBy = $status === 'approved' ? $user->id : null;

            $entry = $wallet->entries()->create([
                'user_id' => $user->id,
                'type' => $data['type'],
                'category' => $data['category'],
                'amount' => $data['amount'],
                'description' => $data['description'] ?? null,
                'receipt_photo' => $receiptPath,
                'status' => $status,
                'approved_by' => $approvedBy,
            ]);

            if ($status === 'approved') {
                self::applyToBalance($wallet, $entry->type, $entry->amount);
            }

            ActivityLog::create([
                'user_id' => $user->id,
                'event' => 'wallet_entry_created',
                'description' => "Mencatat kas {$entry->type}: Rp " . number_format($entry->amount, 0, ',', '.'),
                'auditable_type' => WalletEntry::class,
                'auditable_id' => $entry->id,
                'properties' => $entry->toArray(),
            ]);

            return $entry;
        });
    }

    public static function approveEntry(WalletEntry $entry, User $approver): WalletEntry
    {
        return DB::transaction(function () use ($entry, $approver) {
            if ($entry->status === 'approved') {
                return $entry;
            }

            $wallet = $entry->wallet()->lockForUpdate()->first();

            $entry->update([
                'status' => 'approved',
                'approved_by' => $approver->id,
            ]);

            self::applyToBalance($wallet, $entry->type, $entry->amount);

            ActivityLog::create([
                'user_id' => $approver->id,
                'event' => 'wallet_entry_approved',
                'description' => "Menyetujui transaksi kas {$entry->type} senilai Rp " . number_format($entry->amount, 0, ',', '.'),
                'auditable_type' => WalletEntry::class,
                'auditable_id' => $entry->id,
            ]);

            return $entry;
        });
    }

    public static function rejectEntry(WalletEntry $entry, User $reviewer, ?string $note = null): WalletEntry
    {
        return DB::transaction(function () use ($entry, $reviewer, $note) {
            $entry->update([
                'status' => 'rejected',
                'approved_by' => $reviewer->id,
            ]);

            ActivityLog::create([
                'user_id' => $reviewer->id,
                'event' => 'wallet_entry_rejected',
                'description' => "Menolak transaksi kas: " . ($note ?? 'Tanpa alasan'),
                'auditable_type' => WalletEntry::class,
                'auditable_id' => $entry->id,
            ]);

            return $entry;
        });
    }

    private static function applyToBalance(GroupWallet $wallet, string $type, int $amount): void
    {
        if ($type === 'in') {
            $wallet->increment('balance', $amount);
        } else {
            $wallet->decrement('balance', $amount);
        }
    }
}
