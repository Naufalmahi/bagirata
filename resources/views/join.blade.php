@extends('layouts.app')

@section('title', 'Gabung Circle Tongkrongan · BagiRata')

@section('content')
  <div class="p-4 sm:p-6 mx-auto max-w-md w-full">
    @if (!$invite || !$usable)
      <div class="card p-6 sm:p-8 text-center bg-theme-card border border-theme-border rounded-3xl shadow-xl">
        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-[#EB9998]/15 text-[#EB9998] border border-[#EB9998]/30">
          <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
          </svg>
        </div>
        <h1 class="mt-4 text-xl font-black text-theme-main">Waduh, link undangan circle-nya gak bisa dipakai!</h1>
        <p class="mt-2 text-sm text-theme-muted">{{ $reasonLabel }}</p>
        <a href="{{ route('home') }}" class="mt-5 inline-block px-5 py-2.5 rounded-xl bg-theme-elevated hover:bg-theme-elevated/80 text-theme-main font-bold text-xs transition border border-theme-border">Balik ke Beranda</a>
      </div>
    @else
      @php $group = $invite->group; @endphp
      <div class="card p-6 sm:p-8 bg-theme-card border border-theme-border rounded-3xl shadow-xl">
        <div class="flex items-center gap-3">
          @if ($group->avatar_path)
            <img src="{{ asset('storage/' . $group->avatar_path) }}" alt="{{ $group->name }}" class="h-14 w-14 shrink-0 rounded-2xl object-cover ring-2 ring-[#A373D3]">
          @else
            <span class="grid h-14 w-14 shrink-0 place-items-center rounded-2xl bg-[#5D549C] text-2xl font-black text-white shadow-lg shadow-[#5D549C]/40">
              {{ strtoupper(substr($group->name, 0, 1)) }}
            </span>
          @endif
          <div class="min-w-0">
            <h1 class="text-xl sm:text-2xl font-black text-theme-main truncate">{{ $group->name }}</h1>
            <p class="text-xs text-theme-muted">Diajak {{ $group->owner->name }} · {{ $group->members->count() }} anak udah join di dalem</p>
          </div>
        </div>

        @if ($group->description)
          <p class="mt-4 p-3.5 rounded-2xl bg-theme-elevated border border-theme-border text-xs text-theme-muted leading-relaxed">{{ $group->description }}</p>
        @endif

        <p class="mt-3 text-xs text-theme-subtle">Link undangan ini {{ $invite->lifetimeLabel() }}.</p>

        <div class="mt-6">
          @auth
            @if ($alreadyMember)
              <div class="rounded-2xl bg-emerald-500/10 border border-emerald-500/20 px-4 py-4 text-center">
                <p class="text-sm font-bold text-emerald-500">Lu udah jadi member circle ini, gas terus nongki!</p>
                <a href="{{ route('groups.show', $group) }}" class="mt-3 block w-full py-2.5 px-4 rounded-xl bg-[#5D549C] hover:bg-[#4e4587] text-white font-bold text-xs transition">Buka Circle Sekarang</a>
              </div>
            @else
              <form method="POST" action="{{ route('join.store', $invite->token) }}">
                @csrf
                <button type="submit" class="pushable w-full">
                  <span class="shadow"></span>
                  <span class="edge"></span>
                  <span class="front w-full">
                    <span>Gas Gabung Circle Ini</span>
                  </span>
                </button>
              </form>
              <p class="mt-2 text-center text-xs text-theme-muted">Login sebagai {{ auth()->user()->name }}</p>
            @endif
          @else
            <p class="mb-3 text-center text-xs text-theme-muted">Login atau daftar dulu bro biar langsung masuk circle.</p>
            <div class="grid grid-cols-2 gap-3">
              <a href="{{ route('login', ['redirect' => request()->getRequestUri()]) }}" class="py-2.5 px-4 rounded-xl bg-theme-elevated hover:bg-theme-elevated/80 text-theme-main font-bold text-xs text-center border border-theme-border transition">Login Akun</a>
              <a href="{{ route('register', ['redirect' => request()->getRequestUri()]) }}" class="py-2.5 px-4 rounded-xl bg-[#5D549C] hover:bg-[#4e4587] text-white font-bold text-xs text-center transition shadow-sm">Daftar Akun</a>
            </div>
          @endauth
        </div>
      </div>
    @endif
  </div>
@endsection
