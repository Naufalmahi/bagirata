@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <div class="flex min-h-[70vh] flex-col items-center justify-center text-center">
        <span class="badge badge-brand mb-4 px-4 py-1.5 text-sm">Mode Nongkrong Fun · alpha</span>

        <h1 class="max-w-2xl text-4xl font-extrabold leading-tight tracking-tight text-ink md:text-5xl">
            Patungan makin gampang,<br>
            <span class="text-brand-text">nggak ada lagi yang "tinggal taruh dulu".</span>
        </h1>

        <p class="mt-4 max-w-xl text-ink-muted">
            Bayar makan, bensin, sewa tempat, terus bagi-bagian otomatis. Bikin group kayak grup ngumpul
            kalian, undang temen lewat link, tinggal catat pengeluarannya — utang-piutang langsung keitung.
        </p>

        <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
            <a href="{{ route('register') }}" class="btn btn-primary btn-lg">Gas, daftar</a>
            <a href="{{ route('login') }}" class="btn btn-secondary btn-lg">Udah punya akun</a>
        </div>

        <div class="mt-12 grid w-full max-w-3xl gap-4 sm:grid-cols-3">
            @foreach ([
                ['emoji' => '🎲', 'title' => 'Patungan dadakan', 'body' => 'Nggak usah ribet bikin group. Langsung ajak temen, catat, beres.'],
                ['emoji' => '👥', 'title' => 'Group kayak Disc-nya', 'body' => 'Channel, role, permission, invite link. Lengkap banget buat gang lu.'],
                ['emoji' => '⚖️', 'title' => 'Bagi rata & utang', 'body' => 'Bisa rata, nominal bebas, atau persen. Utang keitung otomatis.'],
            ] as $feature)
                <div class="card text-left">
                    <div class="text-2xl">{{ $feature['emoji'] }}</div>
                    <h2 class="mt-2 font-bold text-ink">{{ $feature['title'] }}</h2>
                    <p class="mt-1 text-sm text-ink-muted">{{ $feature['body'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
@endsection
