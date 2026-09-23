<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Models\Group;
use App\Models\User;
use App\Services\PermissionService;

class GroupPolicy
{
    private function isOwner(User $user, Group $group): bool
    {
        return PermissionService::isOwner($user, $group);
    }

    private function isMember(User $user, Group $group): bool
    {
        return $group->isMember($user);
    }

    public function view(User $user, Group $group): bool
    {
        return $this->isOwner($user, $group) || $this->isMember($user, $group);
    }

    public function update(User $user, Group $group): bool
    {
        return $this->isOwner($user, $group);
    }

    public function delete(User $user, Group $group): bool
    {
        return $this->isOwner($user, $group);
    }

    public function invite(User $user, Group $group): bool
    {
        return $this->isOwner($user, $group)
            || PermissionService::can($user, $group, Permission::INVITE_MEMBERS);
    }

    public function manageRoles(User $user, Group $group): bool
    {
        return $this->isOwner($user, $group);
    }

    public function manageInvites(User $user, Group $group): bool
    {
        return $this->invite($user, $group);
    }

    public function createChannel(User $user, Group $group): bool
    {
        return $this->isOwner($user, $group)
            || PermissionService::can($user, $group, Permission::CREATE_CHANNEL);
    }
}
