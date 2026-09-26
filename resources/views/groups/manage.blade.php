@extends('layouts.app')

@section('title', 'Kelola ' . $group->name)

@section('content')
    <div class="mb-6">
        <a href="{{ route('groups.show', $group) }}" class="back-link">← Balik ke group</a>
        <h1 class="page-title mt-1">Kelola {{ $group->name }}</h1>
    </div>

    {{-- Info group --}}
    <section class="card">
        <h2 class="section-title">Info group</h2>
        <form method="POST" action="{{ route('groups.update', $group) }}" class="mt-3 space-y-3">
            @csrf
            @method('PATCH')
            <div class="grid gap-3 sm:grid-cols-2">
                <div>
                    <label class="label" for="group-name">Nama</label>
                    <input class="input" id="group-name" type="text" name="name" value="{{ old('name', $group->name) }}" required maxlength="60">
                </div>
                <div>
                    <label class="label" for="group-desc">Deskripsi</label>
                    <input class="input" id="group-desc" type="text" name="description" value="{{ old('description', $group->description) }}" maxlength="500">
                </div>
            </div>
            @error('name')<p class="field-error">{{ $message }}</p>@enderror
            @error('description')<p class="field-error">{{ $message }}</p>@enderror
            <button class="btn btn-primary">Simpan info</button>
        </form>
    </section>

    {{-- Channel management --}}
    <section class="card mt-6">
        <h2 class="section-title">Channels</h2>
        @if ($group->channels->isEmpty())
            <p class="empty mt-2">Belum ada channel.</p>
        @else
            <ul class="mt-3 space-y-3">
                @foreach ($group->channels as $channel)
                    <li class="flex items-start justify-between gap-4 border-t border-line pt-3">
                        <form method="POST" action="{{ route('groups.channels.update', [$group, $channel]) }}" class="flex min-w-0 flex-1 flex-wrap items-start gap-2">
                            @csrf
                            @method('PATCH')
                            <div class="min-w-0 flex-1 space-y-2">
                                <input class="input" type="text" name="name" value="{{ $channel->name }}" required maxlength="80">
                                <input class="input" type="text" name="description" value="{{ $channel->description }}" maxlength="255">
                            </div>
                            <button class="btn btn-secondary btn-sm">Simpan</button>
                        </form>
                        <form method="POST" action="{{ route('groups.channels.destroy', [$group, $channel]) }}"
                            onsubmit="return confirm('Hapus channel {{ $channel->name }}? Patungan di dalamnya pindah ke level group.')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-ghost btn-sm text-danger-text hover:bg-danger-soft">Hapus</button>
                        </form>
                    </li>
                @endforeach
            </ul>
        @endif
    </section>

    {{-- Roles --}}
    <section class="card mt-6">
        <h2 class="section-title">Role + permission</h2>

        @if ($group->roles->isEmpty())
            <p class="empty mt-2">Belum ada role.</p>
        @else
            <ul class="mt-3 space-y-4">
                @foreach ($group->roles as $role)
                    @if ($role->is_system)
                        <li class="rounded-card bg-sunken px-4 py-3">
                            <p class="font-semibold text-ink">
                                {{ $role->name }}
                                <span class="text-xs font-normal text-ink-subtle">(bawaan, nggak bisa diubah · {{ $role->members_count ?? $role->members->count() }} anggota)</span>
                            </p>
                            <div class="mt-1.5 flex flex-wrap gap-1.5">
                                @foreach ($role->permissions ?? [] as $perm)
                                    <span class="badge badge-brand">
                                        {{ \App\Enums\Permission::tryFrom($perm)?->label() ?? $perm }}
                                    </span>
                                @endforeach
                            </div>
                        </li>
                    @else
                        <li class="rounded-card border border-line p-4">
                            <div class="mb-3 flex items-center justify-between gap-2">
                                <p class="font-semibold text-ink">{{ $role->name }}</p>
                                <form method="POST" action="{{ route('groups.roles.destroy', [$group, $role]) }}"
                                    onsubmit="return confirm('Hapus role {{ $role->name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-ghost btn-sm text-danger-text hover:bg-danger-soft">Hapus</button>
                                </form>
                            </div>
                            <form method="POST" action="{{ route('groups.roles.update', [$group, $role]) }}" class="space-y-3">
                                @csrf
                                @method('PATCH')
                                <input class="input" type="text" name="name" value="{{ $role->name }}" required maxlength="60">
                                <div class="grid gap-1.5 sm:grid-cols-2">
                                    @foreach ($permissionGroups as $perm)
                                        <label class="flex items-start gap-2 rounded-control border border-line bg-raised px-3 py-2 text-sm has-[:checked]:border-brand/50 has-[:checked]:bg-brand-soft/60">
                                            <input class="checkbox mt-0.5" type="checkbox" name="permissions[]" value="{{ $perm->value }}"
                                                @checked(in_array($perm->value, $role->permissions ?? []))>
                                            <span class="text-ink">{{ $perm->label() }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                <button class="btn btn-primary btn-sm">Simpan role</button>
                            </form>
                        </li>
                    @endif
                @endforeach
            </ul>
        @endif

        {{-- Create role --}}
        <div class="mt-5 border-t border-line pt-4">
            <h3 class="font-semibold text-ink">Bikin role baru</h3>
            <form method="POST" action="{{ route('groups.roles.store', $group) }}" class="mt-3 space-y-3">
                @csrf
                <input class="input" type="text" name="name" required maxlength="60" placeholder="cth: Bendahara">
                <div class="grid gap-1.5 sm:grid-cols-2">
                    @foreach ($permissionGroups as $perm)
                        <label class="flex items-start gap-2 rounded-control border border-line bg-raised px-3 py-2 text-sm has-[:checked]:border-brand/50 has-[:checked]:bg-brand-soft/60">
                            <input class="checkbox mt-0.5" type="checkbox" name="permissions[]" value="{{ $perm->value }}">
                            <span class="text-ink">{{ $perm->label() }}</span>
                        </label>
                    @endforeach
                </div>
                <button class="btn btn-primary btn-sm">Bikin role</button>
            </form>
        </div>
    </section>

    {{-- Member role assignment --}}
    <section class="card mt-6">
        <h2 class="section-title">Kasih role ke anggota</h2>
        <ul class="divide-list mt-3">
            @foreach ($group->members as $member)
                <li class="flex flex-wrap items-center justify-between gap-2 py-2.5">
                    <div class="flex min-w-0 items-center gap-2 text-sm">
                        <span class="avatar">{{ strtoupper(substr($member->user->name, 0, 1)) }}</span>
                        <span class="truncate font-semibold text-ink">{{ $member->user->name }}</span>
                    </div>
                    @if ($member->user_id === $group->user_id)
                        <span class="badge badge-brand">Owner</span>
                    @else
                        <form method="POST" action="{{ route('groups.members.role', [$group, $member]) }}" class="flex items-center gap-2">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ $member->user_id }}">
                            <select class="select select-sm" name="role_id">
                                @foreach ($group->roles as $role)
                                    <option value="{{ $role->id }}" @selected($member->role_id === $role->id)>{{ $role->name }}</option>
                                @endforeach
                            </select>
                            <button class="btn btn-secondary btn-sm">Ganti</button>
                        </form>
                    @endif
                </li>
            @endforeach
        </ul>
    </section>

    {{-- Invites history --}}
    <section class="card mt-6">
        <h2 class="section-title">Riwayat undangan</h2>
        @if ($group->invites->isEmpty())
            <p class="empty mt-2">Belum pernah bikin undangan.</p>
        @else
            <ul class="divide-list mt-3">
                @foreach ($group->invites->sortByDesc('created_at') as $invite)
                    @php $inviteUsable = $invite->usable(); @endphp
                    <li class="flex flex-wrap items-center justify-between gap-2 py-2.5 text-sm">
                        <div class="min-w-0">
                            <p class="flex flex-wrap items-center gap-2">
                                <code class="truncate font-mono text-xs text-ink-muted">{{ $inviteUsable ? route('join.show', $invite->token) : $invite->token }}</code>
                                @if ($inviteUsable)
                                    <span class="badge badge-success">aktif</span>
                                @elseif ($invite->active && $invite->isExpired())
                                    <span class="badge badge-warning">expired</span>
                                @elseif ($invite->active && $invite->usageLimitReached())
                                    <span class="badge badge-warning">penuh</span>
                                @else
                                    <span class="badge badge-neutral">mati</span>
                                @endif
                            </p>
                            <p class="text-xs text-ink-subtle">
                                {{ $invite->usesSummary() }} · {{ $invite->lifetimeLabel() }} · {{ $invite->created_at?->format('d M Y H:i') }}
                            </p>
                        </div>
                        @if ($inviteUsable)
                            <div class="flex gap-2">
                                <a href="{{ route('join.show', $invite->token) }}" target="_blank" rel="noopener" class="btn btn-secondary btn-sm">Buka link</a>
                                <form method="POST" action="{{ route('groups.invite.revoke', [$group, $invite]) }}">
                                    @csrf
                                    <button class="btn btn-ghost btn-sm text-danger-text hover:bg-danger-soft">Revoke</button>
                                </form>
                            </div>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </section>
@endsection
