@php
    if (isset($groups) && $groups->isNotEmpty()) {
        header('Location: ' . route('groups.show', $groups->first()));
        exit;
    }
@endphp

@extends('layouts.app')

@section('title', 'Explore BagiRata')

@section('content')
<div class="p-4 sm:p-6 lg:p-8 max-w-7xl mx-auto w-full space-y-8">
    {{-- Header Banner Sambutan (Colors: #5D549C, #7E3A63, #EB9998) --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-[#5D549C] via-[#7E3A63] to-[#EB9998] p-6 sm:p-8 text-white shadow-2xl shadow-[#5D549C]/20 border border-white/10">
        {{-- Background Soft Blobs --}}
        <div class="absolute -right-10 -bottom-10 h-40 w-40 rounded-full bg-white/10 blur-2xl"></div>
        <div class="absolute -left-10 -top-10 h-32 w-32 rounded-full bg-white/10 blur-xl"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div class="flex items-center gap-4">
                <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-white/20 p-2 shadow-inner ring-2 ring-white/30 backdrop-blur-md">
                    <img src="{{ asset('image/logo.png') }}" alt="Logo BagiRata" class="h-11 w-auto object-contain brightness-0 invert">
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="rounded-full bg-white/20 px-2.5 py-0.5 text-[11px] font-bold uppercase tracking-wider text-white">
                            Member BagiRata
                        </span>
                        <span class="text-xs text-white/80">· {{ now()->translatedFormat('l, d F Y') }}</span>
                    </div>
                    <h1 class="mt-1 text-2xl sm:text-3xl font-black tracking-tight">
                        Halo, {{ auth()->user()->name }}!
                    </h1>
                    <p class="text-sm text-white/90 mt-0.5">
                        Kelola patungan, pantau saldo circle, dan pastikan utang terselesaikan tanpa drama.
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                <a href="{{ route('nongkrong.create') }}" class="inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-[#5D549C] to-[#EB9998] px-5 py-3 text-xs sm:text-sm font-bold text-white shadow-lg shadow-[#5D549C]/30 transition-all hover:opacity-90 active:scale-95">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>+ Nongkrong Baru</span>
                </a>

                <a href="{{ route('groups.create') }}" class="inline-flex items-center gap-2 rounded-2xl border border-white/30 bg-white/15 px-4 py-3 text-xs sm:text-sm font-bold text-white backdrop-blur-sm transition hover:bg-white/25">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span>+ Bikin Group</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Status Finansial / Utang Piutang Card --}}
    @if ($pendingTotal === 0)
        <div class="glass-card rounded-3xl border border-emerald-500/30 bg-emerald-950/30 p-5 sm:p-6 text-emerald-200 shadow-xl flex items-start gap-4">
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-emerald-500 text-white shadow-md shadow-emerald-500/30">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="font-bold text-white text-base">Semuanya Bersih & Lunas!</h3>
                <p class="text-sm text-emerald-300/80 mt-0.5 leading-relaxed">
                    Tidak ada utang maupun piutang pending di semua sesi nongkrong kamu saat ini. Pertemanan tetap damai dan aman!
                </p>
            </div>
            <a href="{{ route('debts.overview') }}" class="hidden sm:inline-flex rounded-xl bg-white/10 border border-white/15 px-3.5 py-2 text-xs font-bold text-white shadow-sm hover:bg-white/20 transition">
                Cek Riwayat →
            </a>
        </div>
    @else
        <div class="glass-card rounded-3xl border border-[#EB9998]/40 bg-[#7E3A63]/25 p-5 sm:p-6 text-white shadow-xl flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-start gap-4">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-[#EB9998] text-[#1f1a2b] shadow-md shadow-[#EB9998]/30">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="font-bold text-white text-base">Ada Utang-Piutang Belum Selesai</h3>
                        <span class="rounded-full bg-[#EB9998]/20 border border-[#EB9998]/40 px-2 py-0.5 text-xs font-bold text-[#EB9998]">{{ $pendingCount }} Transaksi</span>
                    </div>
                    <p class="text-sm text-slate-300 mt-0.5">
                        Total tertunda saat ini senilai <span class="font-extrabold text-[#EB9998] text-base">Rp{{ number_format($pendingTotal, 0, ',', '.') }}</span>. Segera lakukan pelunasan agar catatan tetap rapi.
                    </p>
                </div>
            </div>
            <a href="{{ route('debts.overview') }}" class="inline-flex items-center justify-center gap-1.5 rounded-2xl bg-gradient-to-r from-[#5D549C] to-[#EB9998] px-5 py-2.5 text-xs sm:text-sm font-bold text-white shadow-lg shadow-[#5D549C]/30 hover:opacity-90 transition">
                <span>Selesaikan Sekarang</span>
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    @endif

    {{-- Pintasan Navigasi Fitur Cepat --}}
    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
        <a href="{{ route('nongkrong.create') }}" class="glass-card flex flex-col items-center justify-center rounded-2xl p-4 text-center transition hover:border-[#A373D3]/50 hover:shadow-[0_0_20px_rgba(163,115,211,0.15)] hover:-translate-y-0.5">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#5D549C]/30 text-[#A373D3] mb-2 border border-[#A373D3]/30">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
            </div>
            <span class="text-xs font-bold text-white">Sesi Baru</span>
            <span class="text-[11px] text-[#6F70A2]">Catat patungan</span>
        </a>

        <a href="{{ route('calculator.index') }}" class="glass-card flex flex-col items-center justify-center rounded-2xl p-4 text-center transition hover:border-[#A373D3]/50 hover:shadow-[0_0_20px_rgba(163,115,211,0.15)] hover:-translate-y-0.5">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#7E3A63]/30 text-[#EB9998] mb-2 border border-[#EB9998]/30">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
            </div>
            <span class="text-xs font-bold text-white">Kalkulator</span>
            <span class="text-[11px] text-[#6F70A2]">Split bill instan</span>
        </a>

        <a href="{{ route('budgets.index') }}" class="glass-card flex flex-col items-center justify-center rounded-2xl p-4 text-center transition hover:border-[#A373D3]/50 hover:shadow-[0_0_20px_rgba(163,115,211,0.15)] hover:-translate-y-0.5">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#5D549C]/30 text-[#6F70A2] mb-2 border border-white/10">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                </svg>
            </div>
            <span class="text-xs font-bold text-white">Budgeting</span>
            <span class="text-[11px] text-[#6F70A2]">Atur anggaran</span>
        </a>

        <a href="{{ route('debts.overview') }}" class="glass-card flex flex-col items-center justify-center rounded-2xl p-4 text-center transition hover:border-[#A373D3]/50 hover:shadow-[0_0_20px_rgba(163,115,211,0.15)] hover:-translate-y-0.5">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-500/20 text-emerald-300 mb-2 border border-emerald-500/30">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                </svg>
            </div>
            <span class="text-xs font-bold text-white">Utang-Piutang</span>
            <span class="text-[11px] text-[#6F70A2]">Ringkasan saldo</span>
        </a>
    </div>

    {{-- SECTION: Groups Circle --}}
    <section>
        <div class="mb-4 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-[#5D549C]/40 text-[#A373D3] border border-[#A373D3]/30">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <h2 class="text-lg font-black text-white">Circle & Group Kamu</h2>
            </div>
            <a href="{{ route('groups.index') }}" class="text-xs font-bold text-[#A373D3] hover:text-[#EB9998] hover:underline transition">
                Lihat Semua ({{ $groups->count() }}) →
            </a>
        </div>

        @if ($groups->isEmpty())
            <div class="glass-card rounded-3xl border border-dashed border-white/15 p-8 text-center shadow-xl">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-[#5D549C]/30 text-[#A373D3] mb-3 border border-[#A373D3]/30">
                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                </div>
                <h3 class="text-base font-bold text-white">Belum ada group circle</h3>
                <p class="text-xs text-[#a19db0] mt-1 max-w-sm mx-auto">
                    Bikin group permanen untuk circle nongkrong, teman kos, atau liburan bareng untuk mencatat kas & saldo.
                </p>
                <a href="{{ route('groups.create') }}" class="mt-4 inline-flex items-center gap-1.5 rounded-xl bg-gradient-to-r from-[#5D549C] to-[#7E3A63] px-4 py-2 text-xs font-bold text-white shadow-md hover:opacity-90 transition">
                    + Buat Group Sekarang
                </a>
            </div>
        @else
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($groups->take(6) as $group)
                    <a href="{{ route('groups.show', $group) }}" class="glass-card group relative rounded-3xl p-5 shadow-xl transition-all duration-200 hover:border-[#A373D3]/50 hover:shadow-[0_0_20px_rgba(163,115,211,0.15)] hover:-translate-y-0.5 border border-white/10">
                        <div class="flex items-start justify-between gap-2 mb-2">
                            <h3 class="font-bold text-white group-hover:text-[#A373D3] transition-colors line-clamp-1">
                                {{ $group->name }}
                            </h3>
                            <span class="inline-flex items-center gap-1 rounded-full bg-white/5 border border-white/10 px-2 py-0.5 text-[11px] font-bold text-[#a19db0] shrink-0">
                                <svg class="h-3 w-3 text-[#6F70A2]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                {{ $group->members_count }} orang
                            </span>
                        </div>
                        @if ($group->description)
                            <p class="line-clamp-2 text-xs text-[#a19db0] leading-relaxed">{{ $group->description }}</p>
                        @else
                            <p class="text-xs italic text-[#6F70A2]">Tidak ada deskripsi group.</p>
                        @endif
                        <div class="mt-4 flex items-center justify-between border-t border-white/5 pt-3 text-[11px] text-[#6F70A2]">
                            <span>Pembuat: {{ $group->owner->name }}</span>
                            <span class="font-bold text-[#A373D3] group-hover:translate-x-1 transition-transform">Buka →</span>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </section>

    {{-- SECTION: Sesi Nongkrong --}}
    <section>
        <div class="mb-4 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-[#7E3A63]/40 text-[#EB9998] border border-[#EB9998]/30">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h2 class="text-lg font-black text-white">Patungan & Nongkrong Terbaru</h2>
            </div>
            <a href="{{ route('nongkrong.index') }}" class="text-xs font-bold text-[#A373D3] hover:text-[#EB9998] hover:underline transition">
                Lihat Semua ({{ $sessions->count() }}) →
            </a>
        </div>

        @if ($sessions->isEmpty())
            <div class="glass-card rounded-3xl border border-dashed border-white/15 p-8 text-center shadow-xl">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-[#7E3A63]/30 text-[#EB9998] mb-3 border border-[#EB9998]/30">
                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-base font-bold text-white">Belum ada riwayat patungan</h3>
                <p class="text-xs text-[#a19db0] mt-1 max-w-sm mx-auto">
                    Mulai ajak teman nongkrong, lalu catat pengeluaran dan bagikan tagihannya secara otomatis di sini.
                </p>
                <a href="{{ route('nongkrong.create') }}" class="mt-4 inline-flex items-center gap-1.5 rounded-xl bg-gradient-to-r from-[#7E3A63] to-[#EB9998] px-4 py-2 text-xs font-bold text-white shadow-md hover:opacity-90 transition">
                    + Ajak Nongkrong Sekarang
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
