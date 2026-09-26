@extends('layouts.app')

@section('title', 'Utang-Piutang '.$session->name)

@section('content')
    @php $me = auth()->id(); @endphp

    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <a href="{{ route('nongkrong.show', $session) }}" class="back-link">← {{ $session->name }}</a>
            <h1 class="page-title mt-1">Utang-Piutang</h1>
            <p class="muted mt-1">
                Urusan duit? Ratakan aja boss kuh.
                @if ($summary['active_count'] > 0)
                    Masih ada <strong class="money text-warning-text">Rp{{ number_format($summary['total_owed'] + $summary['total_receivable'], 0, ',', '.') }}</strong> yang belum beres.
                @else
                    Semua aman. Utang sudah rata. 🎉
                @endif
            </p>
        </div>
    </div>

    {{-- Ringkasan --}}
    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="stat stat-danger">
            <p class="stat-label">Kamu harus bayar</p>
            <p class="stat-value money">Rp{{ number_format($summary['total_owed'], 0, ',', '.') }}</p>
        </div>
        <div class="stat stat-success">
            <p class="stat-label">Kamu bakal terima</p>
            <p class="stat-value money">Rp{{ number_format($summary['total_receivable'], 0, ',', '.') }}</p>
        </div>
        <div class="stat stat-warning">
            <p class="stat-label">Transaksi aktif</p>
            <p class="stat-value">{{ $summary['active_count'] }}</p>
        </div>
        <div class="stat stat-neutral">
            <p class="stat-label">Transaksi selesai</p>
            <p class="stat-value">{{ $summary['settled_count'] }}</p>
        </div>
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-2">
        {{-- Aktif --}}
        <section class="card">
            <h2 class="section-title">Masih jalan <span class="text-xs font-semibold text-ink-subtle">({{ $active->count() }})</span></h2>

            @if ($active->isEmpty())
                <p class="empty mt-3">Nggak ada utang aktif, beres semua!</p>
            @else
                <ul class="divide-list mt-3">
                    @foreach ($active as $debt)
                        @php
                            [$badgeClass, $badgeLabel] = match ($debt->status) {
                                'payment_reported' => ['badge-warning', 'Nunggu konfirmasi'],
                                'confirmed' => ['badge-brand', 'Dikonfirmasi'],
                                'rejected' => ['badge-danger', 'Ditolak'],
                                default => ['badge-neutral', 'Belum dibayar'],
                            };
                        @endphp
                        <li class="flex items-center justify-between gap-2 py-3">
                            <div class="min-w-0">
                                <p class="truncate text-sm text-ink-muted">
                                    @if ($debt->from_user_id === $me)
                                        Lu utang <strong class="money text-danger-text">{{ number_format($debt->outstanding(), 0, ',', '.') }}</strong> ke <strong class="text-ink">{{ $debt->creditor->name }}</strong>
                                    @elseif ($debt->to_user_id === $me)
                                        <strong class="text-ink">{{ $debt->debtor->name }}</strong> utang <strong class="money text-success-text">{{ number_format($debt->outstanding(), 0, ',', '.') }}</strong> ke lu
                                    @else
                                        <strong class="text-ink">{{ $debt->debtor->name }}</strong> utang <strong class="money">{{ number_format($debt->outstanding(), 0, ',', '.') }}</strong> ke <strong class="text-ink">{{ $debt->creditor->name }}</strong>
                                    @endif
                                </p>
                                <p class="text-xs text-ink-subtle">{{ $debt->created_at?->format('d M Y') }}</p>
                            </div>
                            <div class="flex shrink-0 items-center gap-2">
                                <span class="badge {{ $badgeClass }}">{{ $badgeLabel }}</span>
                                <a href="{{ route('debts.show', [$session, $debt]) }}" class="btn btn-secondary btn-sm">Detail</a>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>

        {{-- Selesai / histori --}}
        <section class="card">
            <h2 class="section-title">Udah beres <span class="text-xs font-semibold text-ink-subtle">({{ $settled->count() }})</span></h2>

            @if ($settled->isEmpty())
                <p class="empty mt-3">Belum ada yang lunas. Sabar, ikhtiar, ngopi.</p>
            @else
                <ul class="divide-list mt-3">
                    @foreach ($settled as $debt)
                        <li class="flex items-center justify-between gap-2 py-3 text-sm text-ink-subtle">
                            <p class="min-w-0 truncate line-through">
                                <strong>{{ $debt->debtor->name }}</strong>
                                bayar <strong class="money">Rp{{ number_format($debt->amount, 0, ',', '.') }}</strong>
                                ke <strong>{{ $debt->creditor->name }}</strong>
                            </p>
                            <span class="shrink-0 text-xs">{{ $debt->settled_at?->format('d M Y') }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>
    </div>
@endsection
