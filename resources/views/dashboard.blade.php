@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="page-title">Halo, {{ auth()->user()->name }} 👋</h1>
            <p class="muted mt-1">Tinggal berapa sih yang nanggung nongkrong kemarin?</p>
        </div>
        <a href="{{ route('nongkrong.create') }}" class="btn btn-primary">+ Nongkrong baru</a>
    </div>

    @if ($pendingTotal === 0)
        <div class="alert alert-success" role="status">
            Bersih! Nggak ada utang pending di semua patungan.
        </div>
    @else
        <div class="alert alert-warning" role="status">
            Ada {{ $pendingCount }} utang-piutang pending total
            <strong class="money">Rp{{ number_format($pendingTotal, 0, ',', '.') }}</strong>. Jangan kelamaan diutangi ya.
        </div>
    @endif

    <section class="mb-10">
        <div class="mb-3 flex items-center justify-between">
            <h2 class="section-title text-lg">Groups</h2>
            <a href="{{ route('groups.index') }}" class="text-sm font-semibold text-brand-text hover:underline">Semua group →</a>
        </div>

        @if ($groups->isEmpty())
            <div class="empty-panel">
                <p class="text-ink-muted">Belum ada group.</p>
                <a href="{{ route('groups.create') }}" class="mt-2 inline-block text-sm font-semibold text-brand-text hover:underline">Bikin group sekarang →</a>
            </div>
        @else
            <div class="grid gap-4 sm:grid-cols-2">
                @foreach ($groups->take(6) as $group)
                    <a href="{{ route('groups.show', $group) }}" class="card card-hover block">
                        <div class="flex items-start justify-between gap-2">
                            <h3 class="font-bold text-ink">{{ $group->name }}</h3>
                            <span class="badge badge-neutral shrink-0">{{ $group->members_count }} orang</span>
                        </div>
                        @if ($group->description)
                            <p class="mt-1 line-clamp-2 text-sm text-ink-muted">{{ $group->description }}</p>
                        @endif
                        <p class="mt-3 text-xs text-ink-subtle">by {{ $group->owner->name }}</p>
                    </a>
                @endforeach
            </div>
        @endif
    </section>

    <section>
        <div class="mb-3 flex items-center justify-between">
            <h2 class="section-title text-lg">Patungan & nongkrong</h2>
            <a href="{{ route('nongkrong.index') }}" class="text-sm font-semibold text-brand-text hover:underline">Semua nongkrong →</a>
        </div>

        @if ($sessions->isEmpty())
            <div class="empty-panel">
                <p class="text-ink-muted">Belum ada patungan, mamang.</p>
                <a href="{{ route('nongkrong.create') }}" class="mt-2 inline-block text-sm font-semibold text-brand-text hover:underline">Ajak temen nongkrong →</a>
            </div>
        @else
            <div class="space-y-3">
                @foreach ($sessions->take(6) as $session)
                    @include('nongkrong._session_row', ['session' => $session])
                @endforeach
            </div>
        @endif
    </section>
@endsection
