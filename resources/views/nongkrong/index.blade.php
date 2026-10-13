@extends('layouts.app')

@section('title', 'Nongkrong')

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <h1 class="page-title">Nongkrong & patungan</h1>
        <a href="{{ route('nongkrong.create') }}" class="btn btn-primary">+ Nongkrong baru</a>
    </div>

    @if ($sessions->isEmpty())
        <div class="empty-panel">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-500 mb-3">
                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <p class="font-semibold text-ink">Belum ada sesi patungan.</p>
            <p class="mt-1 text-sm text-ink-muted">Bikin patungan dadakan atau lewat group circle biar lebih teratur.</p>
            <a href="{{ route('nongkrong.create') }}" class="btn btn-primary mt-4 inline-flex">Ajak Temen Nongkrong</a>
        </div>
    @else
        <div class="space-y-3">
            @foreach ($sessions as $session)
                @include('nongkrong._session_row', ['session' => $session])
            @endforeach
        </div>
    @endif
@endsection
