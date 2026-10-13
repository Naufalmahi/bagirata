@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-8">
    {{-- Header Banner Sambutan --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-purple-700 via-indigo-600 to-blue-600 p-6 sm:p-8 text-white shadow-xl shadow-indigo-950/10">
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
                        <span class="rounded-full bg-white/20 px-2.5 py-0.5 text-[11px] font-bold uppercase tracking-wider text-indigo-100">
                            Member BagiRata
                        </span>
                        <span class="text-xs text-white/80">· {{ now()->translatedFormat('l, d F Y') }}</span>
                    </div>
                    <h1 class="mt-1 text-2xl sm:text-3xl font-black tracking-tight">
                        Halo, {{ auth()->user()->name }}! 👋
                    </h1>
                    <p class="text-sm text-indigo-100 mt-0.5">
                        Kelola patungan, pantau saldo circle, dan pastikan utang terselesaikan tanpa drama.
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                <a href="{{ route('nongkrong.create') }}" class="inline-flex items-center gap-2 rounded-2xl bg-white px-5 py-3 text-xs sm:text-sm font-bold text-indigo-700 shadow-md transition-all hover:bg-indigo-50 hover:shadow-lg active:scale-95">
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
        <div class="rounded-3xl border border-emerald-200/80 bg-emerald-50/70 p-5 sm:p-6 text-emerald-900 shadow-sm flex items-start gap-4">
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-emerald-500 text-white shadow-md shadow-emerald-200">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="font-bold text-emerald-950 text-base">Semuanya Bersih & Lunas! 🎉</h3>
                <p class="text-sm text-emerald-700 mt-0.5 leading-relaxed">
                    Tidak ada utang maupun piutang pending di semua sesi nongkrong kamu saat ini. Pertemanan tetap damai dan aman!
                </p>
            </div>
            <a href="{{ route('debts.overview') }}" class="hidden sm:inline-flex rounded-xl bg-white px-3.5 py-2 text-xs font-bold text-emerald-700 shadow-xs hover:bg-emerald-100 transition">
                Cek Riwayat →
            </a>
        </div>
    @else
        <div class="rounded-3xl border border-amber-200/80 bg-amber-50/80 p-5 sm:p-6 text-amber-900 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-start gap-4">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-amber-500 text-white shadow-md shadow-amber-200">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="font-bold text-amber-950 text-base">Ada Utang-Piutang Belum Selesai</h3>
                        <span class="rounded-full bg-amber-200 px-2 py-0.5 text-xs font-bold text-amber-800">{{ $pendingCount }} Transaksi</span>
                    </div>
                    <p class="text-sm text-amber-800 mt-0.5">
                        Total tertunda saat ini senilai <span class="font-extrabold text-amber-950 text-base">Rp{{ number_format($pendingTotal, 0, ',', '.') }}</span>. Segera lakukan pelunasan agar catatan tetap rapi.
                    </p>
                </div>
            </div>
            <a href="{{ route('debts.overview') }}" class="inline-flex items-center justify-center gap-1.5 rounded-2xl bg-amber-600 px-5 py-2.5 text-xs sm:text-sm font-bold text-white shadow-sm hover:bg-amber-700 transition">
                <span>Selesaikan Sekarang</span>
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    @endif

    {{-- Pintasan Navigasi Fitur Cepat --}}
    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
        <a href="{{ route('nongkrong.create') }}" class="flex flex-col items-center justify-center rounded-2xl border border-slate-200/80 bg-white p-4 text-center shadow-xs transition hover:border-indigo-400 hover:shadow-md hover:-translate-y-0.5">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-purple-50 text-purple-600 mb-2">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
            </div>
            <span class="text-xs font-bold text-slate-800">Sesi Baru</span>
            <span class="text-[11px] text-slate-400">Catat patungan</span>
        </a>

        <a href="{{ route('calculator.index') }}" class="flex flex-col items-center justify-center rounded-2xl border border-slate-200/80 bg-white p-4 text-center shadow-xs transition hover:border-indigo-400 hover:shadow-md hover:-translate-y-0.5">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 mb-2">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
            </div>
            <span class="text-xs font-bold text-slate-800">Kalkulator</span>
            <span class="text-[11px] text-slate-400">Split bill instan</span>
        </a>

        <a href="{{ route('budgets.index') }}" class="flex flex-col items-center justify-center rounded-2xl border border-slate-200/80 bg-white p-4 text-center shadow-xs transition hover:border-indigo-400 hover:shadow-md hover:-translate-y-0.5">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600 mb-2">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                </svg>
            </div>
            <span class="text-xs font-bold text-slate-800">Budgeting</span>
            <span class="text-[11px] text-slate-400">Atur anggaran</span>
        </a>

        <a href="{{ route('debts.overview') }}" class="flex flex-col items-center justify-center rounded-2xl border border-slate-200/80 bg-white p-4 text-center shadow-xs transition hover:border-indigo-400 hover:shadow-md hover:-translate-y-0.5">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 mb-2">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                </svg>
            </div>
            <span class="text-xs font-bold text-slate-800">Utang-Piutang</span>
            <span class="text-[11px] text-slate-400">Ringkasan saldo</span>
        </a>
    </div>

    {{-- SECTION: Groups Circle --}}
    <section>
        <div class="mb-4 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-purple-100 text-purple-700">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <h2 class="text-lg font-black text-slate-900">Circle & Group Kamu</h2>
            </div>
            <a href="{{ route('groups.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 hover:underline">
                Lihat Semua ({{ $groups->count() }}) →
            </a>
        </div>

        @if ($groups->isEmpty())
            <div class="rounded-3xl border border-dashed border-slate-300 bg-white p-8 text-center shadow-xs">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-500 mb-3">
                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                </div>
                <h3 class="text-base font-bold text-slate-800">Belum ada group circle</h3>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                    Bikin group permanen untuk circle nongkrong, teman kos, atau liburan bareng untuk mencatat kas & saldo.
                </p>
                <a href="{{ route('groups.create') }}" class="mt-4 inline-flex items-center gap-1.5 rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 px-4 py-2 text-xs font-bold text-white shadow-md hover:from-purple-700 hover:to-indigo-700 transition">
                    + Buat Group Sekarang
                </a>
            </div>
        @else
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($groups->take(6) as $group)
                    <a href="{{ route('groups.show', $group) }}" class="group relative rounded-3xl border border-slate-200/80 bg-white p-5 shadow-xs transition-all duration-200 hover:border-indigo-300 hover:shadow-md hover:-translate-y-0.5">
                        <div class="flex items-start justify-between gap-2 mb-2">
                            <h3 class="font-bold text-slate-900 group-hover:text-indigo-600 transition-colors line-clamp-1">
                                {{ $group->name }}
                            </h3>
                            <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 shrink-0">
                                <svg class="h-3 w-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                {{ $group->members_count }} orang
                            </span>
                        </div>
                        @if ($group->description)
                            <p class="line-clamp-2 text-xs text-slate-500 leading-relaxed">{{ $group->description }}</p>
                        @else
                            <p class="text-xs italic text-slate-400">Tidak ada deskripsi group.</p>
                        @endif
                        <div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-3 text-[11px] text-slate-400">
                            <span>Pembuat: {{ $group->owner->name }}</span>
                            <span class="font-bold text-indigo-600 group-hover:translate-x-1 transition-transform">Buka →</span>
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
                <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-blue-100 text-blue-700">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h2 class="text-lg font-black text-slate-900">Patungan & Nongkrong Terbaru</h2>
            </div>
            <a href="{{ route('nongkrong.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 hover:underline">
                Lihat Semua ({{ $sessions->count() }}) →
            </a>
        </div>

        @if ($sessions->isEmpty())
            <div class="rounded-3xl border border-dashed border-slate-300 bg-white p-8 text-center shadow-xs">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-50 text-blue-500 mb-3">
                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-base font-bold text-slate-800">Belum ada riwayat patungan</h3>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                    Mulai ajak teman nongkrong, lalu catat pengeluaran dan bagikan tagihannya secara otomatis di sini.
                </p>
                <a href="{{ route('nongkrong.create') }}" class="mt-4 inline-flex items-center gap-1.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 px-4 py-2 text-xs font-bold text-white shadow-md hover:from-blue-700 hover:to-indigo-700 transition">
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
