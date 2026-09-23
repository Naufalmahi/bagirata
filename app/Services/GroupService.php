<?php

namespace App\Services;

use App\Enums\Permission;
use App\Exceptions\BusinessException;
use App\Models\Group;
use App\Models\GroupInvite;
use App\Models\GroupMember;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GroupService
{
    /**
     * Buat group + seed role owner/member + invite link (dalam 1 transaksi).
     */
    public static function create(User $user, array $data): Group
    {
        return DB::transaction(function () use ($user, $data) {
            $group = Group::create([
                'user_id' => $user->id,
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
            ]);

            $ownerRole = $group->roles()->create([
                'name' => 'Owner',
                'permissions' => Permission::all(),
                'is_system' => true,
                'created_by' => $user->id,
            ]);

            $memberRole = $group->roles()->create([
                'name' => 'Member',
                'permissions' => [Permission::CREATE_PATUNGAN->value],
                'is_system' => true,
                'created_by' => $user->id,
            ]);

            $group->members()->create([
                'user_id' => $user->id,
                'role_id' => $ownerRole->id,
            ]);

            self::makeInvite($group, $user->id);

            return $group;
        });
    }

    /**
     * Buat invite untuk group. Opsi: [expires_days, max_uses, expires_at].
     * Kosong semua = tanpa batas (default).
     */
    public static function makeInvite(Group $group, int $createdByUserId, array $options = []): GroupInvite
    {
        $invite = $group->invites()->create([
            'token' => self::freshToken(),
            'created_by' => $createdByUserId,
            'expires_at' => self::expiryFromOptions($options),
            'max_uses' => isset($options['max_uses']) && $options['max_uses'] !== null
                ? (int) $options['max_uses']
                : null,
        ]);

        return $invite->refresh();
    }

    private static function expiryFromOptions(array $options): ?\Illuminate\Support\Carbon
    {
        if (! empty($options['expires_at'])) {
            return \Illuminate\Support\Carbon::parse($options['expires_at']);
        }

        if (! empty($options['expires_days'])) {
            return now()->addDays((int) $options['expires_days']);
        }

        return null;
    }

    /**
     * Join via invite link. Yang baru masuk dapet role member default.
     */
    public static function join(User $user, GroupInvite $invite): void
    {
        if (! $invite->active) {
            throw new BusinessException('Link undangan udah dicabut, minta invite terbaru ke owner yaa.', 410);
        }

        if ($invite->isExpired()) {
            throw new BusinessException('Link undangan ini udah kedaluwarsa, minta invite baru ke owner.', 410);
        }

        if ($invite->usageLimitReached()) {
            throw new BusinessException('Batas pemakaian link undangan udah abis, minta invite baru ke owner.', 410);
        }

        DB::transaction(function () use ($user, $invite) {
            $existing = GroupMember::where('group_id', $invite->group_id)
                ->where('user_id', $user->id)
                ->first();

            if ($existing) {
                throw new BusinessException('Lu udah gabung di group ini, gak perlu ulang hehe.');
            }

            $memberRole = $invite->group->roles()
                ->where('is_system', true)
                ->where('name', 'Member')
                ->first()
                ?? $invite->group->roles()->first();

            $invite->group->members()->create([
                'user_id' => $user->id,
                'role_id' => $memberRole?->id,
            ]);

            $invite->increment('used_count');
        });
    }

    public static function freshToken(): string
    {
        do {
            $token = Str::random(40);
        } while (GroupInvite::where('token', $token)->exists());

        return $token;
    }
}
