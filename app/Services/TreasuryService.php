<?php

namespace App\Services;

use App\Exceptions\BusinessException;
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
            'name' => 'Kas '.$group->name,
            'balance' => 0,
        ]);
    }

    /**
     * Catat entry baru. Status TIDAK bisa ditentukan pemanggil: owner otomatis
     * approved, selain itu pending. Kalau status sampai diteruskan dari input,
     * anggota bisa self-approve dan langsung crediting saldo sendiri.
     */
    public static function recordEntry(GroupWallet $wallet, User $user, array $data, ?string $receiptPath = null): WalletEntry
    {
        return DB::transaction(function () use ($wallet, $user, $data, $receiptPath) {
            $isOwner = $wallet->group->user_id === $user->id;

            $status = $isOwner ? 'approved' : 'pending';
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
                'reviewed_by' => $approvedBy,
                'reviewed_at' => $isOwner ? now() : null,
            ]);

            if ($status === 'approved') {
                self::applyToBalance($wallet, $entry->type, $entry->amount, 1);
            }

            self::audit($user, 'wallet_entry_created', $entry, $entry->toArray(), "Mencatat kas {$entry->type}: ".self::rp($entry->amount));

            return $entry;
        });
    }

    public static function approveEntry(WalletEntry $entry, User $approver, ?string $note = null): WalletEntry
    {
        return DB::transaction(function () use ($entry, $approver, $note) {
            $fresh = self::lockEntry($entry);

            if ($fresh->isApproved()) {
                // Sudah pernah di-credit. Idempoten, jangan gerakkan saldo lagi.
                return $fresh;
            }

            if ($fresh->isRejected()) {
                throw new BusinessException('Transaksi yang sudah ditolak nggak bisa disetujui. Catat entry baru yaa.');
            }

            $wallet = GroupWallet::whereKey($fresh->group_wallet_id)->lockForUpdate()->firstOrFail();

            // Pengaman kedua: hanya baris yang masih pending boleh jadi approved.
            $diupdate = WalletEntry::whereKey($fresh->id)
                ->where('status', 'pending')
                ->update([
                    'status' => 'approved',
                    'approved_by' => $approver->id,
                    'reviewed_by' => $approver->id,
                    'reviewed_at' => now(),
                    'review_note' => $note,
                ]);

            if ($diupdate === 0) {
                // Sudah diproses request lain. Jangan sentuh saldo.
                return WalletEntry::findOrFail($fresh->id);
            }

            self::applyToBalance($wallet, $fresh->type, $fresh->amount, 1);

            $fresh = WalletEntry::findOrFail($fresh->id);

            self::audit(
                $approver,
                'wallet_entry_approved',
                $fresh,
                ['entry_id' => $fresh->id, 'amount' => $fresh->amount],
                "Menyetujui transaksi kas {$fresh->type} senilai ".self::rp($fresh->amount)
            );

            return $fresh;
        });
    }

    public static function rejectEntry(WalletEntry $entry, User $reviewer, ?string $note = null): WalletEntry
    {
        return DB::transaction(function () use ($entry, $reviewer, $note) {
            $fresh = self::lockEntry($entry);

            if ($fresh->isRejected()) {
                return $fresh;
            }

            $wallet = GroupWallet::whereKey($fresh->group_wallet_id)->lockForUpdate()->firstOrFail();

            // Kunci ledger: kalau entry ini sempat approved, saldonya sudah
            // bergerak. Menolaknya harus membalik, kalau tidak saldo ngaku
            // ada uang masuk padahal transaksinya dibatalkan.
            $wasApproved = $fresh->isApproved();

            $diupdate = WalletEntry::whereKey($fresh->id)
                ->whereIn('status', ['pending', 'approved'])
                ->update([
                    'status' => 'rejected',
                    'approved_by' => null,
                    'reviewed_by' => $reviewer->id,
                    'reviewed_at' => now(),
                    'review_note' => $note,
                ]);

            if ($diupdate === 0) {
                return WalletEntry::findOrFail($fresh->id);
            }

            if ($wasApproved) {
                self::applyToBalance($wallet, $fresh->type, $fresh->amount, -1);
            }

            $fresh = WalletEntry::findOrFail($fresh->id);

            self::audit(
                $reviewer,
                'wallet_entry_rejected',
                $fresh,
                ['entry_id' => $fresh->id, 'amount' => $fresh->amount, 'reversed_balance' => $wasApproved],
                'Menolak transaksi kas: '.($note ?? 'Tanpa alasan')
            );

            return $fresh;
        });
    }

    /**
     * Baris entry yang terkunci untuk update, jadi status yang dibaca di dalam
     * transaksi dijamin data terkini. Urutan lock selalu entry dulu baru wallet,
     * konsisten di approveEntry() dan rejectEntry() supaya nggak deadlock.
     *
     * Di SQLite lockForUpdate() cuma no-op, makanya update bersyarat tetap
     * dipakai sebagai pengaman kedua.
     */
    private static function lockEntry(WalletEntry $entry): WalletEntry
    {
        return WalletEntry::whereKey($entry->id)->lockForUpdate()->firstOrFail();
    }

    /**
     * $sign = 1 untuk credit, -1 untuk reversal saat penolakan.
     */
    private static function applyToBalance(GroupWallet $wallet, string $type, int $amount, int $sign): void
    {
        $delta = $type === 'in' ? $amount * $sign : -($amount * $sign);

        $wallet->update(['balance' => $wallet->balance + $delta]);
    }

    private static function audit(User $actor, string $event, WalletEntry $entry, array $properties, string $description): void
    {
        ActivityLog::create([
            'user_id' => $actor->id,
            'event' => $event,
            'description' => $description,
            'auditable_type' => WalletEntry::class,
            'auditable_id' => $entry->id,
            'properties' => $properties,
        ]);
    }

    private static function rp(int $amount): string
    {
        return 'Rp '.number_format($amount, 0, ',', '.');
    }
}
