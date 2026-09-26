@extends('layouts.app')

@section('title', 'Nongkrong')

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <h1 class="page-title">Nongkrong & patungan</h1>
        <a href="{{ route('nongkrong.create') }}" class="btn btn-primary">+ Nongkrong baru</a>
    </div>

    @if ($sessions->isEmpty())
        <div class="empty-panel">
            <div class="text-4xl">🍜</div>
            <p class="mt-3 font-semibold text-ink">Mana nih patungannya?</p>
            <p class="mt-1 text-sm text-ink-muted">Bikin patungan dadakan, ato lewat group biar lebih rapi.</p>
            <a href="{{ route('nongkrong.create') }}" class="btn btn-primary mt-4">Ajak temen nongkrong</a>
        </div>
    @else
        <div class="space-y-3">
            @foreach ($sessions as $session)
                @include('nongkrong._session_row', ['session' => $session])
            @endforeach
        </div>
    @endif
@endsection
