@extends('layouts.app')

@section('title', 'Nongkrong')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-extrabold text-slate-900">Nongkrong & patungan</h1>
        <a href="{{ route('nongkrong.create') }}" class="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">+ Nongkrong baru</a>
    </div>

    @if ($sessions->isEmpty())
        <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center">
            <div class="text-4xl">🍜</div>
            <p class="mt-3 font-semibold text-slate-700">Mana nih patungannya?</p>
            <p class="mt-1 text-sm text-slate-500">Bikin patungan dadakan, ato lewat group biar lebih rapi.</p>
            <a href="{{ route('nongkrong.create') }}" class="mt-4 inline-block rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">Ajak temen nongkrong</a>
        </div>
    @else
        <div class="space-y-3">
            @foreach ($sessions as $session)
                @include('nongkrong._session_row', ['session' => $session])
            @endforeach
        </div>
    @endif
@endsection