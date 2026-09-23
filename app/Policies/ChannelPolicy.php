<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Models\Channel;
use App\Models\User;
use App\Services\PermissionService;

class ChannelPolicy
{
    public function view(User $user, Channel $channel): bool
    {
        return $this->isOwnerOrMember($user, $channel);
    }

    public function create(User $user, Channel $channel): bool
    {
        return $this->canManage($user, $channel);
    }

    public function update(User $user, Channel $channel): bool
    {
        return $this->canManage($user, $channel);
    }

    public function delete(User $user, Channel $channel): bool
    {
        return $this->canManage($user, $channel);
    }

    private function isOwnerOrMember(User $user, Channel $channel): bool
    {
        $group = $channel->group;

        return PermissionService::isOwner($user, $group) || $group->isMember($user);
    }

    private function canManage(User $user, Channel $channel): bool
    {
        $group = $channel->group;

        return PermissionService::isOwner($user, $group)
            || PermissionService::can($user, $group, Permission::MANAGE_CHANNEL)
            || $channel->created_by === $user->id;
    }
}
