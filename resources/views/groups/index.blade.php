@extends('layouts.app')

@section('title', 'Circle Tongkrongan · BagiRata')

@section('content')
<div class="p-4 sm:p-6 space-y-6 max-w-7xl mx-auto w-full">
  <div class="flex flex-wrap items-center justify-between gap-3">
    <div>
      <h1 class="page-title text-theme-main">Circle Tongkrongan</h1>
      <p class="text-xs sm:text-sm text-theme-muted mt-0.5">Kelola geng nongkrong, squad hobi, dan kas bersama</p>
    </div>
    <a href="{{ route('groups.create') }}" class="pushable pushable-sm">
      <span class="shadow"></span>
      <span class="edge"></span>
      <span class="front">
        <i class="ph-bold ph-plus text-sm"></i>
        <span>Bikin Circle</span>
      </span>
    </a>
  </div>

  {{-- Search Public Groups --}}
  <div>
    <form method="GET" action="{{ route('groups.index') }}" class="flex items-center gap-2">
      <div class="relative flex-1">
        <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-theme-subtle" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
        <input type="text" name="search" value="{{ $searchQuery ?? '' }}" placeholder="Cari circle publik yang bisa diikuti..." class="input pl-10 bg-theme-elevated border-theme-border text-theme-main placeholder:text-theme-subtle">
      </div>
      <button type="submit" class="btn btn-secondary">Cari</button>
      @if (!empty($searchQuery))
        <a href="{{ route('groups.index') }}" class="btn btn-ghost text-xs">Reset</a>
      @endif
    </form>
  </div>

  {{-- Public Groups Search Results --}}
  @if (!empty($searchQuery) && isset($publicGroups) && $publicGroups->isNotEmpty())
    <div>
      <h2 class="text-xs font-bold text-theme-subtle uppercase tracking-wider mb-3 flex items-center gap-1.5">
        <svg class="h-4 w-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        Circle Publik Ditemukan
      </h2>
      <div class="grid gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($publicGroups as $pg)
          <div class="glass-card card-hover rounded-3xl border border-theme-border p-5 flex flex-col justify-between">
            <div>
              <div class="flex items-start justify-between gap-2">
                <div class="flex items-center gap-3">
                  @if ($pg->avatar_path)
                    <img src="{{ asset('storage/' . $pg->avatar_path) }}" alt="{{ $pg->name }}" class="h-11 w-11 rounded-2xl object-cover ring-1 ring-theme-border">
                  @else
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-tr from-[#5D549C] to-[#7E3A63] text-sm font-black text-white">
                      {{ strtoupper(substr($pg->name, 0, 1)) }}
                    </div>
                  @endif
                  <div>
                    <h2 class="font-bold text-theme-main">{{ $pg->name }}</h2>
                    <span class="text-xs text-theme-muted">{{ $pg->members_count }} anggota</span>
                  </div>
                </div>
                <span class="badge badge-success shrink-0 text-[10px]">
                  Publik
                </span>
              </div>
              @if ($pg->description)
                <p class="mt-2.5 line-clamp-2 text-xs text-theme-muted leading-relaxed">{{ $pg->description }}</p>
              @endif
            </div>
            <div class="mt-4 flex items-center justify-between border-t border-theme-border pt-3">
              <span class="text-xs text-theme-subtle">Owner: {{ $pg->owner->name }}</span>
              <form method="POST" action="{{ route('groups.join', $pg) }}">
                @csrf
                <button class="btn btn-primary btn-sm">Gabung Circle</button>
              </form>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  @elseif (!empty($searchQuery) && isset($publicGroups) && $publicGroups->isEmpty())
    <div class="rounded-2xl border border-dashed border-theme-border p-6 text-center text-sm text-theme-muted">
      Tidak ada circle publik yang cocok dengan kata kunci "{{ $searchQuery }}".
    </div>
  @endif

  {{-- My Groups --}}
  <div>
    <div class="flex items-center justify-between mb-3">
      <h2 class="text-xs font-bold text-theme-subtle uppercase tracking-wider">Circle Saya</h2>
      <span class="text-xs text-theme-muted font-semibold">{{ $groups->count() }} circle</span>
    </div>

    @if ($groups->isEmpty())
      <div class="glass-card rounded-3xl border border-dashed border-theme-border p-8 text-center text-theme-main">
        <div class="flex justify-center mb-3">
          <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-[#5D549C]/20 text-[#A373D3]">
            <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
          </div>
        </div>
        <p class="font-semibold text-theme-main">Belum ada circle yang kamu ikuti.</p>
        <p class="mt-1 text-xs text-theme-muted">Bikin circle baru buat teman kamu atau cari circle publik di atas.</p>
        <div class="mt-4">
          <a href="{{ route('groups.create') }}" class="pushable pushable-sm inline-block">
            <span class="shadow"></span>
            <span class="edge"></span>
            <span class="front">Bikin Circle Sekarang</span>
          </a>
        </div>
      </div>
    @else
      <div class="grid gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($groups as $grpItem)
          <a href="{{ route('groups.show', $grpItem) }}" class="glass-card card-hover rounded-3xl border border-theme-border p-5 block">
            <div class="flex items-start justify-between gap-2">
              <div class="flex items-center gap-3">
                @if ($grpItem->avatar_path)
                  <img src="{{ asset('storage/' . $grpItem->avatar_path) }}" alt="{{ $grpItem->name }}" class="h-11 w-11 rounded-2xl object-cover ring-1 ring-theme-border">
                @else
                  <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-tr from-[#5D549C] to-[#7E3A63] text-sm font-black text-white">
                    {{ strtoupper(substr($grpItem->name, 0, 1)) }}
                  </div>
                @endif
                <div class="min-w-0">
                  <h2 class="font-bold text-theme-main truncate">{{ $grpItem->name }}</h2>
                  <div class="flex items-center gap-2 mt-0.5">
                    <span class="text-xs text-theme-muted">{{ $grpItem->members_count }} orang</span>
                    @if ($grpItem->visibility === 'private')
                      <span class="inline-flex items-center gap-0.5 text-[10px] font-semibold text-amber-500">
                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                        Privat
                      </span>
                    @else
                      <span class="inline-flex items-center gap-0.5 text-[10px] font-semibold text-emerald-500">
                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Publik
                      </span>
                    @endif
                  </div>
                </div>
              </div>
              <span class="badge badge-neutral shrink-0">{{ $grpItem->sessions_count }} patungan</span>
            </div>
            @if ($grpItem->description)
              <p class="mt-2.5 line-clamp-2 text-xs text-theme-muted leading-relaxed">{{ $grpItem->description }}</p>
            @endif
            <div class="mt-3 flex items-center justify-between text-xs text-theme-subtle border-t border-theme-border pt-2.5">
              <span>by {{ $grpItem->owner->name }}</span>
              <span class="text-[#A373D3] font-semibold group-hover:underline">Buka Circle &rarr;</span>
            </div>
          </a>
        @endforeach
      </div>
    @endif
  </div>
</div>
@endsection
