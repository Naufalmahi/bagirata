@extends('layouts.app')

@section('title', 'Profil ' . $profileUser->name)

@section('content')
<div class="mx-auto max-w-2xl p-4 sm:p-6">
    {{-- Card Profil ala Discord --}}
    <div class="overflow-hidden rounded-3xl border border-white/10 bg-[#1f1a2b] shadow-2xl">
        {{-- Profile Banner Header --}}
        <div class="relative h-36 bg-gradient-to-r from-[#5D549C] via-[#7E3A63] to-[#EB9998] sm:h-44">
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
                            class="h-28 w-28 sm:h-32 sm:w-32 rounded-3xl border-4 border-[#1f1a2b] object-cover shadow-xl bg-[#181224] ring-2 ring-[#A373D3]/40">
                    @else
                        <div class="flex h-28 w-28 sm:h-32 sm:w-32 items-center justify-center rounded-3xl border-4 border-[#1f1a2b] bg-gradient-to-tr from-[#5D549C] to-[#7E3A63] text-3xl sm:text-4xl font-black text-white shadow-xl ring-2 ring-[#A373D3]/40">
                            {{ strtoupper(substr($profileUser->name, 0, 1)) }}
                        </div>
                    @endif
                    <span class="absolute bottom-1 right-1 h-5 w-5 rounded-full border-2 border-[#1f1a2b] bg-emerald-500 shadow-sm" title="Online"></span>
                </div>

                @if ($isOwner)
                    <div class="flex items-center gap-2">
                        <a href="{{ route('profile.edit') }}" class="inline-flex items-center gap-2 rounded-2xl border border-white/10 bg-white/5 px-5 py-2.5 text-xs sm:text-sm font-bold text-white shadow-xs hover:bg-white/10 hover:border-white/20 transition">
                            <svg class="h-4 w-4 text-[#A373D3]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                            <span>Edit Profil</span>
                        </a>
                    </div>
                @endif
            </div>

            {{-- Nama & Handle --}}
            <div>
                <h1 class="text-2xl font-black text-white tracking-tight sm:text-3xl">
                    {{ $profileUser->name }}
                </h1>
                <p class="text-sm font-semibold text-[#EB9998] mt-0.5">
                    {{ $profileUser->username ? '@' . $profileUser->username : '@' . Str::slug($profileUser->name) }}
                </p>
            </div>

            {{-- Bio --}}
            @if ($profileUser->bio)
                <p class="mt-4 text-sm text-slate-300 leading-relaxed max-w-xl">
                    {{ $profileUser->bio }}
                </p>
            @else
                <p class="mt-4 text-xs italic text-[#6F70A2]">
                    Belum ada bio profil.
                    @if ($isOwner)
                        <a href="{{ route('profile.edit') }}" class="text-[#A373D3] hover:underline ml-1">Tambah bio sekarang</a>
                    @endif
                </p>
            @endif

            {{-- Stat Cards --}}
            <div class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-3">
                <div class="rounded-2xl border border-white/5 bg-[#181224] p-3.5 text-center">
                    <p class="text-xs font-bold text-[#6F70A2] uppercase tracking-wider">Group Circle</p>
                    <p class="mt-1 text-xl font-black text-white">{{ $profileUser->groups_count ?? 0 }}</p>
                </div>
                <div class="rounded-2xl border border-white/5 bg-[#181224] p-3.5 text-center">
                    <p class="text-xs font-bold text-[#6F70A2] uppercase tracking-wider">Sesi Nongkrong</p>
                    <p class="mt-1 text-xl font-black text-white">{{ $profileUser->nongkrong_sessions_count ?? 0 }}</p>
                </div>
                <div class="col-span-2 sm:col-span-1 rounded-2xl border border-white/5 bg-[#181224] p-3.5 text-center">
                    <p class="text-xs font-bold text-[#6F70A2] uppercase tracking-wider">Status Akun</p>
                    <p class="mt-1 text-sm font-bold text-emerald-400 flex items-center justify-center gap-1">
                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span> Aktif
                    </p>
                </div>
            </div>

            {{-- Info Rekening & Pembayaran Patungan --}}
            <div class="mt-6 rounded-2xl border border-white/10 bg-[#181224] p-4">
                <div class="flex items-center gap-2 mb-2">
                    <svg class="h-4 w-4 text-[#A373D3]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                    <span class="text-xs font-bold uppercase tracking-wider text-[#6F70A2]">Tujuan Pembayaran Patungan</span>
                </div>

                @if ($profileUser->bank_account)
                    <div class="flex flex-wrap items-center justify-between gap-2 pt-1">
                        <div>
                            <span class="rounded-md bg-[#5D549C]/30 px-2 py-0.5 text-xs font-extrabold text-[#EB9998] shadow-xs border border-[#EB9998]/30">
                                {{ $profileUser->bank_name ?? 'Transfer / E-Wallet' }}
                            </span>
                            <span class="ml-2 font-mono text-sm font-bold text-white">
                                {{ $profileUser->bank_account }}
                            </span>
                        </div>
                        <span class="text-xs text-[#6F70A2]">a.n. {{ $profileUser->name }}</span>
                    </div>
                @else
                    <p class="text-xs text-[#a19db0]">
                        Belum mengatur nomor rekening/e-wallet. Tambahkan di edit profil agar teman nongkrong bisa transfer langsung.
                    </p>
                @endif
            </div>

            {{-- Kontak --}}
            @if ($profileUser->phone)
                <div class="mt-4 flex items-center gap-2 text-xs font-semibold text-[#6F70A2]">
                    <svg class="h-4 w-4 text-[#6F70A2]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                    </svg>
                    <span>WhatsApp / No. HP: {{ $profileUser->phone }}</span>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
