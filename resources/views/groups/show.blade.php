@extends('layouts.app')

@section('title', $group->name)

@section('content')
    @php
        $groupSessions = $sessions->where('channel_id', null);
        $memberCount = $group->members->count();
        $channelCount = $group->channels->count();
    @endphp

    {{-- Header group --}}
    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900">{{ $group->name }}</h1>
                @if ($group->description)
                    <p class="mt-1 max-w-xl text-slate-600">{{ $group->description }}</p>
                @endif
                <p class="mt-2 text-sm text-slate-400">
                    owner <strong>{{ $group->owner->name }}</strong> · {{ $memberCount }} orang · {{ $channelCount }} channel
                </p>
            </div>

            <div class="flex flex-wrap gap-2">
                @if ($can['manage_roles'])
                    <a href="{{ route('groups.manage', $group) }}" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Kelola</a>
                @endif
                <a href="{{ route('groups.treasury.show', $group) }}" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Kas Grup</a>
                @if ($can['create_patungan'])
                    <a href="{{ route('nongkrong.create', ['group' => $group->id]) }}" class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">+ Patungan</a>
                @endif
            </div>
        </div>

        @if ($can['invite'])
            @php $invite = $group->activeInvite; @endphp
            <div class="mt-5 rounded-2xl border border-indigo-200 bg-indigo-50/60 p-4">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <p class="text-sm font-semibold text-indigo-900">Undang temen</p>
                    <form method="POST" action="{{ route('groups.invite.generate', $group) }}"
                        x-data="{ showOptions: false }"
                        class="flex flex-wrap items-center gap-2">
                        @csrf
                        <button type="button" @click="showOptions = !showOptions"
                            class="rounded-lg border border-indigo-300 bg-white px-3 py-1.5 text-xs font-semibold text-indigo-700 hover:bg-indigo-100"
                            x-text="showOptions ? 'Sembunyikan opsi' : 'Opsi link'"></button>
                        <button class="rounded-lg bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-indigo-700">Bikin link baru</button>

                        <div x-show="showOptions" x-cloak class="mt-2 flex w-full flex-wrap gap-2 rounded-xl bg-white/70 p-3">
                            <label class="flex min-w-[10rem] flex-1 flex-col gap-1 text-xs font-medium text-indigo-900">
                                Masa berlaku (hari, kosong = tanpa batas)
                                <input type="number" name="expires_days" min="1" max="365" placeholder="cth: 7"
                                    class="rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
                            </label>
                            <label class="flex min-w-[10rem] flex-1 flex-col gap-1 text-xs font-medium text-indigo-900">
                                Batas pemakaian (orang, kosong = tanpa batas)
                                <input type="number" name="max_uses" min="1" placeholder="cth: 10"
                                    class="rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
                            </label>
                            <div class="w-full text-[11px] text-indigo-400">
                                Link baru otomatis membatalkan link yang lagi aktif.
                            </div>
                        </div>
                    </form>
                </div>

                @php $inviteUsable = $invite && $invite->usable(); @endphp
                @if ($invite)
                    @php $inviteUrl = $inviteUsable ? route('join.show', $invite->token) : null; @endphp
                    <div x-data="{ copied: false }" class="mt-2">
                        @if ($inviteUsable)
                            <div class="flex flex-wrap items-center gap-2">
                                <code class="min-w-0 flex-1 truncate rounded-lg bg-white px-3 py-2 text-sm text-slate-700">{{ $inviteUrl }}</code>
                                <button type="button" @click="navigator.clipboard.writeText('{{ $inviteUrl }}'); copied = true; setTimeout(() => copied = false, 1500)"
                                    class="rounded-lg bg-indigo-600 px-3 py-2 text-xs font-semibold text-white hover:bg-indigo-700"
                                    x-text="copied ? 'Tersalin!' : 'Salin link'"></button>
                            </div>
                            <p class="mt-1.5 text-xs text-indigo-500">
                                {{ $invite->usesSummary() }} · {{ $invite->lifetimeLabel() }} · yang lama langsung mati kalau link baru dibuat.
                            </p>
                        @else
                            <p class="rounded-lg bg-white px-3 py-2 text-sm text-slate-500">
                                ⛔ Link aktif {{ $invite->invalidReason() ?? 'udah nggak bisa dipakai' }} — bikin link baru yuk.
                            </p>
                        @endif
                    </div>
                @else
                    <p class="mt-1 text-sm text-indigo-700">Belum ada link aktif. Tinggal pencet tombol di atas.</p>
                @endif
            </div>
        @endif
    </div>

    {{-- Channel + patungan --}}
    <div class="mt-8 grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-6">
            @foreach ($channels as $channel)
                @php $channelSessions = $sessions->where('channel_id', $channel->id); @endphp
                <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex items-center justify-between gap-2">
                        <div>
                            <h2 class="flex items-center gap-2 font-bold text-slate-900">
                                <span class="grid h-7 w-7 place-items-center rounded-lg bg-slate-100 text-indigo-600">#</span>
                                {{ $channel->name }}
                            </h2>
                            @if ($channel->description)
                                <p class="mt-0.5 text-sm text-slate-500">{{ $channel->description }}</p>
                            @endif
                        </div>
                        @if ($can['create_patungan'])
                            <a href="{{ route('nongkrong.create', ['group' => $group->id, 'channel' => $channel->id]) }}"
                                class="shrink-0 rounded-lg border border-indigo-200 px-3 py-1.5 text-xs font-semibold text-indigo-600 hover:bg-indigo-50">+ patungan</a>
                        @endif
                    </div>

                    @if ($channelSessions->isEmpty())
                        <p class="mt-3 rounded-xl bg-slate-50 px-3 py-3 text-sm text-slate-400">Belum ada patungan di channel ini.</p>
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
                <form method="POST" action="{{ route('groups.channels.store', $group) }}" class="rounded-3xl border border-dashed border-slate-300 bg-white/60 p-5">
                    @csrf
                    <label class="block text-sm font-semibold text-slate-700">Bikin channel baru</label>
                    <div class="mt-2 flex flex-wrap gap-2">
                        <input type="text" name="name" required maxlength="80" placeholder="cth: jajan-jajan"
                            class="min-w-0 flex-1 rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
                        <input type="text" name="description" maxlength="255" placeholder="Deskripsi (opsional)"
                            class="min-w-[10rem] flex-1 rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
                        <button class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Tambah</button>
                    </div>
                    @error('name')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                </form>
            @endif

            <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="font-bold text-slate-900">Patungan level group</h2>
                @if ($groupSessions->isEmpty())
                    <p class="mt-3 rounded-xl bg-slate-50 px-3 py-3 text-sm text-slate-400">Belum ada patungan di sini.</p>
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
        <aside class="h-fit rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="font-bold text-slate-900">Anggota</h2>
            <ul class="mt-3 space-y-2">
                @foreach ($group->members as $member)
                    <li class="flex items-center gap-2 text-sm">
                        <span class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-indigo-100 font-bold text-indigo-600">{{ strtoupper(substr($member->user->name, 0, 1)) }}</span>
                        <div class="min-w-0">
                            <p class="truncate font-semibold text-slate-800">
                                {{ $member->user->name }}
                                @if ($member->user_id === $group->user_id)<span class="text-indigo-500">(owner)</span>@endif
                            </p>
                            <p class="text-xs text-slate-400">{{ $member->role?->name ?? 'tanpa role' }}</p>
                        </div>
                    </li>
                @endforeach
            </ul>
        </aside>
    </div>
@endsection