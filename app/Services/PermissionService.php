<?php

namespace App\Services;

use App\Enums\Permission;
use App\Models\Group;
use App\Models\User;

/**
 * Role + permission dalam group. Owner selalu full akses. Member dicek dari role-nya.
 */
class PermissionService
{
    /**
     * @return array<int, string> slug permission yang dimiliki user di group ini.
     */
    public static function permissionsFor(?User $user, Group $group): array
    {
        if (! $user) {
            return [];
        }

        if ($group->user_id === $user->id) {
            return Permission::all();
        }

        $member = $group->members()->where('user_id', $user->id)->with('role')->first();
        if (! $member || ! $member->role) {
            return [];
        }

        return $member->role->permissions ?? [];
    }

    public static function can(?User $user, Group $group, Permission|string $permission): bool
    {
        if (! $user) {
            return false;
        }

        $permission = $permission instanceof Permission ? $permission->value : $permission;

        return in_array($permission, self::permissionsFor($user, $group), true);
    }

    public static function isOwner(?User $user, Group $group): bool
    {
        return $user !== null && $group->user_id === $user->id;
    }
}
