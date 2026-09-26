@extends('layouts.app')

@section('title', $group->name)

@section('content')
    @php
        $groupSessions = $sessions->where('channel_id', null);
        $memberCount = $group->members->count();
        $channelCount = $group->channels->count();
    @endphp

    {{-- Header group --}}
    <div class="card p-6">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div class="min-w-0">
                <h1 class="page-title">{{ $group->name }}</h1>
                @if ($group->description)
                    <p class="mt-1 max-w-xl text-ink-muted">{{ $group->description }}</p>
                @endif
                <p class="mt-2 text-sm text-ink-subtle">
                    owner <strong class="text-ink">{{ $group->owner->name }}</strong> · {{ $memberCount }} orang · {{ $channelCount }} channel
                </p>
            </div>

            <div class="flex flex-wrap gap-2">
                @if ($can['manage_roles'])
                    <a href="{{ route('groups.manage', $group) }}" class="btn btn-secondary">Kelola</a>
                @endif
                <a href="{{ route('groups.treasury.show', $group) }}" class="btn btn-secondary">Kas Grup</a>
                @if ($can['create_patungan'])
                    <a href="{{ route('nongkrong.create', ['group' => $group->id]) }}" class="btn btn-primary">+ Patungan</a>
                @endif
            </div>
        </div>

        @if ($can['invite'])
            @php $invite = $group->activeInvite; @endphp
            <div class="mt-5 rounded-card border border-brand/30 bg-brand-soft/60 p-4">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <p class="text-sm font-semibold text-brand-text">Undang temen</p>
                    <form method="POST" action="{{ route('groups.invite.generate', $group) }}"
                        x-data="{ showOptions: false }"
                        class="flex flex-wrap items-center gap-2">
                        @csrf
                        <button type="button" @click="showOptions = !showOptions" class="btn btn-secondary btn-sm"
                            x-text="showOptions ? 'Sembunyikan opsi' : 'Opsi link'"></button>
                        <button class="btn btn-primary btn-sm">Bikin link baru</button>

                        <div x-show="showOptions" x-cloak class="mt-2 flex w-full flex-wrap gap-3 rounded-card bg-raised/70 p-3">
                            <label class="flex min-w-[10rem] flex-1 flex-col gap-1 text-xs font-medium text-brand-text">
                                Masa berlaku (hari, kosong = tanpa batas)
                                <input class="input input-sm" type="number" name="expires_days" min="1" max="365" placeholder="cth: 7">
                            </label>
                            <label class="flex min-w-[10rem] flex-1 flex-col gap-1 text-xs font-medium text-brand-text">
                                Batas pemakaian (orang, kosong = tanpa batas)
                                <input class="input input-sm" type="number" name="max_uses" min="1" placeholder="cth: 10">
                            </label>
                            <div class="w-full text-[11px] text-brand-text/70">
                                Link baru otomatis membatalkan link yang lagi aktif.
                            </div>
                        </div>
                    </form>
                </div>

                @php $inviteUsable = $invite && $invite->usable(); @endphp
                @if ($invite)
                    @php $inviteUrl = $inviteUsable ? route('join.show', $invite->token) : null; @endphp
                    <div x-data="{ copied: false }" class="mt-3">
                        @if ($inviteUsable)
                            <div class="flex flex-wrap items-center gap-2">
                                <code class="min-w-0 flex-1 truncate rounded-control bg-raised px-3 py-2 font-mono text-sm text-ink">{{ $inviteUrl }}</code>
                                <button type="button" @click="navigator.clipboard.writeText('{{ $inviteUrl }}'); copied = true; setTimeout(() => copied = false, 1500)"
                                    class="btn btn-primary btn-sm" x-text="copied ? 'Tersalin!' : 'Salin link'"></button>
                            </div>
                            <p class="mt-1.5 text-xs text-brand-text/80">
                                {{ $invite->usesSummary() }} · {{ $invite->lifetimeLabel() }} · yang lama langsung mati kalau link baru dibuat.
                            </p>
                        @else
                            <p class="rounded-control bg-raised px-3 py-2 text-sm text-ink-muted">
                                ⛔ Link aktif {{ $invite->invalidReason() ?? 'udah nggak bisa dipakai' }} — bikin link baru yuk.
                            </p>
                        @endif
                    </div>
                @else
                    <p class="mt-1 text-sm text-brand-text">Belum ada link aktif. Tinggal pencet tombol di atas.</p>
                @endif
            </div>
        @endif
    </div>

    {{-- Channel + patungan --}}
    <div class="mt-8 grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            @foreach ($channels as $channel)
                @php $channelSessions = $sessions->where('channel_id', $channel->id); @endphp
                <section class="card">
                    <div class="flex items-center justify-between gap-2">
                        <div class="min-w-0">
                            <h2 class="section-title flex items-center gap-2">
                                <span class="grid h-7 w-7 shrink-0 place-items-center rounded-control bg-brand-soft text-brand-text">#</span>
                                {{ $channel->name }}
                            </h2>
                            @if ($channel->description)
                                <p class="mt-0.5 text-sm text-ink-muted">{{ $channel->description }}</p>
                            @endif
                        </div>
                        @if ($can['create_patungan'])
                            <a href="{{ route('nongkrong.create', ['group' => $group->id, 'channel' => $channel->id]) }}"
                                class="btn btn-secondary btn-sm shrink-0">+ patungan</a>
                        @endif
                    </div>

                    @if ($channelSessions->isEmpty())
                        <p class="empty mt-3">Belum ada patungan di channel ini.</p>
                    @else
                        <div class="mt-3 space-y-2">
                            @foreach ($channelSessions as $session)
                                @include('nongkrong._session_row', ['session' => $session])
                            @endforeach
                        </div>
                    @endif
                </section>
            @endforeach

            @if ($can['create_channel'])
                <form method="POST" action="{{ route('groups.channels.store', $group) }}" class="empty-panel p-5">
                    @csrf
                    <label class="block text-sm font-semibold text-ink" for="channel-name">Bikin channel baru</label>
                    <div class="mt-2 flex flex-wrap gap-2">
                        <input class="input min-w-0 flex-1" id="channel-name" type="text" name="name" required maxlength="80" placeholder="cth: jajan-jajan">
                        <input class="input min-w-[10rem] flex-1" type="text" name="description" maxlength="255" placeholder="Deskripsi (opsional)">
                        <button class="btn btn-primary">Tambah</button>
                    </div>
                    @error('name')<p class="field-error">{{ $message }}</p>@enderror
                </form>
            @endif

            <section class="card">
                <h2 class="section-title">Patungan level group</h2>
                @if ($groupSessions->isEmpty())
                    <p class="empty mt-3">Belum ada patungan di sini.</p>
                @else
                    <div class="mt-3 space-y-2">
                        @foreach ($groupSessions as $session)
                            @include('nongkrong._session_row', ['session' => $session])
                        @endforeach
                    </div>
                @endif
            </section>
        </div>

        {{-- Member side panel --}}
        <aside class="card h-fit">
            <h2 class="section-title">Anggota</h2>
            <ul class="mt-3 space-y-2">
                @foreach ($group->members as $member)
                    <li class="flex items-center gap-2 text-sm">
                        <span class="avatar">{{ strtoupper(substr($member->user->name, 0, 1)) }}</span>
                        <div class="min-w-0">
                            <p class="truncate font-semibold text-ink">
                                {{ $member->user->name }}
                                @if ($member->user_id === $group->user_id)<span class="text-brand-text">(owner)</span>@endif
                            </p>
                            <p class="text-xs text-ink-subtle">{{ $member->role?->name ?? 'tanpa role' }}</p>
                        </div>
                    </li>
                @endforeach
            </ul>
        </aside>
    </div>
@endsection
