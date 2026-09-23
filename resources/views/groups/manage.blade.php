@extends('layouts.app')

@section('title', 'Kelola ' . $group->name)

@section('content')
    <div class="mb-6">
        <a href="{{ route('groups.show', $group) }}" class="text-sm font-semibold text-indigo-600 hover:underline">← Balik ke group</a>
        <h1 class="mt-1 text-2xl font-extrabold text-slate-900">Kelola {{ $group->name }}</h1>
    </div>

    {{-- Info group --}}
    <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="font-bold text-slate-900">Info group</h2>
        <form method="POST" action="{{ route('groups.update', $group) }}" class="mt-3 space-y-3">
            @csrf
            @method('PATCH')
            <div class="grid gap-3 sm:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-semibold">Nama</label>
                    <input type="text" name="name" value="{{ old('name', $group->name) }}" required maxlength="60"
                        class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-semibold">Deskripsi</label>
                    <input type="text" name="description" value="{{ old('description', $group->description) }}" maxlength="500"
                        class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>
            <button class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Simpan info</button>
        </form>
    </section>

    {{-- Channel management --}}
    <section class="mt-6 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="font-bold text-slate-900">Channels</h2>
        @if ($group->channels->isEmpty())
            <p class="mt-2 text-sm text-slate-400">Belum ada channel.</p>
        @else
            <ul class="mt-3 space-y-3">
                @foreach ($group->channels as $channel)
                    <li class="flex items-start justify-between gap-4 border-t border-slate-100 pt-3">
                        <div class="min-w-0 flex-1">
                            <form method="POST" action="{{ route('groups.channels.update', [$group, $channel]) }}" class="flex flex-wrap items-start gap-2">
                                @csrf
                                @method('PATCH')
                                <div class="min-w-0 flex-1">
                                    <input type="text" name="name" value="{{ $channel->name }}" required maxlength="80"
                                        class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
                                    <input type="text" name="description" value="{{ $channel->description }}" maxlength="255"
                                        class="mt-2 w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                                <button class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50">Simpan</button>
                            </form>
                        </div>
                        <form method="POST" action="{{ route('groups.channels.destroy', [$group, $channel]) }}"
                            onsubmit="return confirm('Hapus channel {{ $channel->name }}? Patungan di dalamnya pindah ke level group.')">
                            @csrf
                            @method('DELETE')
                            <button class="rounded-lg border border-rose-200 px-3 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50">Hapus</button>
                        </form>
                    </li>
                @endforeach
            </ul>
        @endif
    </section>

    {{-- Roles --}}
    <section class="mt-6 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-slate-900">Role + permission</h2>
        </div>

        @if ($group->roles->isEmpty())
            <p class="mt-2 text-sm text-slate-400">Belum ada role.</p>
        @else
            <ul class="mt-3 space-y-4">
                @foreach ($group->roles as $role)
                    @if ($role->is_system)
                        <li class="rounded-2xl bg-slate-50 px-4 py-3">
                            <div class="flex items-center justify-between">
                                <p class="font-semibold text-slate-700">{{ $role->name }} <span class="text-xs font-normal text-slate-400">(bawaan, nggak bisa diubah · {{ $role->members_count ?? $role->members->count() }} anggota)</span></p>
                            </div>
                            <div class="mt-1 flex flex-wrap gap-1.5">
                                @foreach ($role->permissions ?? [] as $perm)
                                    <span class="rounded-full bg-indigo-50 px-2 py-0.5 text-xs font-medium text-indigo-600">
                                        {{ \App\Enums\Permission::tryFrom($perm)?->label() ?? $perm }}
                                    </span>
                                @endforeach
                            </div>
                        </li>
                    @else
                        <li class="rounded-2xl border border-slate-200 p-4">
                            <div class="mb-3 flex items-center justify-between">
                                <p class="font-semibold text-slate-800">{{ $role->name }}</p>
                                <form method="POST" action="{{ route('groups.roles.destroy', [$group, $role]) }}"
                                    onsubmit="return confirm('Hapus role {{ $role->name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-xs font-semibold text-rose-500 hover:underline">Hapus</button>
                                </form>
                            </div>
                            <form method="POST" action="{{ route('groups.roles.update', [$group, $role]) }}" class="space-y-3">
                                @csrf
                                @method('PATCH')
                                <input type="text" name="name" value="{{ $role->name }}" required maxlength="60"
                                    class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
                                <div class="grid gap-1.5 sm:grid-cols-2">
                                    @foreach ($permissionGroups as $perm)
                                        <label class="flex items-start gap-2 rounded-xl border border-slate-100 px-3 py-2 text-sm">
                                            <input type="checkbox" name="permissions[]" value="{{ $perm->value }}"
                                                @checked(in_array($perm->value, $role->permissions ?? [])) class="mt-0.5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                            <span>{{ $perm->label() }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                <button class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Simpan role</button>
                            </form>
                        </li>
                    @endif
                @endforeach
            </ul>
        @endif

        {{-- Create role --}}
        <div class="mt-5 border-t border-slate-200 pt-4">
            <h3 class="font-semibold text-slate-700">Bikin role baru</h3>
            <form method="POST" action="{{ route('groups.roles.store', $group) }}" class="mt-3 space-y-3">
                @csrf
                <input type="text" name="name" required maxlength="60" placeholder="cth: Bendahara"
                    class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
                <div class="grid gap-1.5 sm:grid-cols-2">
                    @foreach ($permissionGroups as $perm)
                        <label class="flex items-start gap-2 rounded-xl border border-slate-100 px-3 py-2 text-sm">
                            <input type="checkbox" name="permissions[]" value="{{ $perm->value }}"
                                class="mt-0.5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            <span>{{ $perm->label() }}</span>
                        </label>
                    @endforeach
                </div>
                <button class="rounded-lg bg-slate-800 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-900">Bikin role</button>
            </form>
        </div>
    </section>

    {{-- Member role assignment --}}
    <section class="mt-6 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="font-bold text-slate-900">Kasih role ke anggota</h2>
        <ul class="mt-3 divide-y divide-slate-100">
            @foreach ($group->members as $member)
                <li class="flex flex-wrap items-center justify-between gap-2 py-2.5">
                    <div class="flex items-center gap-2 text-sm">
                        <span class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-indigo-100 font-bold text-indigo-600">{{ strtoupper(substr($member->user->name, 0, 1)) }}</span>
                        <span class="font-semibold text-slate-800">{{ $member->user->name }}</span>
                    </div>
                    @if ($member->user_id === $group->user_id)
                        <span class="text-xs font-semibold text-indigo-500">Owner</span>
                    @else
                        <form method="POST" action="{{ route('groups.members.role', [$group, $member]) }}" class="flex items-center gap-2">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ $member->user_id }}">
                            <select name="role_id" class="rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @foreach ($group->roles as $role)
                                    <option value="{{ $role->id }}" @selected($member->role_id === $role->id)>{{ $role->name }}</option>
                                @endforeach
                            </select>
                            <button class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-600 hover:bg-slate-50">Ganti</button>
                        </form>
                    @endif
                </li>
            @endforeach
        </ul>
    </section>

    {{-- Invites history --}}
    <section class="mt-6 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="font-bold text-slate-900">Riwayat undangan</h2>
        @if ($group->invites->isEmpty())
            <p class="mt-2 text-sm text-slate-400">Belum pernah bikin undangan.</p>
        @else
            <ul class="mt-3 divide-y divide-slate-100">
                @foreach ($group->invites->sortByDesc('created_at') as $invite)
                    @php $inviteUsable = $invite->usable(); @endphp
                    <li class="flex flex-wrap items-center justify-between gap-2 py-2.5 text-sm">
                        <div class="min-w-0">
                            <p class="flex flex-wrap items-center gap-2">
                                <code class="truncate text-xs text-slate-500">{{ $inviteUsable ? route('join.show', $invite->token) : $invite->token }}</code>
                                @if ($inviteUsable)
                                    <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-semibold text-emerald-600">aktif</span>
                                @elseif ($invite->active && $invite->isExpired())
                                    <span class="rounded-full bg-amber-50 px-2 py-0.5 text-xs font-semibold text-amber-600">expired</span>
                                @elseif ($invite->active && $invite->usageLimitReached())
                                    <span class="rounded-full bg-amber-50 px-2 py-0.5 text-xs font-semibold text-amber-600">penuh</span>
                                @else
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-500">mati</span>
                                @endif
                            </p>
                            <p class="text-xs text-slate-400">
                                {{ $invite->usesSummary() }} · {{ $invite->lifetimeLabel() }} · {{ $invite->created_at?->format('d M Y H:i') }}
                            </p>
                        </div>
                        @if ($inviteUsable)
                            <div class="flex gap-2">
                                <a href="{{ route('join.show', $invite->token) }}" target="_blank"
                                    class="rounded-lg border border-indigo-200 px-3 py-1.5 text-xs font-semibold text-indigo-600 hover:bg-indigo-50">Buka link</a>
                                <form method="POST" action="{{ route('groups.invite.revoke', [$group, $invite]) }}">
                                    @csrf
                                    <button class="rounded-lg border border-rose-200 px-3 py-1.5 text-xs font-semibold text-rose-600 hover:bg-rose-50">Revoke</button>
                                </form>
                            </div>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </section>
@endsection