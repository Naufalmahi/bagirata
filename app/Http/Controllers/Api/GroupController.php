<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Groups\AssignRoleRequest;
use App\Http\Requests\Groups\StoreGroupRequest;
use App\Http\Requests\Groups\StoreInviteRequest;
use App\Http\Requests\Groups\StoreRoleRequest;
use App\Http\Requests\Groups\UpdateGroupRequest;
use App\Http\Requests\Groups\UpdateRoleRequest;
use App\Http\Resources\GroupInviteResource;
use App\Http\Resources\GroupResource;
use App\Http\Resources\GroupRoleResource;
use App\Models\Group;
use App\Models\GroupInvite;
use App\Models\GroupRole;
use App\Services\GroupService;
use App\Support\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GroupController extends Controller
{
    public function index(Request $request)
    {
        $groups = $request->user()->groups()
            ->with(['owner', 'channels', 'activeInvite'])
            ->latest()
            ->get();

        return ApiResponse::success(
            GroupResource::collection($groups),
            ''
        );
    }

    public function store(StoreGroupRequest $request)
    {
        $group = GroupService::create($request->user(), $request->validated());

        $this->loadDetail($group);

        return ApiResponse::created(new GroupResource($group), 'Group beres dibikin!');
    }

    public function show(Request $request, Group $group)
    {
        $this->authorize('view', $group);
        $this->loadDetail($group);

        return ApiResponse::success(new GroupResource($group), '');
    }

    public function update(UpdateGroupRequest $request, Group $group)
    {
        $group->update($request->validated());
        $this->loadDetail($group);

        return ApiResponse::success(new GroupResource($group), 'Group ke-update.');
    }

    public function destroy(Request $request, Group $group)
    {
        $this->authorize('delete', $group);
        $group->delete();

        return ApiResponse::noContent('Group dihapus, semua data aman di riwayat.');
    }

    public function getInvite(Request $request, Group $group)
    {
        $this->authorize('invite', $group);

        return ApiResponse::success(
            $group->activeInvite ? new GroupInviteResource($group->activeInvite) : null,
            ''
        );
    }

    public function generateInvite(StoreInviteRequest $request, Group $group)
    {
        $invite = DB::transaction(function () use ($request, $group) {
            $group->invites()->where('active', true)->get()->each->revoke($request->user()->id);

            return GroupService::makeInvite($group, $request->user()->id, $request->validated());
        });

        return ApiResponse::created(new GroupInviteResource($invite), 'Link undangan baru aktif, yang lama udah dibatalin.');
    }

    public function revokeInvite(Request $request, Group $group, GroupInvite $invite)
    {
        $this->authorize('invite', $group);

        if ($invite->group_id !== $group->id) {
            abort(404);
        }

        $invite->revoke($request->user()->id);

        return ApiResponse::noContent('Link undangan udah dibatalin.');
    }

    public function roles(Request $request, Group $group)
    {
        $this->authorize('view', $group);
        $group->load('roles');

        return ApiResponse::success(GroupRoleResource::collection($group->roles), '');
    }

    public function storeRole(StoreRoleRequest $request, Group $group)
    {
        $role = $group->roles()->create([
            'name' => $request->validated()['name'],
            'permissions' => $request->validated()['permissions'] ?? [],
            'created_by' => $request->user()->id,
        ]);

        return ApiResponse::created(new GroupRoleResource($role), 'Role baru kebikin.');
    }

    public function updateRole(UpdateRoleRequest $request, Group $group, GroupRole $role)
    {
        if ($role->group_id !== $group->id) {
            abort(404);
        }
        if ($role->is_system) {
            return ApiResponse::error('Role bawaan (Owner/Member) nggak bisa diubah.', 422);
        }

        $role->update([
            'name' => $request->validated()['name'],
            'permissions' => $request->validated()['permissions'] ?? [],
        ]);

        return ApiResponse::success(new GroupRoleResource($role), 'Role ke-update.');
    }

    public function destroyRole(Request $request, Group $group, GroupRole $role)
    {
        $this->authorize('manageRoles', $group);

        if ($role->group_id !== $group->id) {
            abort(404);
        }
        if ($role->is_system) {
            return ApiResponse::error('Role bawaan nggak bisa dihapus.', 422);
        }
        if ($role->members()->exists()) {
            return ApiResponse::error('Role masih dipakai member, ganti role mereka dulu yaa.', 422);
        }

        $role->delete();

        return ApiResponse::noContent('Role kehapus.');
    }

    public function assignRole(AssignRoleRequest $request, Group $group)
    {
        $this->authorize('manageRoles', $group);

        $member = $group->members()->where('user_id', $request->validated()['user_id'])->firstOrFail();

        if ($member->user_id === $group->user_id) {
            return ApiResponse::error('Owner nggak bisa diturunin role-nya.', 422);
        }

        $role = GroupRole::where('id', $request->validated()['role_id'])
            ->where('group_id', $group->id)
            ->firstOrFail();

        $member->update(['role_id' => $role->id]);

        $members = $group->members()->with(['user', 'role'])->get();

        return ApiResponse::success(
            \App\Http\Resources\GroupMemberResource::collection($members),
            'Role anggota ke-update.'
        );
    }

    private function loadDetail(Group $group): void
    {
        $group->load([
            'owner',
            'roles',
            'members.user',
            'members.role',
            'channels' => fn ($query) => $query->withCount('sessions'),
            'activeInvite',
        ]);
    }
}
