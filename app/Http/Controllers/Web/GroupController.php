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

        $searchQuery = $request->input('search', '');
        $publicGroups = collect();

        if (strlen(trim($searchQuery)) >= 2) {
            $userGroupIds = $request->user()->groups()->pluck('groups.id');
            $publicGroups = Group::where('visibility', 'public')
                ->whereNotIn('id', $userGroupIds)
                ->where('name', 'like', "%{$searchQuery}%")
                ->withCount('members')
                ->with('owner')
                ->limit(20)
                ->get();
        }

        return view('groups.index', compact('groups', 'publicGroups', 'searchQuery'));
    }

    public function create()
    {
        return view('groups.create');
    }

    public function store(StoreGroupRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('avatar')) {
            $data['avatar_path'] = $request->file('avatar')->store('group-avatars', 'public');
        }

        $group = GroupService::create($request->user(), $data);

        return redirect()->route('groups.show', $group)
            ->with('success', 'Group kebikin, langsung aja undang temenmu!');
    }

    public function show(Request $request, Group $group)
    {
        $this->authorize('view', $group);

        // Jika group belum punya channel sama sekali, buatkan channel general default
        if ($group->channels()->count() === 0) {
            $group->channels()->create([
                'name' => 'general',
                'description' => 'Channel obrolan umum & nongkrong',
                'sort_order' => 1,
                'created_by' => $group->user_id,
            ]);
        }

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

        $channelId = $request->query('channel');
        $activeChannel = $channelId ? $channels->firstWhere('id', (int) $channelId) : $channels->first();
        if (! $activeChannel && $channels->isNotEmpty()) {
            $activeChannel = $channels->first();
        }

        $messages = $activeChannel ? $activeChannel->messages()->with('user')->latest()->take(50)->get()->reverse()->values() : collect();

        $permissions = PermissionService::permissionsFor($request->user(), $group);
        $can = [
            'invite' => PermissionService::can($request->user(), $group, Permission::INVITE_MEMBERS),
            'create_channel' => PermissionService::can($request->user(), $group, Permission::CREATE_CHANNEL),
            'manage_roles' => PermissionService::isOwner($request->user(), $group),
            'create_patungan' => PermissionService::can($request->user(), $group, Permission::CREATE_PATUNGAN)
                || PermissionService::can($request->user(), $group, Permission::MANAGE_PATUNGAN),
        ];

        // Debt tracking: find members with outstanding debts in this group
        $groupDebts = \App\Models\Debt::whereHas('session', function ($q) use ($group) {
            $q->where('group_id', $group->id);
        })->where('status', '!=', 'settled')
          ->with(['debtor', 'creditor', 'session'])
          ->get();

        return view('groups.show', compact('group', 'sessions', 'channels', 'activeChannel', 'messages', 'permissions', 'can', 'groupDebts'));
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
        $data = $request->validated();

        if ($request->hasFile('avatar')) {
            $data['avatar_path'] = $request->file('avatar')->store('group-avatars', 'public');
        }

        $group->update($data);

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

    public function storeMessage(Request $request, Group $group, Channel $channel)
    {
        $this->authorize('view', $group);

        if ($channel->group_id !== $group->id) {
            abort(404);
        }

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:1000'],
        ]);

        $message = $channel->messages()->create([
            'user_id' => $request->user()->id,
            'body' => $validated['body'],
        ]);

        if ($request->wantsJson()) {
            $message->load('user');
            return response()->json([
                'status' => 'success',
                'data' => [
                    'id' => $message->id,
                    'body' => $message->body,
                    'user_id' => $message->user_id,
                    'user_name' => $message->user->name,
                    'avatar_url' => $message->user->avatar_path ? asset('storage/' . $message->user->avatar_path) : null,
                    'created_at' => $message->created_at->format('H:i'),
                ],
            ]);
        }

        return redirect()->route('groups.show', ['group' => $group, 'channel' => $channel->id])
            ->with('success', 'Pesan terkirim.');
    }

    public function getMessages(Request $request, Group $group, Channel $channel)
    {
        $this->authorize('view', $group);

        if ($channel->group_id !== $group->id) {
            abort(404);
        }

        $messages = $channel->messages()->with('user')->latest()->take(50)->get()->reverse()->values();

        return response()->json([
            'data' => $messages->map(fn ($m) => [
                'id' => $m->id,
                'body' => $m->body,
                'user_id' => $m->user_id,
                'user_name' => $m->user->name,
                'avatar_url' => $m->user->avatar_path ? asset('storage/' . $m->user->avatar_path) : null,
                'created_at' => $m->created_at->format('H:i'),
            ]),
        ]);
    }

    public function joinPublic(Request $request, Group $group)
    {
        if ($group->visibility !== 'public') {
            return back()->with('error', 'Grup ini bersifat private.');
        }

        if ($group->isMember($request->user())) {
            return back()->with('error', 'Kamu sudah menjadi anggota grup ini.');
        }

        $memberRole = $group->roles()->where('is_system', true)->where('name', 'Member')->first();

        $group->members()->create([
            'user_id' => $request->user()->id,
            'role_id' => $memberRole?->id,
        ]);

        return redirect()->route('groups.show', $group)
            ->with('success', 'Berhasil bergabung ke grup!');
    }

    public function kickMember(Request $request, Group $group)
    {
        $this->authorize('manageRoles', $group);

        $validated = $request->validate([
            'user_id' => ['required', 'integer'],
        ]);

        $member = $group->members()->where('user_id', $validated['user_id'])->firstOrFail();

        if ($member->user_id === $group->user_id) {
            return back()->with('error', 'Tidak bisa mengeluarkan owner grup.');
        }

        $member->delete();

        return back()->with('success', 'Anggota berhasil dikeluarkan dari grup.');
    }
}
