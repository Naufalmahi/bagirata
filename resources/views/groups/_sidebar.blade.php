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

<!-- Server / Group Title Header -->
<div class="px-2 pt-1 pb-3 border-b border-white/5">
  <div class="flex items-center gap-3">
    @if ($group->avatar_path)
      <img src="{{ asset('storage/' . $group->avatar_path) }}" alt="{{ $group->name }}" class="w-10 h-10 rounded-2xl object-cover ring-1 ring-white/10 shrink-0">
    @else
      <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-[#5D549C] to-[#7E3A63] flex items-center justify-center text-sm font-black text-white shrink-0 shadow-md">
        {{ strtoupper(substr($group->name, 0, 1)) }}
      </div>
    @endif
    <div class="min-w-0 flex-1">
      <div class="flex items-center gap-1.5">
        <a href="{{ route('groups.show', $group) }}" class="text-sm font-black text-white truncate leading-tight hover:text-[#A373D3] transition">
          {{ $group->name }}
        </a>
        <i class="ph-fill ph-seal-check text-[#A373D3] text-sm shrink-0" title="Verified Circle"></i>
      </div>
      <div class="flex items-center gap-1.5 mt-0.5">
        <span class="text-[10px] text-[#6F70A2] font-semibold uppercase">{{ $memberCount }} Member</span>
        <span class="text-[10px] text-white/20">·</span>
        <span class="text-[10px] font-semibold {{ $group->visibility === 'private' ? 'text-amber-400' : 'text-emerald-400' }}">
          {{ ucfirst($group->visibility) }}
        </span>
      </div>
    </div>
  </div>

  <!-- Group Quick Actions -->
  <div class="mt-3 flex items-center gap-1.5">
    <a href="{{ route('groups.treasury.show', $group) }}"
      class="flex-1 h-8 px-2.5 rounded-xl {{ $isTreasuryActive ? 'bg-[#5D549C] text-white shadow-sm border border-[#A373D3]/50' : 'bg-white/5 hover:bg-white/10 text-white border border-white/5' }} text-xs font-bold transition flex items-center justify-center gap-1.5 truncate">
      <i class="ph-bold ph-vault text-xs text-[#EB9998] shrink-0"></i>
      <span class="truncate">Uang Kas</span>
    </a>

    @if ($canCreatePatungan)
      <a href="{{ route('nongkrong.create', ['group' => $group->id, 'channel' => $activeChannel?->id]) }}"
        class="flex-1 h-8 px-2.5 rounded-xl bg-[#5D549C] hover:bg-[#4e4587] text-white text-xs font-bold border border-[#A373D3]/40 shadow-sm transition flex items-center justify-center gap-1.5 truncate">
        <i class="ph-bold ph-plus text-xs shrink-0"></i>
        <span class="truncate">Patungan</span>
      </a>
    @endif

    @if ($canManageRoles)
      <a href="{{ route('groups.manage', $group) }}"
        class="h-8 w-8 rounded-xl {{ $isManageActive ? 'bg-[#5D549C] text-white' : 'bg-white/5 hover:bg-white/10 text-gray-300 hover:text-white' }} border border-white/5 transition flex items-center justify-center shrink-0" title="Setelan Circle">
        <i class="ph-bold ph-gear text-sm"></i>
      </a>
    @endif
  </div>
</div>

<!-- Channels List (Sub-Grup) Ala Discord -->
<div class="pt-2" x-data="{ newChannelOpen: false }">
  <div class="flex items-center justify-between px-2 pb-2">
    <span class="text-[11px] font-black uppercase tracking-wider text-[#6F70A2]">
      Channel Tongkrongan
    </span>
    @if ($canCreateChannel)
      <button type="button" @click="newChannelOpen = !newChannelOpen" class="w-5 h-5 flex items-center justify-center rounded-lg bg-white/5 text-[#A373D3] hover:bg-white/10 transition" title="Bikin Channel Baru">
        <i class="ph-bold ph-plus text-xs"></i>
      </button>
    @endif
  </div>

  <!-- Inline Form Bikin Channel Baru (Dark Theme) -->
  <div x-show="newChannelOpen" x-cloak class="p-3 mb-2 rounded-2xl bg-[#181224] border border-white/10 space-y-2">
    <form method="POST" action="{{ route('groups.channels.store', $group) }}" class="space-y-2">
      @csrf
      <label class="block text-[10px] font-bold uppercase tracking-wider text-[#6F70A2]" for="channel_name">Nama Channel Baru</label>
      <div class="relative">
        <span class="absolute inset-y-0 left-0 flex items-center pl-2.5 text-xs text-gray-500 font-bold">#</span>
        <input class="w-full rounded-xl border border-white/10 bg-[#110b17] pl-6 pr-2 py-1.5 text-xs text-white placeholder:text-gray-500 focus:border-[#A373D3] focus:outline-none"
          id="channel_name" type="text" name="name" required placeholder="cth: warkop-santuy" maxlength="80">
      </div>
      <input class="w-full rounded-xl border border-white/10 bg-[#110b17] px-2.5 py-1.5 text-xs text-white placeholder:text-gray-500 focus:border-[#A373D3] focus:outline-none"
        type="text" name="description" placeholder="Buat tongkrongan apa nih? (opsional)" maxlength="255">
      <div class="flex items-center justify-end gap-2 pt-1">
        <button type="button" @click="newChannelOpen = false" class="rounded-lg px-2 py-1 text-[11px] font-semibold text-gray-400 hover:text-white">Skip Dulu</button>
        <button type="submit" class="rounded-xl bg-[#5D549C] hover:bg-[#4e4587] text-white px-3 py-1 text-[11px] font-bold transition shadow-sm">
          Gas Bikin
        </button>
      </div>
    </form>
  </div>

  <!-- Navigation Channels -->
  <nav class="space-y-1">
    @foreach ($channels as $ch)
      @php $isActive = request()->routeIs('groups.show') && $activeChannel && $activeChannel->id === $ch->id; @endphp
      <a href="{{ route('groups.show', ['group' => $group, 'channel' => $ch->id]) }}"
        class="group flex items-center justify-between rounded-xl px-3 py-2 text-xs font-semibold transition-all {{ $isActive ? 'bg-white/10 border border-[#A373D3]/40 text-white font-bold shadow-sm' : 'text-[#a19db0] hover:bg-white/5 hover:text-white' }}">
        <div class="flex items-center gap-2 truncate">
          <span class="font-bold text-sm {{ $isActive ? 'text-[#EB9998]' : 'text-gray-500 group-hover:text-gray-300' }}">#</span>
          <span class="truncate">{{ $ch->name }}</span>
        </div>
        <span class="rounded-full px-1.5 py-0.2 text-[10px] font-semibold {{ $isActive ? 'bg-[#5D549C]/40 text-[#A373D3] border border-[#A373D3]/30' : 'bg-white/5 text-[#6F70A2]' }}">
          {{ $sessions->where('channel_id', $ch->id)->count() }} bill
        </span>
      </a>
    @endforeach
  </nav>
</div>

<!-- Patungan Umum Group (Level Grup tanpa channel) -->
<div class="pt-3 border-t border-white/5">
  <span class="text-[11px] font-black uppercase tracking-wider text-[#6F70A2] block mb-2 px-2">
    Patungan Umum Circle
  </span>
  <div class="p-3 rounded-2xl bg-[#181224] border border-white/5 text-xs">
    <p class="text-[#6F70A2] text-[11px] mb-1.5">Ada {{ $groupLevelSessions->count() }} patungan lepas di luar channel nih.</p>
    @if ($groupLevelSessions->isNotEmpty())
      <div class="space-y-1">
        @foreach ($groupLevelSessions->take(3) as $s)
          <a href="{{ route('nongkrong.show', $s) }}" class="block truncate rounded-lg bg-black/30 px-2.5 py-1.5 font-medium text-slate-300 hover:text-white hover:bg-white/5 transition">
            {{ $s->name }} · <span class="text-[#EB9998]">Rp{{ number_format($s->total_amount, 0, ',', '.') }}</span>
          </a>
        @endforeach
      </div>
    @endif
  </div>
</div>
