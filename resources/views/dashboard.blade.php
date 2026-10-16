@extends('layouts.app')

@section('title', 'Dashboard Akun')

@section('content')
<div class="p-4 sm:p-6 lg:p-8 max-w-7xl mx-auto w-full space-y-8">
  {{-- Header Banner Sambutan (GTA 6 Neon Palette: Pink #FF2A85, Purple #8B5CF6, Orange #FF7A00) --}}
  <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-[#FF2A85] via-[#8B5CF6] to-[#FF7A00] p-6 sm:p-8 text-white shadow-2xl shadow-[#FF2A85]/20 border border-white/20">
    {{-- Background Soft Blobs --}}
    <div class="absolute -right-10 -bottom-10 h-40 w-40 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
    <div class="absolute -left-10 -top-10 h-32 w-32 rounded-full bg-white/10 blur-xl pointer-events-none"></div>

    <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
      <div class="flex items-center gap-4">
        <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-white/20 p-2 shadow-inner ring-2 ring-white/30 backdrop-blur-md">
          <img src="{{ asset('image/logo.png') }}" alt="Logo BagiRata" class="h-11 w-auto object-contain brightness-0 invert">
        </div>
        <div>
          <div class="flex items-center gap-2">
            <span class="rounded-full bg-white/20 px-2.5 py-0.5 text-[11px] font-bold uppercase tracking-wider text-white">
              Anak BagiRata
            </span>
            <span class="text-xs text-white/90">· {{ now()->translatedFormat('l, d F Y') }}</span>
          </div>
          <h1 class="mt-1 text-2xl sm:text-3xl font-black tracking-tight text-white">
            Yo, {{ auth()->user()->name }}!
          </h1>
          <p class="text-sm text-white/95 mt-0.5 max-w-xl">
            Kelola patungan santuy, cek uang kas circle, dan pastiin utang kelar tanpa drama nagih!
          </p>
        </div>
      </div>

      <div class="flex flex-wrap items-center gap-3">
        <a href="{{ route('nongkrong.create') }}" class="pushable">
          <span class="shadow"></span>
          <span class="edge"></span>
          <span class="front">
            <i class="ph-bold ph-plus text-base"></i>
            <span>Gas Nongki Baru</span>
          </span>
        </a>

        <a href="{{ route('groups.create') }}" class="pushable">
          <span class="shadow"></span>
          <span class="edge"></span>
          <span class="front">
            <i class="ph-bold ph-users-three text-base"></i>
            <span>Bikin Circle Baru</span>
          </span>
        </a>
      </div>
    </div>
  </div>

  {{-- Status Finansial / Utang Piutang Card (Theme-Adaptive & Rounded-3xl) --}}
  @if ($pendingTotal === 0)
    <div class="glass-card rounded-3xl border border-emerald-500/30 bg-emerald-500/10 p-5 sm:p-6 text-theme-main shadow-lg flex items-start gap-4">
      <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-emerald-500 text-white shadow-md shadow-emerald-500/30">
        <i class="ph-bold ph-check text-xl"></i>
      </div>
      <div class="flex-1">
        <h3 class="font-black text-theme-main text-base">Aman Bos, Utang Bersih & Lunas!</h3>
        <p class="text-sm text-theme-muted mt-0.5 leading-relaxed">
          Kagak ada utang maupun piutang yang nyangkut di circle lu saat ini. Dompet aman, pertemanan tentram!
        </p>
      </div>
      <a href="{{ route('debts.overview') }}" class="hidden sm:inline-flex rounded-2xl bg-theme-elevated border border-theme-border px-4 py-2 text-xs font-bold text-theme-main shadow-xs hover:border-[#FF2A85]/40 transition">
        Cek Buku Utang →
      </a>
    </div>
  @else
    <div class="glass-card rounded-3xl border border-[#FF2A85]/35 bg-[#FF2A85]/10 p-5 sm:p-6 text-theme-main shadow-lg flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div class="flex items-start gap-4">
        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-[#FF2A85] text-white shadow-md shadow-[#FF2A85]/30">
          <i class="ph-bold ph-warning text-xl"></i>
        </div>
        <div>
          <div class="flex items-center gap-2">
            <h3 class="font-black text-theme-main text-base">Eits, Ada Utang yang Belum Beres Nih!</h3>
            <span class="rounded-full bg-[#FF2A85]/20 border border-[#FF2A85]/40 px-2.5 py-0.5 text-xs font-bold text-[#FF2A85]">{{ $pendingCount }} Transaksi</span>
          </div>
          <p class="text-sm text-theme-muted mt-0.5">
            Total yang masih nyangkut senilai <span class="font-extrabold text-[#FF2A85] text-base">Rp{{ number_format($pendingTotal, 0, ',', '.') }}</span>. Buruan beresin biar catatan tetep rapi & circle gak canggung.
          </p>
        </div>
      </div>
      <a href="{{ route('debts.overview') }}" class="pushable pushable-danger">
        <span class="shadow"></span>
        <span class="edge"></span>
        <span class="front">
          <span>Beresin Sekarang</span>
          <i class="ph-bold ph-arrow-right text-sm"></i>
        </span>
      </a>
    </div>
  @endif

  {{-- Pintasan Navigasi Fitur Cepat (GTA 6 Accents, Non-rigid, Soft Rounded) --}}
  <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
    <a href="{{ route('nongkrong.create') }}" class="glass-card flex flex-col items-center justify-center rounded-3xl p-5 text-center transition hover:border-[#FF2A85]/50 hover:shadow-[0_0_20px_rgba(255,42,133,0.15)] hover:-translate-y-0.5">
      <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-[#FF2A85]/15 text-[#FF2A85] mb-2.5 border border-[#FF2A85]/30 shadow-xs">
        <i class="ph-bold ph-coffee text-xl"></i>
      </div>
      <span class="text-xs font-bold text-theme-main">Sesi Nongki</span>
      <span class="text-[11px] text-theme-subtle">Catat bill sat-set</span>
    </a>

    <a href="{{ route('calculator.index') }}" class="glass-card flex flex-col items-center justify-center rounded-3xl p-5 text-center transition hover:border-[#FF7A00]/50 hover:shadow-[0_0_20px_rgba(255,122,0,0.15)] hover:-translate-y-0.5">
      <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-[#FF7A00]/15 text-[#FF7A00] mb-2.5 border border-[#FF7A00]/30 shadow-xs">
        <i class="ph-bold ph-calculator text-xl"></i>
      </div>
      <span class="text-xs font-bold text-theme-main">Kalkulator</span>
      <span class="text-[11px] text-theme-subtle">Hitung kilat</span>
    </a>

    <a href="{{ route('budgets.index') }}" class="glass-card flex flex-col items-center justify-center rounded-3xl p-5 text-center transition hover:border-[#8B5CF6]/50 hover:shadow-[0_0_20px_rgba(139,92,246,0.15)] hover:-translate-y-0.5">
      <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-[#8B5CF6]/15 text-[#8B5CF6] mb-2.5 border border-[#8B5CF6]/30 shadow-xs">
        <i class="ph-bold ph-chart-pie-slice text-xl"></i>
      </div>
      <span class="text-xs font-bold text-theme-main">Budgeting</span>
      <span class="text-[11px] text-theme-subtle">Anti boncos</span>
    </a>

    <a href="{{ route('debts.overview') }}" class="glass-card flex flex-col items-center justify-center rounded-3xl p-5 text-center transition hover:border-emerald-500/50 hover:shadow-[0_0_20px_rgba(52,211,153,0.15)] hover:-translate-y-0.5">
      <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-500/15 text-emerald-400 mb-2.5 border border-emerald-500/30 shadow-xs">
        <i class="ph-bold ph-hand-coins text-xl"></i>
      </div>
      <span class="text-xs font-bold text-theme-main">Buku Utang</span>
      <span class="text-[11px] text-theme-subtle">Cek siapa nunggak</span>
    </a>
  </div>

  {{-- SECTION: Groups Circle --}}
  <section>
    <div class="mb-4 flex items-center justify-between">
      <div class="flex items-center gap-2.5">
        <div class="flex h-8 w-8 items-center justify-center rounded-xl bg-[#8B5CF6]/15 text-[#8B5CF6] border border-[#8B5CF6]/30">
          <i class="ph-bold ph-users-three text-base"></i>
        </div>
        <h2 class="text-lg font-black text-theme-main">Circle Tongkrongan Lu</h2>
      </div>
      <a href="{{ route('groups.index') }}" class="text-xs font-bold text-[#8B5CF6] hover:text-[#FF2A85] hover:underline transition">
        Lihat Semua ({{ $groups->count() }}) →
      </a>
    </div>

    @if ($groups->isEmpty())
      <div class="glass-card rounded-3xl border border-dashed border-theme-border p-8 text-center shadow-xl">
        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-[#8B5CF6]/15 text-[#8B5CF6] mb-3 border border-[#8B5CF6]/30">
          <i class="ph-bold ph-users-three text-2xl"></i>
        </div>
        <h3 class="text-base font-bold text-theme-main">Belum ada circle tongkrongan nih</h3>
        <p class="text-xs text-theme-muted mt-1 max-w-sm mx-auto">
          Bikin circle permanen buat geng ngopi, temen kosan, atau trip liburan bareng biar kas & utang tercatat rapi.
        </p>
        <div class="mt-4">
          <a href="{{ route('groups.create') }}" class="pushable pushable-sm">
            <span class="shadow"></span>
            <span class="edge"></span>
            <span class="front">+ Gas Bikin Circle</span>
          </a>
        </div>
      </div>
    @else
      <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($groups->take(6) as $grpItem)
          <a href="{{ route('groups.show', $grpItem) }}" class="glass-card group relative rounded-3xl p-5 shadow-xl transition-all duration-200 hover:border-[#FF2A85]/50 hover:shadow-[0_0_20px_rgba(255,42,133,0.15)] hover:-translate-y-0.5 border border-theme-border">
            <div class="flex items-start justify-between gap-2 mb-2">
              <h3 class="font-bold text-theme-main group-hover:text-[#FF2A85] transition-colors line-clamp-1">
                {{ $grpItem->name }}
              </h3>
              <span class="inline-flex items-center gap-1 rounded-full bg-theme-elevated border border-theme-border px-2.5 py-0.5 text-[11px] font-bold text-theme-subtle shrink-0">
                <i class="ph-bold ph-users text-xs text-[#8B5CF6]"></i>
                {{ $grpItem->members_count }} anak
              </span>
            </div>
            @if ($grpItem->description)
              <p class="line-clamp-2 text-xs text-theme-muted leading-relaxed">{{ $grpItem->description }}</p>
            @else
              <p class="text-xs italic text-theme-subtle">Belum ada bio circle.</p>
            @endif
            <div class="mt-4 flex items-center justify-between border-t border-theme-border/40 pt-3 text-[11px] text-theme-subtle">
              <span>Inisiator: {{ $grpItem->owner->name }}</span>
              <span class="font-bold text-[#FF2A85] group-hover:translate-x-1 transition-transform">Nimbrung →</span>
            </div>
          </a>
        @endforeach
      </div>
    @endif
  </section>

  {{-- SECTION: Sesi Nongkrong --}}
  <section>
    <div class="mb-4 flex items-center justify-between">
      <div class="flex items-center gap-2.5">
        <div class="flex h-8 w-8 items-center justify-center rounded-xl bg-[#FF7A00]/15 text-[#FF7A00] border border-[#FF7A00]/30">
          <i class="ph-bold ph-receipt text-base"></i>
        </div>
        <h2 class="text-lg font-black text-theme-main">Riwayat Nongki & Bill Terbaru</h2>
      </div>
      <a href="{{ route('nongkrong.index') }}" class="text-xs font-bold text-[#FF7A00] hover:text-[#FF2A85] hover:underline transition">
        Lihat Semua ({{ $sessions->count() }}) →
      </a>
    </div>

    @if ($sessions->isEmpty())
      <div class="glass-card rounded-3xl border border-dashed border-theme-border p-8 text-center shadow-xl">
        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-[#FF7A00]/15 text-[#FF7A00] mb-3 border border-[#FF7A00]/30">
          <i class="ph-bold ph-coffee text-2xl"></i>
        </div>
        <h3 class="text-base font-bold text-theme-main">Belum ada riwayat nongki nih</h3>
        <p class="text-xs text-theme-muted mt-1 max-w-sm mx-auto">
          Kuy ajak circle lu nongki atau makan bareng, terus catet bill-nya di sini biar gak boncos!
        </p>
        <a href="{{ route('nongkrong.create') }}" class="pushable pushable-sm mt-4 inline-block">
          <span class="shadow"></span>
          <span class="edge"></span>
          <span class="front">+ Gas Ajak Nongki Sekarang</span>
        </a>
      </div>
    @else
      <div class="space-y-3">
        @foreach ($sessions->take(6) as $session)
          @include('nongkrong._session_row', ['session' => $session])
        @endforeach
      </div>
    @endif
  </section>
</div>
@endsection
