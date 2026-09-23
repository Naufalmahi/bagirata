<?php

namespace App\Services;

use App\Enums\DebtStatus;
use App\Exceptions\BusinessException;
use App\Models\Channel;
use App\Models\Group;
use App\Models\NongkrongSession;
use App\Models\User;
use App\Services\Support\Money;

class SessionService
{
    /**
     * @param  array{name: string, date: string, description?: ?string, group_id?: ?int, channel_id?: ?int, member_ids: array<int>}  $data
     */
    public static function create(User $user, array $data): NongkrongSession
    {
        $group = null;
        if (! empty($data['group_id'])) {
            $group = Group::findOrFail($data['group_id']);

            if (! empty($data['channel_id'])) {
                $channel = Channel::whereKey($data['channel_id'])->first();
                if (! $channel || $channel->group_id !== $group->id) {
                    throw new BusinessException('Channel-nya bukan punya group itu, cek lagi yaa.');
                }
            }

            // Semua yang ikut harus member group.
            $memberIds = array_values(array_unique(array_merge([$user->id], $data['member_ids'] ?? [])));
            $inGroup = $group->members()->whereIn('user_id', $memberIds)->count();
            if ($inGroup !== count($memberIds)) {
                throw new BusinessException('Ada peserta yang bukan member group. Ajakin gabung dulu yaa.');
            }
        }

        $session = NongkrongSession::create([
            'user_id' => $user->id,
            'group_id' => $data['group_id'] ?? null,
            'channel_id' => $data['channel_id'] ?? ($group ? null : null),
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'date' => $data['date'],
        ]);

        // Creator otomatis ikut; member_ids = temen yang diajak.
        $memberIds = array_values(array_unique(array_merge([$user->id], $data['member_ids'] ?? [])));
        $session->members()->sync($memberIds);

        return $session;
    }

    /**
     * Ringkasan ala SessionResource->splitSummary(), buat halaman SSR.
     *
     * @return array<string, int|float|string>
     */
    public static function summaryFor(NongkrongSession $session, int $viewerId): array
    {
        $session->loadMissing(['expenses', 'debts']);

        $balances = DebtService::netBalances($session);
        $viewerBalance = $balances[$viewerId] ?? 0;

        $totalSpent = $session->expenses->sum(fn ($expense) => $expense->grandTotal());
        $pending = $session->debts
            ->where('status', DebtStatus::PENDING->value)
            ->sum('amount');

        return [
            'status' => $session->status(),
            'status_label' => $session->statusLabel(),
            'total_spent' => $totalSpent,
            'total_spent_formatted' => Money::format($totalSpent),
            'total_pending' => $pending,
            'total_pending_formatted' => Money::format($pending),
            'viewer_balance' => $viewerBalance,
            'viewer_balance_formatted' => Money::format(abs($viewerBalance)),
            'viewer_role' => $viewerBalance > 0 ? 'creditor' : ($viewerBalance < 0 ? 'debtor' : 'neutral'),
        ];
    }
}
