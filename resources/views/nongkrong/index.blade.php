@extends('layouts.app')

@section('title', 'Sesi Nongki & Patungan · BagiRata')

@section('content')
<div class="p-4 sm:p-6 max-w-5xl mx-auto w-full space-y-6">
  <div class="flex flex-wrap items-center justify-between gap-3">
    <div>
      <h1 class="page-title text-white">Sesi Nongki & Patungan</h1>
      <p class="text-xs sm:text-sm text-[#a19db0] mt-1">Daftar bill dan patungan bareng circle lu biar urusan bayar-membayar aman sentosa.</p>
    </div>
    <a href="{{ route('nongkrong.create') }}" class="btn btn-primary inline-flex items-center gap-2">
      <span>+ Gas Nongki Baru</span>
    </a>
  </div>

  @if ($sessions->isEmpty())
    <div class="empty-panel">
      <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-[#5D549C]/30 text-[#A373D3] mb-3">
        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
      </div>
      <p class="font-semibold text-white">Belum ada sesi nongki nih.</p>
      <p class="mt-1 text-sm text-[#a19db0]">Bikin patungan dadakan atau lewat circle tongkrongan lu biar gak ada yang boncos!</p>
      <a href="{{ route('nongkrong.create') }}" class="btn btn-primary mt-4 inline-flex">Gas Ajak Nongki Sekarang</a>
    </div>
  @else
    <div class="space-y-3">
      @foreach ($sessions as $session)
        @include('nongkrong._session_row', ['session' => $session])
      @endforeach
    </div>
  @endif
</div>
@endsection
