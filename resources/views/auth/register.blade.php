@extends('layouts.app')

@section('title', 'Bikin Akun Baru · BagiRata')

@section('content')
<div class="mx-auto max-w-md py-6 sm:py-10">
  <div class="glass-card rounded-3xl border border-theme-border p-7 sm:p-9 shadow-2xl backdrop-blur-xl text-theme-main">
    {{-- Kembali ke Beranda --}}
    <div class="mb-6">
      <a href="{{ route('landing') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-theme-muted hover:text-[#A373D3] transition-colors">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
        </svg>
        Balik ke Beranda
      </a>
    </div>

    {{-- Logo & Header Form --}}
    <div class="text-center mb-8">
      <div class="mx-auto mb-3 flex h-16 w-16 items-center justify-center rounded-2xl bg-[#5D549C] p-2.5 shadow-lg shadow-[#5D549C]/40 border border-[#A373D3]/30">
        <img src="{{ asset('image/logo.png') }}" alt="Logo BagiRata" class="h-full w-auto object-contain brightness-0 invert">
      </div>
      <h1 class="text-2xl font-black tracking-tight text-theme-main">Bikin Akun BagiRata</h1>
      <p class="mt-1.5 text-xs sm:text-sm text-theme-muted">
        Gratis & sat-set! Bebasin circle tongkrongan lu dari drama nagih utang dan boncos.
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
        <label class="block text-xs font-bold uppercase tracking-wider text-theme-muted mb-1.5" for="name">
          Nama / Nickname Lu
        </label>
        <div class="relative">
          <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-theme-subtle">
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
            placeholder="cth: Alex Santuy"
            class="w-full rounded-2xl border border-theme-border bg-theme-elevated pl-10 pr-4 py-3 text-sm text-theme-main placeholder:text-theme-subtle focus:border-[#A373D3] focus:outline-none focus:ring-1 focus:ring-[#A373D3]/30 transition"
          >
        </div>
        @error('name')
          <p class="mt-1.5 text-xs font-medium text-[#EB9998]">{{ $message }}</p>
        @enderror
      </div>

      {{-- Input Email --}}
      <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-theme-muted mb-1.5" for="email">
          Alamat Email Lu
        </label>
        <div class="relative">
          <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-theme-subtle">
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
            placeholder="email@tongkrongan.com"
            class="w-full rounded-2xl border border-theme-border bg-theme-elevated pl-10 pr-4 py-3 text-sm text-theme-main placeholder:text-theme-subtle focus:border-[#A373D3] focus:outline-none focus:ring-1 focus:ring-[#A373D3]/30 transition"
          >
        </div>
        @error('email')
          <p class="mt-1.5 text-xs font-medium text-[#EB9998]">{{ $message }}</p>
        @enderror
      </div>

      {{-- Input Password --}}
      <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-theme-muted mb-1.5" for="password">
          Password
        </label>
        <div class="relative">
          <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-theme-subtle">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
          </div>
          <input
            id="password"
            type="password"
            name="password"
            required
            placeholder="Minimal 8 karakter ya"
            class="w-full rounded-2xl border border-theme-border bg-theme-elevated pl-10 pr-4 py-3 text-sm text-theme-main placeholder:text-theme-subtle focus:border-[#A373D3] focus:outline-none focus:ring-1 focus:ring-[#A373D3]/30 transition"
          >
        </div>
        @error('password')
          <p class="mt-1.5 text-xs font-medium text-[#EB9998]">{{ $message }}</p>
        @enderror
      </div>

      {{-- Input Konfirmasi Password --}}
      <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-theme-muted mb-1.5" for="password_confirmation">
          Ketik Ulang Password
        </label>
        <div class="relative">
          <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-theme-subtle">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
          </div>
          <input
            id="password_confirmation"
            type="password"
            name="password_confirmation"
            required
            placeholder="Ketik ulang password lu"
            class="w-full rounded-2xl border border-theme-border bg-theme-elevated pl-10 pr-4 py-3 text-sm text-theme-main placeholder:text-theme-subtle focus:border-[#A373D3] focus:outline-none focus:ring-1 focus:ring-[#A373D3]/30 transition"
          >
        </div>
      </div>

      {{-- Tombol Submit Pushable --}}
      <button type="submit" class="pushable w-full mt-2">
        <span class="shadow"></span>
        <span class="edge"></span>
        <span class="front w-full py-3.5">
          <span>Gas Bikin Akun Sekarang</span>
          <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
          </svg>
        </span>
      </button>
    </form>

    {{-- Switch to Login --}}
    <div class="mt-6 border-t border-theme-border pt-5 text-center text-xs text-theme-muted">
      <span>Udah punya akun?</span>
      <a href="{{ route('login', ['redirect' => request('redirect')]) }}" class="ml-1 font-bold text-[#A373D3] hover:text-[#EB9998] hover:underline transition">
        Langsung login di sini →
      </a>
    </div>
  </div>
</div>
@endsection
