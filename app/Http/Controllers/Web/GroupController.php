<?php

namespace App\Http\Controllers\Web;

use App\Enums\Permission;
use App\Http\Controllers\Controller;
use App\Http\Requests\Groups\AssignRoleRequest;
use App\Http\Requests\Groups\StoreChannelRequest;
use App\Http\Requests\Groups\StoreGroupRequest;
use App\Http\Requests\Groups\StoreInviteRequest;
use App\Http\Requests\Groups\StoreRoleRequest;
use App\Http\Requests\Groups\UpdateChannelRequest;
use App\Http\Requests\Groups\UpdateGroupRequest;
use App\Http\Requests\Groups\UpdateRoleRequest;
use App\Models\Channel;
use App\Models\Group;
use App\Models\GroupInvite;
use App\Models\GroupRole;
use App\Services\GroupService;
use App\Services\PermissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GroupController extends Controller
{
    public function index(Request $request)
    {
        $groups = $request->user()->groups()
            ->withCount(['members', 'sessions'])
            ->with(['owner', 'activeInvite'])
            ->latest()
            ->get();

        return view('groups.index', compact('groups'));
    }

    public function create()
    {
        return view('groups.create');
    }

    public function store(StoreGroupRequest $request)
    {
        $group = GroupService::create($request->user(), $request->validated());

        return redirect()->route('groups.show', $group)
            ->with('success', 'Group kebikin, langsung aja undang temenmu!');
    }

    public function show(Request $request, Group $group)
    {
        $this->authorize('view', $group);

        $group->load([
            'owner',
            'roles',
            'members.user',
            'members.role',
            'channels.sessions',
            'activeInvite',
            'sessions.expenses',
            'sessions.debts',
            'sessions.members',
            'sessions.creator',
        ]);

        $sessions = $group->sessions->sortByDesc(fn ($s) => $s->date?->timestamp ?? 0);
        $channels = $group->channels;

        $permissions = PermissionService::permissionsFor($request->user(), $group);
        $can = [
            'invite' => PermissionService::can($request->user(), $group, Permission::INVITE_MEMBERS),
            'create_channel' => PermissionService::can($request->user(), $group, Permission::CREATE_CHANNEL),
            'manage_roles' => PermissionService::isOwner($request->user(), $group),
            'create_patungan' => PermissionService::can($request->user(), $group, Permission::CREATE_PATUNGAN)
                || PermissionService::can($request->user(), $group, Permission::MANAGE_PATUNGAN),
        ];

        return view('groups.show', compact('group', 'sessions', 'channels', 'permissions', 'can'));
    }

    public function manage(Request $request, Group $group)
    {
        $this->authorize('manageRoles', $group);

        $group->load([
            'owner',
            'roles.members',
            'members.user',
            'members.role',
            'channels',
            'invites',
        ]);

        $permissionGroups = Permission::cases();

        return view('groups.manage', compact('group', 'permissionGroups'));
    }

    public function update(UpdateGroupRequest $request, Group $group)
    {
        $group->update($request->validated());

        return back()->with('success', 'Group ke-update.');
    }

    public function destroy(Request $request, Group $group)
    {
        $this->authorize('delete', $group);

        $group->delete();

        return redirect()->route('groups.index')->with('success', 'Group dihapus. Sedih, tapi oke.');
    }

    public function generateInvite(StoreInviteRequest $request, Group $group)
    {
        DB::transaction(function () use ($request, $group) {
            $group->invites()->where('active', true)->get()->each->revoke($request->user()->id);

            return GroupService::makeInvite($group, $request->user()->id, $request->validated());
        });

        return back()->with('success', 'Link undangan baru aktif, yang lama langsung dibatalin.');
    }

    public function revokeInvite(Request $request, Group $group, GroupInvite $invite)
    {
        $this->authorize('invite', $group);

        if ($invite->group_id !== $group->id) {
            abort(404);
        }

        $invite->revoke($request->user()->id);

        return back()->with('success', 'Link undangan udah dibatalin.');
    }

    public function storeChannel(StoreChannelRequest $request, Group $group)
    {
        $group->channels()->create([
            'name' => $request->validated()['name'],
            'description' => $request->validated()['description'] ?? null,
            'sort_order' => $group->channels()->count() + 1,
            'created_by' => $request->user()->id,
        ]);

        return back()->with('success', 'Channel baru kebikin.');
    }

    public function updateChannel(UpdateChannelRequest $request, Group $group, Channel $channel)
    {
        $this->authorize('update', $channel);

        if ($channel->group_id !== $group->id) {
            abort(404);
        }

        $channel->update($request->validated());

        return back()->with('success', 'Channel ke-update.');
    }

    public function destroyChannel(Request $request, Group $group, Channel $channel)
    {
        $this->authorize('delete', $channel);

        if ($channel->group_id !== $group->id) {
            abort(404);
        }

        DB::transaction(function () use ($channel) {
            $channel->sessions()->update(['channel_id' => null]);
            $channel->delete();
        });

        return back()->with('success', 'Channel dihapus, patungan di dalamnya pindah ke level group.');
    }

    public function storeRole(StoreRoleRequest $request, Group $group)
    {
        $role = $group->roles()->create([
            'name' => $request->validated()['name'],
            'permissions' => $request->validated()['permissions'] ?? [],
            'created_by' => $request->user()->id,
        ]);

        return back()->with('success', "Role {$role->name} kebikin.");
    }

    public function updateRole(UpdateRoleRequest $request, Group $group, GroupRole $role)
    {
        if ($role->group_id !== $group->id) {
            abort(404);
        }
        if ($role->is_system) {
            return back()->with('error', 'Role bawaan (Owner/Member) nggak bisa diubah.');
        }

        $role->update([
            'name' => $request->validated()['name'],
            'permissions' => $request->validated()['permissions'] ?? [],
        ]);

        return back()->with('success', 'Role ke-update.');
    }

    public function destroyRole(Request $request, Group $group, GroupRole $role)
    {
        $this->authorize('manageRoles', $group);

        if ($role->group_id !== $group->id) {
            abort(404);
        }
        if ($role->is_system) {
            return back()->with('error', 'Role bawaan nggak bisa dihapus.');
        }
        if ($role->members()->exists()) {
            return back()->with('error', 'Role masih dipakai member, ganti role mereka dulu yaa.');
        }

        $role->delete();

        return back()->with('success', 'Role kehapus.');
    }

    public function assignRole(AssignRoleRequest $request, Group $group)
    {
        $this->authorize('manageRoles', $group);

        $member = $group->members()->where('user_id', $request->validated()['user_id'])->firstOrFail();

        if ($member->user_id === $group->user_id) {
            return back()->with('error', 'Owner nggak bisa diturunin role-nya.');
        }

        $role = GroupRole::where('id', $request->validated()['role_id'])
            ->where('group_id', $group->id)
            ->firstOrFail();

        $member->update(['role_id' => $role->id]);

        return back()->with('success', 'Role anggota ke-update.');
    }
}
