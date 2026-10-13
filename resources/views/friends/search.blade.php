@extends('layouts.app')

@section('title', 'Cari Teman')

@section('content')
    <div class="mx-auto max-w-2xl">
        <div class="mb-6">
            <a href="{{ route('friends.index') }}" class="back-link inline-flex items-center gap-1.5 text-xs font-bold text-indigo-600 hover:text-indigo-800 transition">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                Kembali ke Daftar Teman
            </a>
            <h1 class="page-title mt-2">Cari Teman</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Cari teman terdaftar di web BagiRata berdasarkan nama, username, atau email.</p>
        </div>

        <form method="GET" action="{{ route('friends.search') }}" class="flex items-center gap-2 mb-6">
            <div class="relative flex-1">
                <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" name="q" value="{{ $q }}" placeholder="Ketik nama, @username, atau email..." class="input pl-10" autofocus>
            </div>
            <button type="submit" class="btn btn-primary">Cari</button>
            @if (!empty($q))
                <a href="{{ route('friends.search') }}" class="btn btn-ghost text-xs">Reset</a>
            @endif
        </form>

        @if ($q)
            @if ($results->isEmpty())
                <div class="empty-panel">
                    <div class="flex justify-center mb-3">
                        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                            <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>
                    <p class="font-semibold text-ink">Tidak ditemukan pengguna</p>
                    <p class="mt-1 text-sm text-ink-muted">Tidak ada pengguna yang cocok dengan kata kunci "{{ $q }}".</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach ($results as $user)
                        <div class="card flex items-center justify-between gap-3 p-4">
                            <a href="{{ route('profile.user', $user) }}" class="flex items-center gap-3 group min-w-0">
                                @if ($user->avatar_path)
                                    <img src="{{ asset('storage/' . $user->avatar_path) }}" alt="{{ $user->name }}" class="h-11 w-11 rounded-2xl object-cover ring-1 ring-slate-200">
                                @else
                                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-tr from-purple-500 to-indigo-600 text-sm font-bold text-white shadow-xs">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-slate-800 group-hover:text-indigo-600 transition truncate">{{ $user->name }}</p>
                                    <p class="text-[11px] text-slate-500 truncate">
                                        {{ $user->username ? '@' . $user->username : $user->email }}
                                    </p>
                                </div>
                            </a>

                            <div class="shrink-0">
                                @if ($user->friendship_status === 'accepted')
                                    <span class="badge badge-success inline-flex items-center gap-1 text-[11px]">
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                        Sudah Berteman
                                    </span>
                                @elseif ($user->friendship_status === 'pending')
                                    <span class="badge badge-warning inline-flex items-center gap-1 text-[11px]">
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        {{ $user->is_sender ? 'Menunggu Konfirmasi' : 'Permintaan Masuk' }}
                                    </span>
                                @else
                                    <form method="POST" action="{{ route('friends.request', $user) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-primary btn-sm inline-flex items-center gap-1.5 text-xs">
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg>
                                            Tambah Teman
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        @else
            <div class="rounded-2xl border border-dashed border-slate-200 p-8 text-center text-xs text-slate-400">
                <svg class="mx-auto h-8 w-8 text-slate-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                Ketik nama atau username pengguna di kolom pencarian untuk mencari teman.
            </div>
        @endif
    </div>
@endsection
