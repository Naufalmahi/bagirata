@extends('layouts.app')

@section('title', 'Profil ' . $profileUser->name)

@section('content')
<div class="mx-auto max-w-2xl p-4 sm:p-6">
  {{-- Card Profil ala Discord (Non-rigid, GTA 6 theme compatible) --}}
  <div class="overflow-hidden rounded-3xl border border-theme-border bg-theme-card shadow-2xl">
    {{-- Profile Banner Header (GTA 6 Neon Pink, Purple, Orange) --}}
    <div class="relative h-36 bg-gradient-to-r from-[#FF2A85] via-[#8B5CF6] to-[#FF7A00] sm:h-44">
      <div class="absolute -right-10 -bottom-10 h-32 w-32 rounded-full bg-white/10 blur-xl"></div>
      <div class="absolute -left-10 -top-10 h-32 w-32 rounded-full bg-white/10 blur-xl"></div>
    </div>

    {{-- Info Profil & Avatar --}}
    <div class="relative px-6 pb-8 pt-0 sm:px-8">
      {{-- Avatar Wrapper --}}
      <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between -mt-16 sm:-mt-20 gap-4 mb-6">
        <div class="relative inline-block">
          @if ($profileUser->avatar_path)
            <img src="{{ asset('storage/' . $profileUser->avatar_path) }}" alt="{{ $profileUser->name }}"
              class="h-28 w-28 sm:h-32 sm:w-32 rounded-3xl border-4 border-theme-card object-cover shadow-xl bg-theme-elevated ring-2 ring-[#FF2A85]/40">
          @else
            <div class="flex h-28 w-28 sm:h-32 sm:w-32 items-center justify-center rounded-3xl border-4 border-theme-card bg-gradient-to-tr from-[#FF2A85] via-[#8B5CF6] to-[#FF7A00] text-3xl sm:text-4xl font-black text-white shadow-xl ring-2 ring-[#FF2A85]/40">
              {{ strtoupper(substr($profileUser->name, 0, 1)) }}
            </div>
          @endif
          <span class="absolute bottom-1 right-1 h-5 w-5 rounded-full border-2 border-theme-card bg-emerald-500 shadow-sm" title="Online"></span>
        </div>

        @if ($isOwner)
          <div class="flex items-center gap-2">
            <a href="{{ route('profile.edit') }}" class="inline-flex items-center gap-2 rounded-2xl border border-theme-border bg-theme-elevated px-5 py-2.5 text-xs sm:text-sm font-bold text-theme-main shadow-xs hover:border-[#FF2A85]/50 transition">
              <i class="fa-solid fa-gear text-sm text-[#FF2A85]"></i>
              <span>User Settings</span>
            </a>
          </div>
        @endif
      </div>

      {{-- Nama & Handle --}}
      <div>
        <h1 class="text-2xl font-black text-theme-main tracking-tight sm:text-3xl">
          {{ $profileUser->name }}
        </h1>
        <p class="text-sm font-semibold text-[#FF2A85] mt-0.5">
          {{ $profileUser->username ? '@' . $profileUser->username : '@' . Str::slug($profileUser->name) }}
        </p>
      </div>

      {{-- Bio --}}
      @if ($profileUser->bio)
        <p class="mt-4 text-sm text-theme-muted leading-relaxed max-w-xl">
          {{ $profileUser->bio }}
        </p>
      @else
        <p class="mt-4 text-xs italic text-theme-subtle">
          Belum ada bio profil.
          @if ($isOwner)
            <a href="{{ route('profile.edit') }}" class="text-[#FF2A85] hover:underline ml-1">Tambah bio sekarang</a>
          @endif
        </p>
      @endif

      {{-- Stat Cards --}}
      <div class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-3">
        <div class="rounded-2xl border border-theme-border bg-theme-elevated p-3.5 text-center">
          <p class="text-xs font-bold text-theme-subtle uppercase tracking-wider">Group Circle</p>
          <p class="mt-1 text-xl font-black text-theme-main">{{ $profileUser->groups_count ?? 0 }}</p>
        </div>
        <div class="rounded-2xl border border-theme-border bg-theme-elevated p-3.5 text-center">
          <p class="text-xs font-bold text-theme-subtle uppercase tracking-wider">Sesi Nongkrong</p>
          <p class="mt-1 text-xl font-black text-theme-main">{{ $profileUser->nongkrong_sessions_count ?? 0 }}</p>
        </div>
        <div class="col-span-2 sm:col-span-1 rounded-2xl border border-theme-border bg-theme-elevated p-3.5 text-center">
          <p class="text-xs font-bold text-theme-subtle uppercase tracking-wider">Status Akun</p>
          <p class="mt-1 text-sm font-bold text-emerald-400 flex items-center justify-center gap-1">
            <span class="h-2 w-2 rounded-full bg-emerald-500"></span> Aktif
          </p>
        </div>
      </div>

      {{-- Info Rekening & Pembayaran Patungan --}}
      <div class="mt-6 rounded-2xl border border-theme-border bg-theme-elevated p-4">
        <div class="flex items-center gap-2 mb-2">
          <i class="ph-bold ph-credit-card text-[#EB9998] text-base"></i>
          <span class="text-xs font-bold uppercase tracking-wider text-theme-subtle">Tujuan Pembayaran Patungan</span>
        </div>

        @if ($profileUser->bank_account)
          <div class="flex flex-wrap items-center justify-between gap-2 pt-1">
            <div>
              <span class="rounded-xl bg-[#EB9998]/15 px-2.5 py-1 text-xs font-extrabold text-[#EB9998] shadow-xs border border-[#EB9998]/30">
                {{ $profileUser->bank_name ?? 'Transfer / E-Wallet' }}
              </span>
              <span class="ml-2 font-mono text-sm font-bold text-theme-main">
                {{ $profileUser->bank_account }}
              </span>
            </div>
            <span class="text-xs text-theme-subtle">a.n. {{ $profileUser->name }}</span>
          </div>
        @else
          <p class="text-xs text-theme-subtle">
            Belum mengatur nomor rekening/e-wallet. Tambahkan di edit profil agar teman nongkrong bisa transfer langsung.
          </p>
        @endif
      </div>

      {{-- Kontak --}}
      @if ($profileUser->phone)
        <div class="mt-4 flex items-center gap-2 text-xs font-semibold text-theme-muted">
          <i class="ph-bold ph-phone text-[#FF7A00] text-sm"></i>
          <span>WhatsApp / No. HP: {{ $profileUser->phone }}</span>
        </div>
      @endif
    </div>
  </div>
</div>
@endsection
