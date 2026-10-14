@extends('layouts.app')

@section('title', 'Masuk Akun · BagiRata')

@section('content')
<div class="mx-auto max-w-md py-6 sm:py-10">
  <div class="glass-card rounded-3xl border border-white/10 p-7 sm:p-9 shadow-2xl backdrop-blur-xl text-white">
    {{-- Kembali ke Beranda --}}
    <div class="mb-6">
      <a href="{{ route('landing') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-[#6F70A2] hover:text-[#A373D3] transition-colors">
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
      <h1 class="text-2xl font-black tracking-tight text-white">Balik Lagi Nih! </h1>
      <p class="mt-1.5 text-xs sm:text-sm text-[#a19db0]">
        Masuk akun dulu bro, biar patungan nongki circle lu tercatat rapi tanpa drama boncos.
      </p>
    </div>

    {{-- Form Login --}}
    <form method="POST" action="{{ route('login.store') }}" class="space-y-4">
      @csrf
      @if (request('redirect'))
        <input type="hidden" name="redirect" value="{{ request('redirect') }}">
      @endif

      {{-- Input Email --}}
      <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5" for="email">
          Alamat Email Lu
        </label>
        <div class="relative">
          <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-500">
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
            autofocus
            placeholder="email@tongkrongan.com"
            class="w-full rounded-2xl border border-white/10 bg-[#110b17] pl-10 pr-4 py-3 text-sm text-white placeholder:text-gray-500 focus:border-[#A373D3] focus:outline-none focus:ring-1 focus:ring-[#A373D3]/30 transition"
          >
        </div>
        @error('email')
          <p class="mt-1.5 text-xs font-medium text-[#EB9998]">{{ $message }}</p>
        @enderror
      </div>

      {{-- Input Password --}}
      <div>
        <div class="flex items-center justify-between mb-1.5">
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-300" for="password">
            Password
          </label>
        </div>
        <div class="relative">
          <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-500">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
          </div>
          <input
            id="password"
            type="password"
            name="password"
            required
            placeholder="••••••••"
            class="w-full rounded-2xl border border-white/10 bg-[#110b17] pl-10 pr-4 py-3 text-sm text-white placeholder:text-gray-500 focus:border-[#A373D3] focus:outline-none focus:ring-1 focus:ring-[#A373D3]/30 transition"
          >
        </div>
        @error('password')
          <p class="mt-1.5 text-xs font-medium text-[#EB9998]">{{ $message }}</p>
        @enderror
      </div>

      {{-- Remember Me --}}
      <div class="flex items-center justify-between pt-1">
        <label class="flex items-center gap-2 cursor-pointer text-xs font-medium text-slate-300">
          <input class="h-4 w-4 rounded border-white/20 bg-[#110b17] text-[#A373D3] focus:ring-[#A373D3]" type="checkbox" name="remember" value="1">
          <span>Ingetin gua di perangkat ini</span>
        </label>
      </div>

      {{-- Tombol Submit Pushable --}}
      <button type="submit" class="pushable w-full mt-2">
        <span class="shadow"></span>
        <span class="edge"></span>
        <span class="front w-full py-3.5">
          <span>Gas Masuk Akun </span>
          <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
          </svg>
        </span>
      </button>
    </form>

    {{-- Switch to Register --}}
    <div class="mt-6 border-t border-white/10 pt-5 text-center text-xs text-[#a19db0]">
      <span>Belum punya akun?</span>
      <a href="{{ route('register', ['redirect' => request('redirect')]) }}" class="ml-1 font-bold text-[#A373D3] hover:text-[#EB9998] hover:underline transition">
        Gas bikin akun gratis di sini →
      </a>
    </div>
  </div>
</div>
@endsection
