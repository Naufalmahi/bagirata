@extends('layouts.app')

@section('title', 'Kelola ' . $group->name)

@section('sidebar')
  @include('groups._sidebar')
@endsection

@section('content')
  <div class="p-4 sm:p-6 max-w-5xl mx-auto w-full space-y-6">
    <div class="mb-2">
      <a href="{{ route('groups.show', $group) }}" class="back-link text-[#A373D3] hover:underline">← Balik ke group</a>
      <h1 class="page-title mt-1 text-theme-main">Kelola {{ $group->name }}</h1>
    </div>

    {{-- Info group --}}
    <section class="glass-card rounded-3xl border border-theme-border p-5 sm:p-6 text-theme-main shadow-xl">
      <h2 class="section-title text-theme-main">Info group</h2>
      <form method="POST" action="{{ route('groups.update', $group) }}" enctype="multipart/form-data" class="mt-4 space-y-4">
        @csrf
        @method('PATCH')

        {{-- Avatar Grup --}}
        <div class="flex items-center gap-4 p-4 rounded-2xl bg-theme-elevated border border-theme-border">
          <div class="shrink-0">
            @if ($group->avatar_path)
              <img src="{{ asset('storage/' . $group->avatar_path) }}" alt="{{ $group->name }}" class="h-14 w-14 rounded-2xl object-cover ring-1 ring-theme-border">
            @else
              <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-tr from-[#5D549C] to-[#7E3A63] text-xl font-black text-white shadow-sm">
                {{ strtoupper(substr($group->name, 0, 1)) }}
              </div>
            @endif
          </div>
          <div class="flex-1 min-w-0">
            <label class="block text-xs font-bold uppercase tracking-wider text-theme-muted mb-1" for="avatar">Ganti Foto Profil Grup</label>
            <input type="file" id="avatar" name="avatar" accept="image/*" class="block w-full text-xs text-theme-muted file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#5D549C] file:text-white hover:file:bg-[#7E3A63] cursor-pointer">
            <p class="text-[11px] text-theme-subtle mt-1">Format: JPG, PNG, WEBP (maks. 2MB)</p>
            @error('avatar')<p class="text-xs text-[#EB9998] mt-1">{{ $message }}</p>@enderror
          </div>
        </div>

        <div class="grid gap-3 sm:grid-cols-2">
          <div>
            <label class="label text-theme-main" for="group-name">Nama</label>
            <input class="input" id="group-name" type="text" name="name" value="{{ old('name', $group->name) }}" required maxlength="60">
            @error('name')<p class="field-error text-[#EB9998]">{{ $message }}</p>@enderror
          </div>
          <div>
            <label class="label text-theme-main" for="group-desc">Deskripsi</label>
            <input class="input" id="group-desc" type="text" name="description" value="{{ old('description', $group->description) }}" maxlength="500">
            @error('description')<p class="field-error text-[#EB9998]">{{ $message }}</p>@enderror
          </div>
        </div>

        {{-- Visibility --}}
        <div>
          <label class="label text-theme-main">Tipe Akses Grup</label>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <label class="flex items-center gap-3 rounded-2xl border border-theme-border bg-theme-elevated p-3 cursor-pointer transition hover:border-[#A373D3] has-[:checked]:border-[#A373D3] has-[:checked]:bg-[#5D549C]/20">
              <input type="radio" name="visibility" value="public" class="hidden peer" {{ old('visibility', $group->visibility ?? 'public') === 'public' ? 'checked' : '' }}>
              <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <div>
                <p class="text-xs font-bold text-theme-main">Public</p>
                <p class="text-[10px] text-theme-subtle">Bisa dicari di web</p>
              </div>
            </label>
            <label class="flex items-center gap-3 rounded-2xl border border-theme-border bg-theme-elevated p-3 cursor-pointer transition hover:border-[#A373D3] has-[:checked]:border-[#A373D3] has-[:checked]:bg-[#5D549C]/20">
              <input type="radio" name="visibility" value="private" class="hidden peer" {{ old('visibility', $group->visibility) === 'private' ? 'checked' : '' }}>
              <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-amber-500/20 text-amber-400 border border-amber-500/30">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
              </div>
              <div>
                <p class="text-xs font-bold text-theme-main">Private</p>
                <p class="text-[10px] text-theme-subtle">Hanya via link undangan</p>
              </div>
            </label>
          </div>
        </div>

        <button class="pushable pushable-sm">
          <span class="shadow"></span>
          <span class="edge"></span>
          <span class="front">Simpan Info Grup</span>
        </button>
      </form>
    </section>

    {{-- Channels manager --}}
    @if (!empty($can['create_channel']))
      <section class="glass-card rounded-3xl border border-theme-border p-5 sm:p-6 text-theme-main shadow-xl">
        <h2 class="section-title text-theme-main">Channel Diskusi ({{ $group->channels->count() }})</h2>
        <p class="muted text-xs text-theme-muted mt-0.5">Kelola saluran obrolan tongkrongan untuk grup ini</p>

        <div class="mt-4 space-y-2">
          @foreach ($group->channels as $channel)
            <div class="flex items-center justify-between p-3 rounded-2xl bg-theme-elevated border border-theme-border">
              <div class="flex items-center gap-2 truncate">
                <span class="font-bold text-[#5D549C] dark:text-[#EB9998]">#</span>
                <span class="font-bold text-xs sm:text-sm text-theme-main truncate">{{ $channel->name }}</span>
                @if ($channel->description)
                  <span class="hidden sm:inline text-xs text-theme-subtle truncate max-w-xs">· {{ $channel->description }}</span>
                @endif
              </div>
              <div class="flex items-center gap-2 shrink-0">
                <span class="text-[10px] px-2 py-0.5 rounded-full bg-theme-card border border-theme-border text-theme-muted font-bold">
                  {{ $channel->messages()->count() }} chat
                </span>
                @if ($group->channels->count() > 1)
                  <form method="POST" action="{{ route('groups.channels.destroy', [$group, $channel]) }}" onsubmit="return confirm('Hapus channel #{{ $channel->name }}?')">
                    @csrf
                    @method('DELETE')
                    <button class="p-1 rounded-lg hover:bg-rose-500/20 text-[#EB9998] transition" title="Hapus channel">
                      <i class="ph-bold ph-trash text-sm"></i>
                    </button>
                  </form>
                @endif
              </div>
            </div>
          @endforeach
        </div>
      </section>
    @endif

    {{-- Role + permission --}}
    <section class="glass-card rounded-3xl border border-theme-border p-5 sm:p-6 text-theme-main shadow-xl">
      <h2 class="section-title text-theme-main">Role & Peran Anggota</h2>

      @if ($group->roles->isEmpty())
        <p class="empty mt-2 text-theme-muted">Belum ada role tambahan.</p>
      @else
        <ul class="mt-3 space-y-3">
          @foreach ($group->roles as $role)
            @if ($role->is_system)
              <li class="rounded-2xl bg-theme-elevated border border-theme-border px-4 py-3">
                <p class="font-semibold text-theme-main text-xs sm:text-sm">
                  {{ $role->name }}
                  <span class="text-xs font-normal text-theme-subtle">(bawaan sistem · {{ $role->members_count ?? $role->members->count() }} anggota)</span>
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
              <li class="rounded-2xl border border-theme-border bg-theme-elevated p-4">
                <div class="mb-3 flex items-center justify-between gap-2">
                  <p class="font-semibold text-theme-main">{{ $role->name }}</p>
                  <form method="POST" action="{{ route('groups.roles.destroy', [$group, $role]) }}"
                    onsubmit="return confirm('Hapus role {{ $role->name }}?')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-ghost btn-sm text-[#EB9998] hover:bg-rose-500/20">Hapus</button>
                  </form>
                </div>
                <form method="POST" action="{{ route('groups.roles.update', [$group, $role]) }}" class="space-y-3">
                  @csrf
                  @method('PATCH')
                  <input class="input" type="text" name="name" value="{{ $role->name }}" required maxlength="60">
                  <div class="grid gap-1.5 grid-cols-1 sm:grid-cols-2">
                    @foreach ($permissionGroups as $perm)
                      <label class="flex items-start gap-2 rounded-xl border border-theme-border bg-theme-card px-3 py-2 text-xs text-theme-main has-[:checked]:border-[#A373D3] has-[:checked]:bg-[#5D549C]/20">
                        <input class="checkbox mt-0.5" type="checkbox" name="permissions[]" value="{{ $perm->value }}"
                          @checked(in_array($perm->value, $role->permissions ?? []))>
                        <span class="text-theme-main">{{ $perm->label() }}</span>
                      </label>
                    @endforeach
                  </div>
                  <button class="btn btn-primary btn-sm">Simpan Role</button>
                </form>
              </li>
            @endif
          @endforeach
        </ul>
      @endif

      {{-- Create role --}}
      <div class="mt-5 border-t border-theme-border pt-4">
        <h3 class="font-semibold text-theme-main text-xs sm:text-sm">Bikin Role Baru</h3>
        <form method="POST" action="{{ route('groups.roles.store', $group) }}" class="mt-3 space-y-3">
          @csrf
          <input class="input" type="text" name="name" required maxlength="60" placeholder="cth: Bendahara">
          <div class="grid gap-1.5 grid-cols-1 sm:grid-cols-2">
            @foreach ($permissionGroups as $perm)
              <label class="flex items-start gap-2 rounded-xl border border-theme-border bg-theme-card px-3 py-2 text-xs text-theme-main has-[:checked]:border-[#A373D3] has-[:checked]:bg-[#5D549C]/20">
                <input class="checkbox mt-0.5" type="checkbox" name="permissions[]" value="{{ $perm->value }}">
                <span class="text-theme-main">{{ $perm->label() }}</span>
              </label>
            @endforeach
          </div>
          <button class="btn btn-primary btn-sm">Bikin Role</button>
        </form>
      </div>
    </section>

    {{-- Member role assignment --}}
    <section class="glass-card rounded-3xl border border-theme-border p-5 sm:p-6 text-theme-main shadow-xl">
      <h2 class="section-title text-theme-main">Kasih Role ke Anggota</h2>
      <ul class="divide-y divide-theme-border mt-3">
        @foreach ($group->members as $member)
          <li class="flex flex-wrap items-center justify-between gap-3 py-3">
            <div class="flex min-w-0 items-center gap-2.5 text-sm">
              <span class="avatar">{{ strtoupper(substr($member->user->name, 0, 1)) }}</span>
              <span class="truncate font-semibold text-theme-main">{{ $member->user->name }}</span>
            </div>
            @if ($member->user_id === $group->user_id)
              <span class="badge badge-brand">Owner</span>
            @else
              <div class="flex flex-wrap items-center gap-2">
                <form method="POST" action="{{ route('groups.members.role', [$group, $member]) }}" class="flex items-center gap-2">
                  @csrf
                  <input type="hidden" name="user_id" value="{{ $member->user_id }}">
                  <select class="select select-sm" name="role_id">
                    @foreach ($group->roles as $role)
                      <option value="{{ $role->id }}" @selected($member->role_id === $role->id)>{{ $role->name }}</option>
                    @endforeach
                  </select>
                  <button class="btn btn-secondary btn-sm">Ganti Role</button>
                </form>

                <form method="POST" action="{{ route('groups.kick', $group) }}" onsubmit="return confirm('Keluarkan {{ $member->user->name }} dari grup ini?')">
                  @csrf
                  <input type="hidden" name="user_id" value="{{ $member->user_id }}">
                  <button type="submit" class="btn btn-ghost btn-sm text-[#EB9998] hover:bg-rose-500/20" title="Keluarkan anggota">
                    <i class="ph-bold ph-user-minus text-sm"></i>
                    <span class="hidden sm:inline">Kick</span>
                  </button>
                </form>
              </div>
            @endif
          </li>
        @endforeach
      </ul>
    </section>

    {{-- Invites history --}}
    <section class="glass-card rounded-3xl border border-theme-border p-5 sm:p-6 text-theme-main shadow-xl">
      <h2 class="section-title text-theme-main">Riwayat Undangan</h2>
      @if ($group->invites->isEmpty())
        <p class="empty mt-2 text-theme-muted">Belum pernah bikin undangan.</p>
      @else
        <ul class="divide-y divide-theme-border mt-3">
          @foreach ($group->invites->sortByDesc('created_at') as $invite)
            @php $inviteUsable = $invite->usable(); @endphp
            <li class="flex flex-wrap items-center justify-between gap-3 py-3 text-sm">
              <div class="min-w-0">
                <p class="flex flex-wrap items-center gap-2">
                  <code class="truncate font-mono text-xs text-theme-subtle">{{ $inviteUsable ? route('join.show', $invite->token) : $invite->token }}</code>
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
                <p class="text-xs text-theme-subtle mt-1">
                  {{ $invite->usesSummary() }} · {{ $invite->lifetimeLabel() }} · {{ $invite->created_at?->format('d M Y H:i') }}
                </p>
              </div>
              @if ($inviteUsable)
                <div class="flex gap-2">
                  <a href="{{ route('join.show', $invite->token) }}" target="_blank" rel="noopener" class="btn btn-secondary btn-sm">Buka Link</a>
                  <form method="POST" action="{{ route('groups.invite.revoke', [$group, $invite]) }}">
                    @csrf
                    <button class="btn btn-ghost btn-sm text-[#EB9998] hover:bg-rose-500/20">Revoke</button>
                  </form>
                </div>
              @endif
            </li>
          @endforeach
        </ul>
      @endif
    </section>
  </div>
@endsection
