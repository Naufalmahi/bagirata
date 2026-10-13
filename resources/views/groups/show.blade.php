@extends('layouts.app')

@section('title', $group->name . ' · Circle')

@section('content')
@php
    $memberCount = $group->members->count();
    $channelCount = $channels->count();
    $activeChannel = $activeChannel ?? $channels->first();
    $channelSessions = $activeChannel ? $sessions->where('channel_id', $activeChannel->id) : collect();
    $groupLevelSessions = $sessions->where('channel_id', null);
@endphp

<div class="space-y-6">
    {{-- Discord Server Banner / Header --}}
    <div class="overflow-hidden rounded-3xl border border-slate-200/90 bg-white shadow-sm">
        <div class="bg-gradient-to-r from-purple-700 via-indigo-600 to-blue-600 px-6 py-5 sm:px-8 text-white">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-white/20 text-2xl font-black text-white shadow-inner backdrop-blur-md ring-2 ring-white/30">
                        {{ strtoupper(substr($group->name, 0, 1)) }}
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h1 class="text-xl sm:text-2xl font-black tracking-tight text-white">{{ $group->name }}</h1>
                            <span class="rounded-full bg-white/20 px-2 py-0.5 text-[11px] font-bold text-indigo-100">
                                {{ $memberCount }} Member
                            </span>
                        </div>
                        <p class="text-xs sm:text-sm text-indigo-100/90 mt-0.5">
                            {{ $group->description ?? 'Circle nongkrong & patungan santai tanpa drama.' }}
                        </p>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('groups.treasury.show', $group) }}" class="inline-flex items-center gap-1.5 rounded-xl bg-white/20 px-3.5 py-2 text-xs font-bold text-white backdrop-blur-sm hover:bg-white/30 transition">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Kas Grup</span>
                    </a>

                    @if ($can['manage_roles'])
                        <a href="{{ route('groups.manage', $group) }}" class="inline-flex items-center gap-1.5 rounded-xl bg-white/20 px-3.5 py-2 text-xs font-bold text-white backdrop-blur-sm hover:bg-white/30 transition">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>Kelola</span>
                        </a>
                    @endif

                    @if ($can['create_patungan'])
                        <a href="{{ route('nongkrong.create', ['group' => $group->id, 'channel' => $activeChannel?->id]) }}" class="inline-flex items-center gap-1.5 rounded-xl bg-white px-4 py-2 text-xs font-bold text-indigo-700 shadow-md hover:bg-indigo-50 transition">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                            </svg>
                            <span>+ Patungan</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- Invite Box Quick Bar --}}
        @if ($can['invite'])
            @php $invite = $group->activeInvite; @endphp
            <div class="border-t border-slate-100 bg-slate-50/70 px-6 py-2.5 sm:px-8 text-xs flex flex-wrap items-center justify-between gap-2" x-data="{ copied: false }">
                <div class="flex items-center gap-2 text-slate-600">
                    <svg class="h-4 w-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                    </svg>
                    <span>Undang teman ke grup:</span>
                    @if ($invite && $invite->usable())
                        <code class="rounded bg-white px-2 py-0.5 font-mono text-[11px] text-slate-800 border border-slate-200">{{ route('join.show', $invite->token) }}</code>
                    @else
                        <span class="text-slate-400 italic">Belum ada link aktif</span>
                    @endif
                </div>

                <div class="flex items-center gap-2">
                    @if ($invite && $invite->usable())
                        <button type="button" @click="navigator.clipboard.writeText('{{ route('join.show', $invite->token) }}'); copied = true; setTimeout(() => copied = false, 2000)" class="font-bold text-indigo-600 hover:text-indigo-800">
                            <span x-text="copied ? '✓ Tersalin!' : 'Salin Link'">Salin Link</span>
                        </button>
                    @endif
                    <form method="POST" action="{{ route('groups.invite.generate', $group) }}">
                        @csrf
                        <button class="font-semibold text-slate-500 hover:text-slate-800">Bikin Link Baru</button>
                    </form>
                </div>
            </div>
        @endif
    </div>

    {{-- DISCORD-STYLE 3-COLUMN INTERFACE --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
        {{-- ========================================================
             KOLOM KIRI: Sub-Grup / Channels Sidebar Ala Discord
             ======================================================== --}}
        <div class="lg:col-span-3 space-y-4">
            <div class="rounded-3xl border border-slate-200/90 bg-white p-4 shadow-sm" x-data="{ newChannelOpen: false }">
                {{-- Header Channels --}}
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <span class="text-xs font-black uppercase tracking-wider text-slate-400">
                        Channels / Sub-Grup
                    </span>
                    @if ($can['create_channel'])
                        <button type="button" @click="newChannelOpen = !newChannelOpen" class="flex h-6 w-6 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-100 transition" title="Bikin Sub-Grup / Channel Baru">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                            </svg>
                        </button>
                    @endif
                </div>

                {{-- Form Bikin Sub-Grup Baru (Discord Style) --}}
                <div x-show="newChannelOpen" x-cloak class="mt-3 p-3 rounded-2xl bg-slate-50 border border-slate-200">
                    <form method="POST" action="{{ route('groups.channels.store', $group) }}" class="space-y-2">
                        @csrf
                        <label class="block text-[11px] font-bold text-slate-700" for="channel_name">Nama Sub-Grup / Channel</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-2.5 text-xs text-slate-400 font-bold">#</span>
                            <input class="w-full rounded-xl border border-slate-300 bg-white pl-6 pr-2 py-1.5 text-xs text-slate-800 placeholder:text-slate-400 focus:border-indigo-500 focus:outline-none"
                                id="channel_name" type="text" name="name" required placeholder="cth: jalan-jalan" maxlength="80">
                        </div>
                        <input class="w-full rounded-xl border border-slate-300 bg-white px-2.5 py-1.5 text-xs text-slate-800 placeholder:text-slate-400 focus:border-indigo-500 focus:outline-none"
                            type="text" name="description" placeholder="Deskripsi singkat (opsional)" maxlength="255">
                        <div class="flex items-center justify-end gap-1.5 pt-1">
                            <button type="button" @click="newChannelOpen = false" class="rounded-lg px-2.5 py-1 text-[11px] font-semibold text-slate-500 hover:bg-slate-200">Batal</button>
                            <button type="submit" class="rounded-lg bg-indigo-600 px-3 py-1 text-[11px] font-bold text-white hover:bg-indigo-700 shadow-xs">Tambah</button>
                        </div>
                    </form>
                </div>

                {{-- Channel List --}}
                <nav class="mt-3 space-y-1">
                    @foreach ($channels as $ch)
                        @php $isActive = $activeChannel && $activeChannel->id === $ch->id; @endphp
                        <a href="{{ route('groups.show', ['group' => $group, 'channel' => $ch->id]) }}"
                            @class([
                                'group flex items-center justify-between rounded-2xl px-3 py-2 text-xs font-bold transition-all',
                                'bg-gradient-to-r from-purple-600 via-indigo-600 to-blue-600 text-white shadow-md shadow-indigo-200' => $isActive,
                                'text-slate-600 hover:bg-slate-50 hover:text-slate-900' => !$isActive,
                            ])>
                            <div class="flex items-center gap-2 truncate">
                                <span class="{{ $isActive ? 'text-indigo-200' : 'text-slate-400 group-hover:text-indigo-600' }} font-bold text-sm">#</span>
                                <span class="truncate">{{ $ch->name }}</span>
                            </div>
                            <span @class([
                                'rounded-full px-1.5 py-0.2 text-[10px] font-semibold',
                                'bg-white/20 text-white' => $isActive,
                                'bg-slate-100 text-slate-500' => !$isActive,
                            ])>
                                {{ $sessions->where('channel_id', $ch->id)->count() }} bill
                            </span>
                        </a>
                    @endforeach
                </nav>
            </div>

            {{-- Patungan Tanpa Channel (Level Group) --}}
            <div class="rounded-3xl border border-slate-200/90 bg-white p-4 shadow-sm text-xs">
                <span class="text-xs font-black uppercase tracking-wider text-slate-400 block mb-2">
                    Patungan Umum Group
                </span>
                <p class="text-slate-500 text-[11px]">Ada {{ $groupLevelSessions->count() }} patungan di luar channel.</p>
                @if ($groupLevelSessions->isNotEmpty())
                    <div class="mt-2 space-y-1.5">
                        @foreach ($groupLevelSessions->take(3) as $s)
                            <a href="{{ route('nongkrong.show', $s) }}" class="block truncate rounded-xl bg-slate-50 px-2.5 py-1.5 font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-600">
                                {{ $s->name }} · Rp{{ number_format($s->total_amount, 0, ',', '.') }}
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- ========================================================
             KOLOM TENGAH: Chat & Patungan Main Area Ala Discord
             ======================================================== --}}
        <div class="lg:col-span-6 space-y-4" x-data="{ activeTab: 'chat' }">
            <div class="rounded-3xl border border-slate-200/90 bg-white shadow-sm overflow-hidden">
                {{-- Header Channel Aktif & Tab Switcher --}}
                <div class="border-b border-slate-100 px-5 py-3.5 flex flex-wrap items-center justify-between gap-3 bg-white">
                    <div class="flex items-center gap-2">
                        <span class="text-xl font-black text-indigo-600">#</span>
                        <div>
                            <h2 class="text-base font-black text-slate-900 leading-tight">
                                {{ $activeChannel?->name ?? 'general' }}
                            </h2>
                            <p class="text-[11px] text-slate-400 truncate max-w-xs">
                                {{ $activeChannel?->description ?? 'Channel diskusi & patungan bersama.' }}
                            </p>
                        </div>
                    </div>

                    {{-- Tab Switcher: Chat Diskusi vs Patungan --}}
                    <div class="flex items-center rounded-xl bg-slate-100 p-1 text-xs font-bold">
                        <button type="button" @click="activeTab = 'chat'"
                            :class="activeTab === 'chat' ? 'bg-white text-indigo-700 shadow-xs' : 'text-slate-500 hover:text-slate-800'"
                            class="flex items-center gap-1.5 rounded-lg px-3 py-1.5 transition">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            <span>Chat</span>
                        </button>

                        <button type="button" @click="activeTab = 'patungan'"
                            :class="activeTab === 'patungan' ? 'bg-white text-indigo-700 shadow-xs' : 'text-slate-500 hover:text-slate-800'"
                            class="flex items-center gap-1.5 rounded-lg px-3 py-1.5 transition">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Patungan ({{ $channelSessions->count() }})</span>
                        </button>
                    </div>
                </div>

                {{-- TAB 1: Chat Diskusi (Discord Style) --}}
                <div x-show="activeTab === 'chat'" class="flex flex-col h-[520px]">
                    {{-- Messages Scrollable Area --}}
                    <div id="chat-messages-container" class="flex-1 overflow-y-auto p-4 sm:p-5 space-y-4">
                        @if ($messages->isEmpty())
                            <div class="flex flex-col items-center justify-center h-full text-center text-slate-400 p-6">
                                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-500 mb-3">
                                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                    </svg>
                                </div>
                                <h3 class="text-sm font-bold text-slate-700">Belum ada obrolan di #{{ $activeChannel?->name }}</h3>
                                <p class="text-xs text-slate-500 mt-1">Jadilah yang pertama mengirim pesan atau menyapa circle nongkrong kamu!</p>
                            </div>
                        @else
                            @foreach ($messages as $msg)
                                <div class="flex items-start gap-3 group hover:bg-slate-50/70 p-2 rounded-2xl transition">
                                    {{-- Avatar --}}
                                    @if ($msg->user->avatar_path)
                                        <img src="{{ asset('storage/' . $msg->user->avatar_path) }}" alt="{{ $msg->user->name }}" class="h-9 w-9 rounded-xl object-cover shrink-0 ring-1 ring-slate-200">
                                    @else
                                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-gradient-to-tr from-purple-600 to-indigo-600 text-xs font-bold text-white shadow-xs">
                                            {{ strtoupper(substr($msg->user->name, 0, 1)) }}
                                        </div>
                                    @endif

                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-baseline gap-2">
                                            <a href="{{ route('profile.user', $msg->user) }}" class="text-xs font-bold text-slate-900 hover:text-indigo-600 hover:underline">
                                                {{ $msg->user->name }}
                                            </a>
                                            @if ($msg->user_id === $group->user_id)
                                                <span class="rounded bg-indigo-100 px-1 py-0.2 text-[9px] font-black text-indigo-700 uppercase">Owner</span>
                                            @endif
                                            <span class="text-[10px] text-slate-400">
                                                {{ $msg->created_at->format('H:i') }}
                                            </span>
                                        </div>
                                        <p class="mt-1 text-xs text-slate-800 leading-relaxed break-words">
                                            {{ $msg->body }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>

                    {{-- Form Input Chat (Discord Style) --}}
                    @if ($activeChannel)
                        <div class="border-t border-slate-100 p-3 bg-slate-50/50">
                            <form method="POST" action="{{ route('groups.channels.messages.store', ['group' => $group, 'channel' => $activeChannel]) }}" class="flex items-center gap-2">
                                @csrf
                                <input class="flex-1 rounded-2xl border border-slate-300 bg-white px-4 py-2.5 text-xs text-slate-800 placeholder:text-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                                    type="text" name="body" required placeholder="Kirim pesan ke #{{ $activeChannel->name }}..." maxlength="1000" autocomplete="off">
                                <button type="submit" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-r from-purple-600 via-indigo-600 to-blue-600 text-white shadow-md hover:from-purple-700 hover:to-blue-700 transition" title="Kirim">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    @endif
                </div>

                {{-- TAB 2: Patungan & Nongkrong di Channel Ini --}}
                <div x-show="activeTab === 'patungan'" x-cloak class="p-5 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">
                            Daftar Bill di #{{ $activeChannel?->name }}
                        </span>
                        @if ($can['create_patungan'])
                            <a href="{{ route('nongkrong.create', ['group' => $group->id, 'channel' => $activeChannel?->id]) }}" class="rounded-xl bg-indigo-600 px-3 py-1.5 text-xs font-bold text-white hover:bg-indigo-700 transition">
                                + Patungan Baru
                            </a>
                        @endif
                    </div>

                    @if ($channelSessions->isEmpty())
                        <div class="rounded-2xl border border-dashed border-slate-200 p-8 text-center text-slate-400 text-xs">
                            Belum ada patungan di channel ini. Tekan tombol "+ Patungan Baru" untuk membuat sesi pertama!
                        </div>
                    @else
                        <div class="space-y-2.5">
                            @foreach ($channelSessions as $session)
                                @include('nongkrong._session_row', ['session' => $session])
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ========================================================
             KOLOM KANAN: Member List Ala Discord
             ======================================================== --}}
        <div class="lg:col-span-3 space-y-4">
            <div class="rounded-3xl border border-slate-200/90 bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <span class="text-xs font-black uppercase tracking-wider text-slate-400">
                        Anggota Circle ({{ $memberCount }})
                    </span>
                </div>

                <ul class="mt-3 space-y-2.5">
                    @foreach ($group->members as $member)
                        <li>
                            <a href="{{ route('profile.user', $member->user) }}" class="flex items-center gap-2.5 p-1.5 rounded-2xl hover:bg-slate-50 transition group">
                                <div class="relative shrink-0">
                                    @if ($member->user->avatar_path)
                                        <img src="{{ asset('storage/' . $member->user->avatar_path) }}" alt="{{ $member->user->name }}" class="h-8 w-8 rounded-xl object-cover ring-1 ring-slate-200">
                                    @else
                                        <div class="flex h-8 w-8 items-center justify-center rounded-xl bg-gradient-to-tr from-purple-500 to-indigo-600 text-xs font-bold text-white">
                                            {{ strtoupper(substr($member->user->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <span class="absolute -bottom-0.5 -right-0.5 h-2.5 w-2.5 rounded-full border border-white bg-emerald-500"></span>
                                </div>

                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-xs font-bold text-slate-800 group-hover:text-indigo-600 transition-colors">
                                        {{ $member->user->name }}
                                    </p>
                                    <div class="flex items-center gap-1">
                                        @if ($member->user_id === $group->user_id)
                                            <span class="text-[10px] font-extrabold text-indigo-600">Owner</span>
                                        @elseif ($member->role)
                                            <span class="text-[10px] text-slate-400">{{ $member->role->name }}</span>
                                        @else
                                            <span class="text-[10px] text-slate-400">Member</span>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const container = document.getElementById('chat-messages-container');
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    });
</script>
@endsection
