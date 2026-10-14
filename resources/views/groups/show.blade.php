@extends('layouts.app')

@section('title', '#' . ($activeChannel?->name ?? 'general') . ' · ' . $group->name)

@php
  $memberCount = $group->members->count();
  $channelCount = $channels->count();
  $activeChannel = $activeChannel ?? $channels->first();
  $channelSessions = $activeChannel ? $sessions->where('channel_id', $activeChannel->id) : collect();
  $groupLevelSessions = $sessions->where('channel_id', null);
@endphp

{{-- ========================================================
   KOLOM 2: CHANNELS & SUB-GRUP SIDEBAR (ALA DISCORD ASLI)
   ======================================================== --}}
@section('sidebar')
  @include('groups._sidebar')
@endsection


{{-- ========================================================
   HEADER BAR ATAS CHAT (ALA DISCORD CHANNEL HEADER)
   ======================================================== --}}
@section('header-left')
  <div class="flex items-center gap-3">
    <div class="flex items-center gap-2">
      <span class="text-xl font-bold text-[#EB9998]">#</span>
      <span class="text-sm sm:text-base font-black text-white tracking-wide">
        {{ $activeChannel?->name ?? 'general' }}
      </span>
    </div>
    @if ($activeChannel?->description)
      <span class="hidden md:inline-block text-xs text-[#6F70A2] border-l border-white/10 pl-3 truncate max-w-sm">
        {{ $activeChannel->description }}
      </span>
    @endif
  </div>
@endsection


{{-- ========================================================
   KOLOM 3: MAIN CHAT & PATUNGAN STREAM (FULL VIEWPORT)
   ======================================================== --}}
@section('content')
<div class="flex flex-col h-full min-h-0 flex-1 w-full overflow-hidden bg-[#110b17]" x-data="{ activeTab: 'chat' }">
  
  <!-- Top Channel Sub-bar (Tab Switcher + Member Toggle) -->
  <div class="border-b border-white/5 px-4 sm:px-6 py-2.5 flex items-center justify-between gap-3 bg-[#181224]/80 backdrop-blur-md shrink-0">
    <!-- Tabs: Chat vs Patungan -->
    <div class="flex items-center rounded-xl bg-[#110b17] border border-white/5 p-1 text-xs font-bold">
      <button type="button" @click="activeTab = 'chat'"
        :class="activeTab === 'chat' ? 'bg-[#5D549C] text-white shadow-sm' : 'text-[#6F70A2] hover:text-white'"
        class="flex items-center gap-1.5 rounded-lg px-3 py-1.5 transition">
        <i class="ph-bold ph-chat-circle-dots text-sm"></i>
        <span>Chat</span>
      </button>

      <button type="button" @click="activeTab = 'patungan'"
        :class="activeTab === 'patungan' ? 'bg-[#5D549C] text-white shadow-sm' : 'text-[#6F70A2] hover:text-white'"
        class="flex items-center gap-1.5 rounded-lg px-3 py-1.5 transition">
        <i class="ph-bold ph-receipt text-sm"></i>
        <span>Patungan ({{ $channelSessions->count() }})</span>
      </button>
    </div>

    <!-- Tombol Info Channel & Anggota (Toggle Sidebar Kanan) -->
    <div class="flex items-center gap-2">
      <button type="button"
          @click="rightSidebarOpen = !rightSidebarOpen"
          :class="rightSidebarOpen ? 'bg-white/10 text-[#EB9998] border-[#EB9998]/40' : 'bg-white/5 text-[#a19db0] hover:text-white border-white/10'"
          class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl border text-xs font-bold transition shadow-sm"
          title="Buka / Tutup Daftar Anggota & Info">
        <i class="ph-bold ph-users text-sm"></i>
        <span>Anggota ({{ $memberCount }})</span>
      </button>
    </div>
  </div>

  <!-- TAB 1: CHAT DISKUSI (ALA DISCORD REAL) -->
  <div x-show="activeTab === 'chat'" class="flex-1 flex flex-col overflow-hidden relative">
    
    <!-- Messages Scrollable Stream -->
    <div id="chat-messages-container" class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-4 custom-scrollbar">
      @if ($messages->isEmpty())
        <div class="flex flex-col items-center justify-center h-full text-center p-8 my-auto">
          <div class="w-16 h-16 rounded-3xl bg-[#5D549C]/25 border border-[#A373D3]/30 flex items-center justify-center text-[#A373D3] mb-4 shadow-inner">
            <i class="ph-fill ph-chats-circle text-3xl"></i>
          </div>
          <h3 class="text-base font-bold text-white">Selamat datang di #{{ $activeChannel?->name }}!</h3>
          <p class="text-xs text-[#a19db0] mt-1 max-w-sm leading-relaxed">
            Ini adalah awal dari channel #{{ $activeChannel?->name }}. Sapa teman nongkrong kamu atau mulai obrolan patungan!
          </p>
        </div>
      @else
        @foreach ($messages as $msg)
          <div class="flex items-start gap-3 group hover:bg-white/5 p-2 rounded-2xl transition">
            {{-- Avatar Pengirim --}}
            <div class="shrink-0 pt-0.5">
              @if ($msg->user->avatar_path)
                <img src="{{ asset('storage/' . $msg->user->avatar_path) }}" alt="{{ $msg->user->name }}" class="w-10 h-10 rounded-2xl object-cover ring-1 ring-white/10">
              @else
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-[#5D549C] to-[#7E3A63] flex items-center justify-center text-xs font-black text-white shadow-sm">
                  {{ strtoupper(substr($msg->user->name, 0, 1)) }}
                </div>
              @endif
            </div>

            {{-- Bubble / Konten Pesan --}}
            <div class="flex-1 min-w-0">
              <div class="flex items-baseline gap-2">
                <a href="{{ route('profile.user', $msg->user) }}" class="text-xs font-bold text-white hover:text-[#A373D3] transition">
                  {{ $msg->user->name }}
                </a>
                @if ($msg->user_id === $group->user_id)
                  <span class="rounded-md bg-[#EB9998]/20 border border-[#EB9998]/30 px-1.5 py-0.2 text-[9px] font-black text-[#EB9998] uppercase">
                    Owner
                  </span>
                @endif
                <span class="text-[10px] text-[#6F70A2]">
                  {{ $msg->created_at->format('H:i') }}
                </span>
              </div>
              <div class="mt-1 p-3 rounded-2xl bg-[#181224] border border-white/5 text-xs sm:text-sm text-slate-100 leading-relaxed break-words max-w-2xl">
                {{ $msg->body }}
              </div>
            </div>
          </div>
        @endforeach
      @endif
    </div>

    <!-- Sticky Chat Input Bar (Persis Discord Input Box) -->
    @if ($activeChannel)
      <div class="border-t border-white/5 p-3 sm:p-4 bg-[#181224] shrink-0">
        <form method="POST" action="{{ route('groups.channels.messages.store', ['group' => $group, 'channel' => $activeChannel]) }}" class="flex items-center gap-2">
          @csrf
          <div class="relative flex-1">
            <input class="w-full rounded-2xl border border-white/10 bg-[#110b17] pl-4 pr-10 py-3 text-xs sm:text-sm text-white placeholder:text-gray-500 focus:border-[#A373D3] focus:outline-none focus:ring-1 focus:ring-[#A373D3]/30 transition shadow-inner"
              type="text" name="body" required placeholder="Kirim pesan ke #{{ $activeChannel->name }}..." maxlength="1000" autocomplete="off">
          </div>
          <button type="submit" class="pushable pushable-sm shrink-0" title="Kirim Pesan">
            <span class="shadow"></span>
            <span class="edge"></span>
            <span class="front py-2.5 px-4">
              <span>Kirim</span>
              <i class="ph-bold ph-paper-plane-right text-sm"></i>
            </span>
          </button>
        </form>
      </div>
    @endif
  </div>

  <!-- TAB 2: PATUNGAN & BILL DI CHANNEL INI -->
  <div x-show="activeTab === 'patungan'" x-cloak class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-4 custom-scrollbar">
    <div class="flex items-center justify-between pb-3 border-b border-white/5">
      <div>
        <h3 class="text-sm font-bold text-white">Daftar Patungan di #{{ $activeChannel?->name }}</h3>
        <p class="text-xs text-[#6F70A2]">Semua tagihan split bill yang tercatat di sub-channel ini</p>
      </div>
      @if ($can['create_patungan'])
        <a href="{{ route('nongkrong.create', ['group' => $group->id, 'channel' => $activeChannel?->id]) }}" class="pushable pushable-sm">
          <span class="shadow"></span>
          <span class="edge"></span>
          <span class="front py-1.5 px-3">
            + Patungan Baru
          </span>
        </a>
      @endif
    </div>

    @if ($channelSessions->isEmpty())
      <div class="glass-card rounded-2xl border border-dashed border-white/10 p-10 text-center text-[#6F70A2] text-xs">
        Belum ada patungan di channel ini. Tekan tombol <span class="text-[#EB9998] font-bold">"+ Patungan Baru"</span> untuk membuat sesi pertama!
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

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('chat-messages-container');
    if (container) {
      container.scrollTop = container.scrollHeight;
    }
  });
</script>
@endsection


{{-- ========================================================
   KOLOM 4: RIGHT SIDEBAR (MEMBER LIST & CHANNEL INFO)
   Hanya muncul saat tombol info/anggota diklik!
   ======================================================== --}}
@section('right-sidebar')
  <!-- Channel Info Card -->
  <div class="p-4 rounded-2xl bg-[#181224] border border-white/5 mb-5">
    <div class="flex items-center gap-2 mb-1">
      <span class="text-lg font-bold text-[#EB9998]">#</span>
      <h3 class="font-bold text-white text-sm">{{ $activeChannel?->name ?? 'general' }}</h3>
    </div>
    <p class="text-xs text-[#a19db0] leading-relaxed">
      {{ $activeChannel?->description ?? 'Channel obrolan umum & nongkrong circle ini.' }}
    </p>
  </div>

  <!-- Quick Invite Box -->
  @if ($can['invite'])
    @php $invite = $group->activeInvite; @endphp
    <div class="p-3.5 rounded-2xl bg-[#181224] border border-white/5 mb-5 text-xs" x-data="{ copied: false }">
      <span class="text-[10px] uppercase font-bold text-[#6F70A2] tracking-wider block mb-1.5">Link Undangan Circle</span>
      @if ($invite && $invite->usable())
        <div class="flex items-center gap-1.5">
          <input type="text" readonly value="{{ route('join.show', $invite->token) }}" class="flex-1 bg-black/40 border border-white/10 rounded-lg px-2 py-1 text-[11px] text-gray-300 font-mono">
          <button type="button" @click="navigator.clipboard.writeText('{{ route('join.show', $invite->token) }}'); copied = true; setTimeout(() => copied = false, 2000)" class="p-1.5 rounded-lg bg-[#5D549C] text-white hover:opacity-90">
            <i :class="copied ? 'ph-bold ph-check text-emerald-400' : 'ph-bold ph-copy'"></i>
          </button>
        </div>
      @else
        <form method="POST" action="{{ route('groups.invite.generate', $group) }}">
          @csrf
          <button class="w-full py-1.5 rounded-lg bg-white/5 hover:bg-white/10 text-white font-bold text-[11px]">Bikin Link Baru</button>
        </form>
      @endif
    </div>
  @endif

  <!-- Anggota Circle List -->
  <div class="mb-5">
    <div class="flex items-center justify-between pb-2 mb-3 border-b border-white/5">
      <span class="text-[11px] font-black uppercase tracking-wider text-[#6F70A2]">
        Anggota Circle ({{ $memberCount }})
      </span>
    </div>

    <ul class="space-y-2">
      @foreach ($group->members as $member)
        <li class="p-2 rounded-xl hover:bg-white/5 transition group">
          <div class="flex items-center justify-between gap-2">
            <a href="{{ route('profile.user', $member->user) }}" class="flex items-center gap-2.5 min-w-0 flex-1">
              <div class="relative shrink-0">
                @if ($member->user->avatar_path)
                  <img src="{{ asset('storage/' . $member->user->avatar_path) }}" alt="{{ $member->user->name }}" class="w-8 h-8 rounded-xl object-cover ring-1 ring-white/10">
                @else
                  <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-[#5D549C] to-[#7E3A63] flex items-center justify-center text-xs font-bold text-white shadow-xs">
                    {{ strtoupper(substr($member->user->name, 0, 1)) }}
                  </div>
                @endif
                <span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 rounded-full border border-[#1f1a2b] bg-emerald-500"></span>
              </div>

              <div class="min-w-0 flex-1">
                <p class="truncate text-xs font-bold text-white group-hover:text-[#A373D3] transition">
                  {{ $member->user->name }}
                </p>
                <span class="text-[10px] font-semibold {{ $member->user_id === $group->user_id ? 'text-[#EB9998]' : 'text-[#6F70A2]' }}">
                  {{ $member->user_id === $group->user_id ? 'Owner' : ($member->role?->name ?? 'Member') }}
                </span>
              </div>
            </a>

            {{-- Tombol Kick --}}
            @if (!empty($can['manage_roles']) && $member->user_id !== $group->user_id)
              <form method="POST" action="{{ route('groups.kick', $group) }}" onsubmit="return confirm('Keluarkan {{ $member->user->name }} dari grup ini?')">
                @csrf
                <input type="hidden" name="user_id" value="{{ $member->user_id }}">
                <button type="submit" class="p-1 rounded-lg hover:bg-rose-500/20 text-[#6F70A2] hover:text-[#EB9998] transition" title="Keluarkan">
                  <i class="ph-bold ph-user-minus text-xs"></i>
                </button>
              </form>
            @endif
          </div>
        </li>
      @endforeach
    </ul>
  </div>

  <!-- Status Hutang Anggota Grup (Dark Theme Alert Card) -->
  @if (isset($groupDebts) && $groupDebts->isNotEmpty())
    <div class="mt-4 pt-4 border-t border-white/5">
      <span class="text-[11px] font-black uppercase tracking-wider text-[#EB9998] block mb-2.5">
        Tagihan Belum Lunas ({{ $groupDebts->count() }})
      </span>
      <div class="space-y-2">
        @foreach ($groupDebts->take(5) as $debt)
          <div class="p-3 rounded-xl bg-[#7E3A63]/25 border border-[#EB9998]/30 text-xs">
            <div class="flex items-center justify-between mb-1">
              <span class="font-bold text-white truncate">{{ $debt->debtor->name }}</span>
              <span class="font-bold text-[#EB9998]">Rp{{ number_format($debt->amount - ($debt->paid_amount ?? 0), 0, ',', '.') }}</span>
            </div>
            <p class="text-[10px] text-[#a19db0]">
              Ke: {{ $debt->creditor->name }} · {{ $debt->session->name ?? 'Patungan' }}
            </p>
          </div>
        @endforeach
      </div>
    </div>
  @endif
@endsection
