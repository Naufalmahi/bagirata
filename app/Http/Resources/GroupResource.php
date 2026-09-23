<?php

namespace App\Http\Resources;

use App\Services\PermissionService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupResource extends JsonResource
{
    protected bool $withManagement = true;

    public function withManagement(bool $value = true): static
    {
        $this->withManagement = $value;

        return $this;
    }

    public function toArray(Request $request): array
    {
        $permissions = PermissionService::permissionsFor($request->user(), $this->resource);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'owner' => new UserResource($this->whenLoaded('owner')),
            'is_owner' => $request->user()?->id === $this->user_id,
            'my_permissions' => $permissions,
            'roles' => GroupRoleResource::collection($this->whenLoaded('roles')),
            'channels' => ChannelResource::collection($this->whenLoaded('channels')),
            'members' => GroupMemberResource::collection($this->whenLoaded('members')),
            'invite' => $this->when($this->withManagement, function () use ($request) {
                if (PermissionService::isOwner($request->user(), $this->resource)
                    || $this->activeInvite) {
                    return new GroupInviteResource($this->activeInvite);
                }

                return null;
            }),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
