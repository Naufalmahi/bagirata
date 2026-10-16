@php
  $memberCount = $group->members->count();
  $channels = $channels ?? $group->channels;
  $activeChannel = $activeChannel ?? (request()->query('channel') ? $channels->firstWhere('id', (int) request()->query('channel')) : $channels->first());
  $sessions = $sessions ?? ($group->sessions ?? collect());
  $channelSessions = $activeChannel ? $sessions->where('channel_id', $activeChannel->id) : collect();
  $groupLevelSessions = $sessions->where('channel_id', null);

  $user = auth()->user();
  $isOwner = $user && $group->user_id === $user->id;
  $canCreateChannel = isset($can['create_channel']) ? $can['create_channel'] : $isOwner;
  $canCreatePatungan = isset($can['create_patungan']) ? $can['create_patungan'] : ($user && ($isOwner || $group->isMember($user)));
  $canManageRoles = isset($can['manage_roles']) ? $can['manage_roles'] : $isOwner;

  $isTreasuryActive = request()->routeIs('groups.treasury.*');
  $isManageActive = request()->routeIs('groups.manage');
@endphp

<!-- Server / Group Title Header (Sticky di Atas & Responsive) -->
<div class="sticky -top-2 px-1 pt-2 pb-3 bg-theme-sidebar2 z-20 shadow-xs">
  <div class="flex items-center justify-between gap-2">
    <div class="flex items-center gap-3 min-w-0 flex-1">
      @if ($group->avatar_path)
        <img src="{{ asset('storage/' . $group->avatar_path) }}" alt="{{ $group->name }}" class="w-10 h-10 rounded-2xl object-cover ring-1 ring-theme-border shrink-0 shadow-sm">
      @else
        <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-[#5D549C] to-[#7E3A63] flex items-center justify-center text-sm font-black text-white shrink-0 shadow-sm ring-2 ring-white/60 dark:ring-white/10">
          {{ strtoupper(substr($group->name, 0, 1)) }}
        </div>
      @endif
      <div class="min-w-0 flex-1">
        <div class="flex items-center gap-1.5">
          <a href="{{ route('groups.show', $group) }}" class="text-sm font-black text-theme-main truncate leading-tight hover:text-[#A373D3] transition">
            {{ $group->name }}
          </a>
          <i class="ph-fill ph-seal-check text-[#5D549C] dark:text-[#A373D3] text-sm shrink-0" title="Verified Circle"></i>
        </div>
        <div class="flex items-center gap-1.5 mt-0.5">
          <span class="text-[10px] text-theme-muted font-bold uppercase tracking-wider">{{ $memberCount }} Member</span>
          <span class="text-[10px] text-theme-muted">·</span>
          <span class="text-[10px] font-bold uppercase tracking-wider {{ $group->visibility === 'private' ? 'text-amber-500' : 'text-emerald-500' }}">
            {{ ucfirst($group->visibility) }}
          </span>
        </div>
      </div>
    </div>

    <!-- Mobile Drawer Close Button -->
    <button type="button" @click="mobileNav = false" class="lg:hidden p-1.5 rounded-xl text-theme-muted hover:text-theme-main hover:bg-theme-elevated transition shrink-0" title="Tutup Menu">
      <i class="ph-bold ph-x text-base"></i>
    </button>
  </div>

  <!-- Group Quick Actions (Discord / BagiRata High-End Aesthetic) -->
  <div class="mt-3 grid grid-cols-12 gap-1.5 items-stretch">
    <!-- Uang Kas -->
    <a href="{{ route('groups.treasury.show', $group) }}"
      class="col-span-5 flex items-center justify-center gap-1.5 py-2 px-2 rounded-xl font-bold text-xs transition-all shadow-xs border whitespace-nowrap {{ $isTreasuryActive ? 'bg-[#5D549C] text-white border-[#5D549C] shadow-sm' : 'bg-theme-elevated hover:bg-theme-card text-theme-main border-theme-border hover:border-theme-border-strong' }}">
      <i class="ph-bold ph-vault text-xs text-[#EB9998] shrink-0"></i>
      <span class="truncate">Kas</span>
    </a>

    <!-- Patungan -->
    @if ($canCreatePatungan)
      <a href="{{ route('nongkrong.create', ['group' => $group->id, 'channel' => $activeChannel?->id]) }}"
        class="col-span-5 flex items-center justify-center gap-1.5 py-2 px-2 rounded-xl font-bold text-xs bg-[#5D549C] hover:bg-[#4e4587] text-white border border-[#5D549C]/80 shadow-sm hover:shadow transition-all whitespace-nowrap">
        <i class="ph-bold ph-plus text-xs shrink-0"></i>
        <span class="truncate">Patungan</span>
      </a>
    @endif

    <!-- Setelan Circle -->
    @if ($canManageRoles)
      <a href="{{ route('groups.manage', $group) }}"
        class="col-span-2 flex items-center justify-center py-2 rounded-xl border border-theme-border transition-all {{ $isManageActive ? 'bg-[#5D549C] text-white border-[#5D549C]' : 'bg-theme-elevated hover:bg-theme-card text-theme-muted hover:text-theme-main' }}"
        title="Setelan Circle">
        <i class="ph-bold ph-gear text-sm"></i>
      </a>
    @endif
  </div>
</div>

<!-- Channels List (Sub-Grup) Ala Discord dengan Optimal Search Filter -->
<div class="pt-3" x-data="{ newChannelOpen: false, channelFilter: '' }">
  <div class="flex items-center justify-between px-2 pb-2">
    <span class="text-[10px] font-black uppercase tracking-wider text-theme-muted flex items-center gap-1.5">
      <span>Channel Tongkrongan</span>
      <span class="text-[9px] px-1.5 py-0.2 rounded-full bg-theme-elevated border border-theme-border font-bold">{{ $channels->count() }}</span>
    </span>
    @if ($canCreateChannel)
      <button type="button" @click="newChannelOpen = !newChannelOpen" class="w-6 h-6 flex items-center justify-center rounded-lg bg-theme-elevated text-[#5D549C] dark:text-[#A373D3] hover:text-theme-main border border-theme-border transition" title="Bikin Channel Baru">
        <i class="ph-bold ph-plus text-xs"></i>
      </button>
    @endif
  </div>

  {{-- Quick Channel Filter jika channel > 5 --}}
  @if ($channels->count() > 5)
    <div class="px-1 pb-2">
      <div class="relative">
        <i class="ph-bold ph-magnifying-glass absolute left-2.5 top-1/2 -translate-y-1/2 text-theme-subtle text-xs"></i>
        <input type="text"
          x-model="channelFilter"
          placeholder="Cari dari {{ $channels->count() }} channel..."
          class="w-full rounded-xl bg-theme-elevated border border-theme-border pl-7 pr-7 py-1.5 text-xs text-theme-main placeholder:text-theme-subtle focus:outline-none focus:border-[#5D549C] focus:ring-1 focus:ring-[#5D549C]/30 transition shadow-inner">
        <button type="button" x-show="channelFilter" @click="channelFilter = ''" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-theme-subtle hover:text-theme-main text-xs">
          <i class="ph-bold ph-x"></i>
        </button>
      </div>
    </div>
  @endif

  <!-- Inline Form Bikin Channel Baru (Adaptive Light/Dark Theme) -->
  <div x-show="newChannelOpen" x-cloak class="p-3.5 mb-2.5 rounded-2xl bg-theme-elevated border border-theme-border space-y-2.5 shadow-sm">
    <form method="POST" action="{{ route('groups.channels.store', $group) }}" class="space-y-2.5">
      @csrf
      <label class="block text-[10px] font-bold uppercase tracking-wider text-theme-muted" for="channel_name">Nama Channel Baru</label>
      <div class="relative">
        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-xs text-theme-muted font-bold">#</span>
        <input class="w-full rounded-xl border border-theme-border bg-theme-card pl-7 pr-3 py-2 text-xs text-theme-main placeholder:text-theme-muted focus:border-[#5D549C] focus:outline-none"
          id="channel_name" type="text" name="name" required placeholder="cth: warkop-santuy" maxlength="80">
      </div>
      <input class="w-full rounded-xl border border-theme-border bg-theme-card px-3 py-2 text-xs text-theme-main placeholder:text-theme-muted focus:border-[#5D549C] focus:outline-none"
        type="text" name="description" placeholder="Buat tongkrongan apa nih? (opsional)" maxlength="255">
      <div class="flex items-center justify-end gap-2 pt-1">
        <button type="button" @click="newChannelOpen = false" class="rounded-lg px-2.5 py-1 text-[11px] font-bold text-theme-muted hover:text-theme-main">Batal</button>
        <button type="submit" class="rounded-xl bg-[#5D549C] hover:bg-[#4e4587] text-white px-3.5 py-1.5 text-[11px] font-bold transition shadow-sm">
          Gas Bikin
        </button>
      </div>
    </form>
  </div>

  <!-- Navigation Channels (Clean Original Discord Aesthetic with Indicator) -->
  <nav class="space-y-1 px-1">
    @foreach ($channels as $ch)
      @php $isActive = request()->routeIs('groups.show') && $activeChannel && $activeChannel->id === $ch->id; @endphp
      <a href="{{ route('groups.show', ['group' => $group, 'channel' => $ch->id]) }}"
        x-show="!channelFilter || '{{ strtolower(addslashes($ch->name)) }}'.includes(channelFilter.toLowerCase())"
        class="group relative flex items-center justify-between rounded-xl px-3 py-2 text-xs transition-all {{ $isActive ? 'bg-theme-elevated border border-theme-border-strong text-theme-main font-bold shadow-xs' : 'text-theme-muted hover:bg-theme-elevated/80 hover:text-theme-main' }}">
        @if ($isActive)
          <span class="absolute -left-1 top-1/2 -translate-y-1/2 w-1 h-5 rounded-r bg-[#FF2A85] shadow-xs"></span>
        @endif
        <div class="flex items-center gap-2 truncate pl-1">
          <span class="font-bold text-sm {{ $isActive ? 'text-[#5D549C] dark:text-[#EB9998]' : 'text-theme-subtle group-hover:text-theme-muted' }}">#</span>
          <span class="truncate font-semibold">{{ $ch->name }}</span>
        </div>
        <span class="rounded-full px-2 py-0.5 text-[10px] font-bold {{ $isActive ? 'bg-[#5D549C]/20 dark:bg-[#5D549C]/40 text-[#5D549C] dark:text-[#A373D3] border border-[#5D549C]/30' : 'bg-theme-elevated text-theme-muted border border-theme-border' }}">
          {{ $sessions->where('channel_id', $ch->id)->count() }} bill
        </span>
      </a>
    @endforeach
  </nav>
</div>

<!-- Patungan Umum Group (Level Grup tanpa channel) -->
<div class="pt-4 mt-2 border-t border-theme-border">
  <span class="text-[10px] font-black uppercase tracking-wider text-theme-muted block mb-2 px-3">
    Patungan Umum Circle
  </span>
  <div class="p-3.5 rounded-2xl bg-theme-elevated border border-theme-border text-xs">
    <p class="text-theme-main dark:text-theme-muted text-[11px] mb-2 font-medium">Ada {{ $groupLevelSessions->count() }} patungan lepas di luar channel.</p>
    @if ($groupLevelSessions->isNotEmpty())
      <div class="space-y-1.5">
        @foreach ($groupLevelSessions->take(3) as $s)
          <a href="{{ route('nongkrong.show', $s) }}" class="block truncate rounded-xl bg-theme-card border border-theme-border px-3 py-2 font-medium text-theme-main hover:border-[#5D549C]/40 transition">
            <span class="truncate">{{ $s->name }}</span> · <span class="text-[#5D549C] dark:text-[#EB9998] font-bold">Rp{{ number_format($s->total_amount, 0, ',', '.') }}</span>
          </a>
        @endforeach
      </div>
    @endif
  </div>
</div>
