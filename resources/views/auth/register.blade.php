@extends('layouts.app')

@section('title', 'Daftar Akun Baru')

@section('content')
<div class="mx-auto max-w-md py-6 sm:py-10">
    <div class="rounded-3xl border border-slate-200/90 bg-white p-7 sm:p-9 shadow-xl shadow-slate-200/50">
        {{-- Kembali ke Beranda --}}
        <div class="mb-6">
            <a href="{{ route('landing') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-400 hover:text-indigo-600 transition-colors">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali ke Beranda
            </a>
        </div>

        {{-- Logo & Header Form --}}
        <div class="text-center mb-8">
            <div class="mx-auto mb-3 flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-tr from-purple-600 via-indigo-600 to-blue-600 p-2.5 shadow-md shadow-indigo-200">
                <img src="{{ asset('image/logo.png') }}" alt="Logo BagiRata" class="h-full w-auto object-contain brightness-0 invert">
            </div>
            <h1 class="text-2xl font-black tracking-tight text-slate-900">Buat Akun Gratis</h1>
            <p class="mt-1.5 text-xs sm:text-sm text-slate-500">
                Mulai kelola patungan, kas grup, dan nongkrong tanpa drama.
            </p>
        </div>

        {{-- Form Register --}}
        <form method="POST" action="{{ route('register.store') }}" class="space-y-4">
            @csrf
            @if (request('redirect'))
                <input type="hidden" name="redirect" value="{{ request('redirect') }}">
            @endif

            {{-- Input Nama --}}
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="name">
                    Nama Panggilan
                </label>
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <input
                        id="name"
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        required
                        autofocus
                        placeholder="Contoh: Budi Santoso"
                        class="w-full rounded-2xl border border-slate-300 bg-slate-50/50 pl-10 pr-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 transition"
                    >
                </div>
                @error('name')
                    <p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Input Email --}}
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="email">
                    Alamat Email
                </label>
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206" />
                        </svg>
                    </div>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        placeholder="nama@email.com"
                        class="w-full rounded-2xl border border-slate-300 bg-slate-50/50 pl-10 pr-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 transition"
                    >
                </div>
                @error('email')
                    <p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Input Password --}}
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="password">
                    Kata Sandi
                </label>
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        placeholder="Minimal 8 karakter"
                        class="w-full rounded-2xl border border-slate-300 bg-slate-50/50 pl-10 pr-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 transition"
                    >
                </div>
                @error('password')
                    <p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Input Konfirmasi Password --}}
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5" for="password_confirmation">
                    Ulangi Kata Sandi
                </label>
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <input
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        required
                        placeholder="Ketik ulang kata sandi"
                        class="w-full rounded-2xl border border-slate-300 bg-slate-50/50 pl-10 pr-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 transition"
                    >
                </div>
            </div>

            {{-- Tombol Submit Gradasi Ungu-Biru --}}
            <button type="submit" class="w-full mt-2 inline-flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-purple-600 via-indigo-600 to-blue-600 py-3.5 px-4 text-sm font-bold text-white shadow-lg shadow-indigo-500/25 transition-all duration-200 hover:from-purple-700 hover:to-blue-700 hover:shadow-indigo-500/40 active:scale-[0.99]">
                <span>Daftar Akun Gratis</span>
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </button>
        </form>

        {{-- Switch to Login --}}
        <div class="mt-6 border-t border-slate-100 pt-5 text-center text-xs text-slate-500">
            <span>Sudah memiliki akun?</span>
            <a href="{{ route('login', ['redirect' => request('redirect')]) }}" class="ml-1 font-bold text-indigo-600 hover:text-indigo-800 hover:underline">
                Masuk di sini →
            </a>
        </div>
    </div>
</div>
@endsection
