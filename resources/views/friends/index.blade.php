@extends('layouts.app')

@section('title', 'Teman & Koneksi')

@section('content')
<div class="p-4 sm:p-6 max-w-5xl mx-auto w-full space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="page-title text-white">Teman & Koneksi</h1>
            <p class="text-xs sm:text-sm text-[#a19db0] mt-1">Cari teman sesama pengguna untuk diajak nongkrong dan patungan bareng.</p>
        </div>
        <a href="{{ route('friends.search') }}" class="btn btn-primary inline-flex items-center gap-2">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <span>Cari Teman Baru</span>
        </a>
    </div>

    {{-- Permintaan Pertemanan Masuk --}}
    @if ($pendingReceived->isNotEmpty())
        <div class="mb-8 rounded-3xl border border-[#A373D3]/30 bg-[#5D549C]/20 p-5 sm:p-6 shadow-sm">
            <h2 class="text-xs font-black text-[#A373D3] uppercase tracking-wider mb-3 flex items-center gap-2">
                <svg class="h-4 w-4 text-[#EB9998]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                Permintaan Pertemanan Masuk ({{ $pendingReceived->count() }})
            </h2>
            <div class="grid gap-3 sm:grid-cols-2">
                @foreach ($pendingReceived as $req)
                    <div class="flex items-center justify-between gap-3 rounded-2xl bg-[#1f1a2b] p-3.5 border border-white/10 shadow-xs">
                        <div class="flex items-center gap-3">
                            @if ($req->sender->avatar_path)
                                <img src="{{ asset('storage/' . $req->sender->avatar_path) }}" alt="{{ $req->sender->name }}" class="h-10 w-10 rounded-full object-cover ring-2 ring-white/20">
                            @else
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-tr from-[#5D549C] to-[#7E3A63] text-xs font-bold text-white">
                                    {{ strtoupper(substr($req->sender->name, 0, 1)) }}
                                </div>
                            @endif
                            <div class="min-w-0">
                                <a href="{{ route('profile.user', $req->sender) }}" class="text-xs font-bold text-white hover:text-[#A373D3] truncate block">
                                    {{ $req->sender->name }}
                                </a>
                                <p class="text-[11px] text-[#a19db0] truncate">
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
                                <button type="submit" class="btn btn-ghost btn-sm px-2.5 py-1.5 text-xs text-[#EB9998] hover:bg-white/5">
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
            <h2 class="text-xs font-bold text-[#6F70A2] uppercase tracking-wider mb-3">
                Permintaan Terkirim (Menunggu Konfirmasi) &middot; {{ $pendingSent->count() }}
            </h2>
            <div class="grid gap-3 sm:grid-cols-2">
                @foreach ($pendingSent as $req)
                    <div class="card flex items-center justify-between gap-3 p-3.5">
                        <div class="flex items-center gap-3">
                            @if ($req->receiver->avatar_path)
                                <img src="{{ asset('storage/' . $req->receiver->avatar_path) }}" alt="{{ $req->receiver->name }}" class="h-10 w-10 rounded-full object-cover ring-1 ring-white/20">
                            @else
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-white/10 text-xs font-bold text-white">
                                    {{ strtoupper(substr($req->receiver->name, 0, 1)) }}
                                </div>
                            @endif
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-white truncate">{{ $req->receiver->name }}</p>
                                <p class="text-[11px] text-[#a19db0] truncate">
                                    {{ $req->receiver->username ? '@' . $req->receiver->username : $req->receiver->email }}
                                </p>
                            </div>
                        </div>
                        <span class="badge badge-warning text-[10px]">Menunggu</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Daftar Teman --}}
    <div>
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-xs font-bold text-[#6F70A2] uppercase tracking-wider">
                Daftar Teman ({{ $friends->count() }})
            </h2>
        </div>

        @if ($friends->isEmpty())
            <div class="empty-panel">
                <div class="flex justify-center mb-3">
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-[#5D549C]/30 text-[#A373D3]">
                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>
                <p class="font-semibold text-white">Belum ada teman terhubung.</p>
                <p class="mt-1 text-sm text-[#a19db0]">Cari teman kamu yang sudah terdaftar di web BagiRata dan kirim permintaan pertemanan.</p>
                <a href="{{ route('friends.search') }}" class="btn btn-primary mt-4 inline-flex">Cari Teman Sekarang</a>
            </div>
        @else
            <div class="grid gap-3 sm:grid-cols-2">
                @foreach ($friends as $friend)
                    <div class="card flex items-center justify-between gap-3 p-4">
                        <a href="{{ route('profile.user', $friend) }}" class="flex items-center gap-3 min-w-0 group">
                            @if ($friend->avatar_path)
                                <img src="{{ asset('storage/' . $friend->avatar_path) }}" alt="{{ $friend->name }}" class="h-10 w-10 rounded-full object-cover ring-2 ring-white/20">
                            @else
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-tr from-[#5D549C] to-[#7E3A63] text-xs font-bold text-white">
                                    {{ strtoupper(substr($friend->name, 0, 1)) }}
                                </div>
                            @endif
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-white group-hover:text-[#A373D3] transition truncate">{{ $friend->name }}</p>
                                <p class="text-[11px] text-[#a19db0] truncate">
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
