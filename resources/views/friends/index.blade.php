@extends('layouts.app')

@section('title', 'Circle Teman · BagiRata')

@section('content')
<div class="p-4 sm:p-6 max-w-5xl mx-auto w-full space-y-6">
  <div class="flex flex-wrap items-center justify-between gap-3">
    <div>
      <h1 class="page-title text-theme-main">Circle Teman</h1>
      <p class="text-xs sm:text-sm text-theme-muted mt-1">Koneksiin temen tongkrongan lu biar sat-set diajak nongki dan patungan bareng.</p>
    </div>
    <a href="{{ route('friends.search') }}" class="pushable pushable-sm">
      <span class="shadow"></span>
      <span class="edge"></span>
      <span class="front">
        <i class="ph-bold ph-magnifying-glass text-sm"></i>
        <span>Cari Teman Baru</span>
      </span>
    </a>
  </div>

  {{-- Permintaan Pertemanan Masuk --}}
  @if ($pendingReceived->isNotEmpty())
    <div class="mb-8 rounded-3xl border border-[#A373D3]/30 bg-[#5D549C]/15 p-5 sm:p-6 shadow-sm">
      <h2 class="text-xs font-black text-[#A373D3] uppercase tracking-wider mb-3 flex items-center gap-2">
        <svg class="h-4 w-4 text-[#EB9998]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        Ada yang Mau Masuk Circle Lu Nih ({{ $pendingReceived->count() }})
      </h2>
      <div class="grid gap-3 grid-cols-1 sm:grid-cols-2">
        @foreach ($pendingReceived as $req)
          <div class="flex items-center justify-between gap-3 rounded-2xl bg-theme-elevated p-3.5 border border-theme-border shadow-xs">
            <div class="flex items-center gap-3 min-w-0">
              @if ($req->sender->avatar_path)
                <img src="{{ asset('storage/' . $req->sender->avatar_path) }}" alt="{{ $req->sender->name }}" class="h-10 w-10 rounded-full object-cover ring-2 ring-theme-border shrink-0">
              @else
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-tr from-[#5D549C] to-[#7E3A63] text-xs font-bold text-white shrink-0">
                  {{ strtoupper(substr($req->sender->name, 0, 1)) }}
                </div>
              @endif
              <div class="min-w-0">
                <a href="{{ route('profile.user', $req->sender) }}" class="text-xs font-bold text-theme-main hover:text-[#A373D3] truncate block">
                  {{ $req->sender->name }}
                </a>
                <p class="text-[11px] text-theme-muted truncate">
                  {{ $req->sender->username ? '@' . $req->sender->username : $req->sender->email }}
                </p>
              </div>
            </div>
            <div class="flex items-center gap-1.5 shrink-0">
              <form method="POST" action="{{ route('friends.accept', $req) }}">
                @csrf
                <button type="submit" class="btn btn-primary btn-sm px-3 py-1.5 text-xs">
                  Terima
                </button>
              </form>
              <form method="POST" action="{{ route('friends.reject', $req) }}">
                @csrf
                <button type="submit" class="btn btn-ghost btn-sm px-2.5 py-1.5 text-xs text-[#EB9998] hover:bg-theme-elevated">
                  Tolak
                </button>
              </form>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  @endif

  {{-- Permintaan Terkirim (Menunggu Konfirmasi) --}}
  @if ($pendingSent->isNotEmpty())
    <div class="mb-8">
      <h2 class="text-xs font-bold text-theme-subtle uppercase tracking-wider mb-3">
        Nunggu Di-acc Circle Teman &middot; {{ $pendingSent->count() }}
      </h2>
      <div class="grid gap-3 grid-cols-1 sm:grid-cols-2">
        @foreach ($pendingSent as $req)
          <div class="card flex items-center justify-between gap-3 p-3.5">
            <div class="flex items-center gap-3 min-w-0">
              @if ($req->receiver->avatar_path)
                <img src="{{ asset('storage/' . $req->receiver->avatar_path) }}" alt="{{ $req->receiver->name }}" class="h-10 w-10 rounded-full object-cover ring-2 ring-theme-border shrink-0">
              @else
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-theme-elevated text-xs font-bold text-theme-main shrink-0">
                  {{ strtoupper(substr($req->receiver->name, 0, 1)) }}
                </div>
              @endif
              <div class="min-w-0">
                <p class="text-xs font-bold text-theme-main truncate">{{ $req->receiver->name }}</p>
                <p class="text-[11px] text-theme-muted truncate">
                  {{ $req->receiver->username ? '@' . $req->receiver->username : $req->receiver->email }}
                </p>
              </div>
            </div>
            <span class="badge badge-warning text-[10px]">Pending</span>
          </div>
        @endforeach
      </div>
    </div>
  @endif

  {{-- Daftar Teman --}}
  <div>
    <div class="flex items-center justify-between mb-3">
      <h2 class="text-xs font-bold text-theme-subtle uppercase tracking-wider">
        Teman Satu Circle ({{ $friends->count() }})
      </h2>
    </div>

    @if ($friends->isEmpty())
      <div class="glass-card rounded-3xl border border-dashed border-theme-border p-8 text-center text-theme-main">
        <div class="flex justify-center mb-3">
          <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-[#5D549C]/20 text-[#A373D3]">
            <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
          </div>
        </div>
        <p class="font-semibold text-theme-main">Belum ada temen di circle lu nih.</p>
        <p class="mt-1 text-sm text-theme-muted">Yuk cari temen nongkrong lu yang udah join BagiRata biar langsung mabar & patungan!</p>
        <div class="mt-4">
          <a href="{{ route('friends.search') }}" class="pushable pushable-sm inline-block">
            <span class="shadow"></span>
            <span class="edge"></span>
            <span class="front">Cari Teman Sekarang</span>
          </a>
        </div>
      </div>
    @else
      <div class="grid gap-3 grid-cols-1 sm:grid-cols-2">
        @foreach ($friends as $friend)
          <div class="card flex items-center justify-between gap-3 p-4">
            <a href="{{ route('profile.user', $friend) }}" class="flex items-center gap-3 min-w-0 group">
              @if ($friend->avatar_path)
                <img src="{{ asset('storage/' . $friend->avatar_path) }}" alt="{{ $friend->name }}" class="h-10 w-10 rounded-full object-cover ring-2 ring-theme-border shrink-0">
              @else
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-tr from-[#5D549C] to-[#7E3A63] text-xs font-bold text-white shrink-0">
                  {{ strtoupper(substr($friend->name, 0, 1)) }}
                </div>
              @endif
              <div class="min-w-0">
                <p class="text-xs font-bold text-theme-main group-hover:text-[#A373D3] transition truncate">{{ $friend->name }}</p>
                <p class="text-[11px] text-theme-muted truncate">
                  {{ $friend->username ? '@' . $friend->username : $friend->email }}
                </p>
              </div>
            </a>
            <div class="flex items-center gap-2 shrink-0">
              <a href="{{ route('profile.user', $friend) }}" class="btn btn-secondary btn-sm text-[11px]">
                Profil
              </a>
            </div>
          </div>
        @endforeach
      </div>
    @endif
  </div>
</div>
@endsection
