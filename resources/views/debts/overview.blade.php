@extends('layouts.app')

@section('title', 'Utang-Piutang')

@section('content')
    @php $me = auth()->id(); $sessionsById = $sessions->keyBy('id'); @endphp

    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">Utang-Piutang</h1>
            <p class="mt-1 text-sm text-slate-500">
                Urusan duit? Ratakan aja boss kuh.
                @if ($summary['active_count'] > 0)
                    Masih ada <strong class="text-amber-600">Rp{{ number_format($summary['total_owed'] + $summary['total_receivable'], 0, ',', '.') }}</strong> yang belum beres.
                @else
                    Semua aman. Utang sudah rata. 🎉
                @endif
            </p>
        </div>
    </div>

    {{-- Ringkasan --}}
    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-2xl border border-rose-200 bg-rose-50 p-5">
            <p class="text-xs font-semibold uppercase tracking-wide text-rose-500">Kamu harus bayar</p>
            <p class="mt-1 text-2xl font-extrabold text-rose-700">Rp{{ number_format($summary['total_owed'], 0, ',', '.') }}</p>
        </div>
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5">
            <p class="text-xs font-semibold uppercase tracking-wide text-emerald-500">Kamu bakal terima</p>
            <p class="mt-1 text-2xl font-extrabold text-emerald-700">Rp{{ number_format($summary['total_receivable'], 0, ',', '.') }}</p>
        </div>
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5">
            <p class="text-xs font-semibold uppercase tracking-wide text-amber-500">Transaksi aktif</p>
            <p class="mt-1 text-2xl font-extrabold text-amber-700">{{ $summary['active_count'] }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Transaksi selesai</p>
            <p class="mt-1 text-2xl font-extrabold text-slate-700">{{ $summary['settled_count'] }}</p>
        </div>
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-2">
        {{-- Aktif per session --}}
        <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="font-bold text-slate-900">Masih jalan <span class="text-xs font-semibold text-slate-400">({{ $active->count() }})</span></h2>

            @if ($active->isEmpty())
                <p class="mt-3 rounded-xl bg-slate-50 px-3 py-3 text-sm text-slate-400">Nggak ada utang aktif, beres semua!</p>
            @else
                @foreach ($active->groupBy('nongkrong_session_id') as $sessionId => $debts)
                    @php $session = $sessionsById->get($sessionId); @endphp
                    <div class="mt-4">
                        <a href="{{ route('nongkrong.show', $session) }}" class="text-xs font-bold uppercase tracking-wide text-indigo-600 hover:underline">
                            {{ $session?->name }}
                        </a>
                        <ul class="mt-1 divide-y divide-slate-100">
                            @foreach ($debts as $debt)
                                @php
                                    $badge = match ($debt->status) {
                                        'payment_reported' => ['bg-amber-50 text-amber-700', 'Nunggu konfirmasi'],
                                        'confirmed' => ['bg-indigo-50 text-indigo-700', 'Dikonfirmasi'],
                                        'rejected' => ['bg-rose-50 text-rose-700', 'Ditolak'],
                                        default => ['bg-slate-100 text-slate-600', 'Belum dibayar'],
                                    };
                                @endphp
                                <li class="flex items-center justify-between gap-2 py-3">
                                    <div class="min-w-0">
                                        <p class="truncate text-sm text-slate-700">
                                            @if ($debt->from_user_id === $me)
                                                Lu utang <strong class="text-rose-600">{{ number_format($debt->outstanding(), 0, ',', '.') }}</strong> ke <strong>{{ $debt->creditor->name }}</strong>
                                            @elseif ($debt->to_user_id === $me)
                                                <strong>{{ $debt->debtor->name }}</strong> utang <strong class="text-emerald-600">{{ number_format($debt->outstanding(), 0, ',', '.') }}</strong> ke lu
                                            @else
                                                <strong>{{ $debt->debtor->name }}</strong> → <strong>{{ $debt->creditor->name }}</strong> Rp{{ number_format($debt->outstanding(), 0, ',', '.') }}
                                            @endif
                                        </p>
                                        <p class="text-xs text-slate-400">{{ $debt->created_at?->format('d M Y') }}</p>
                                    </div>
                                    <div class="flex shrink-0 items-center gap-2">
                                        <span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $badge[0] }}">{{ $badge[1] }}</span>
                                        <a href="{{ route('debts.show', [$session, $debt]) }}" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-indigo-600 hover:bg-indigo-50">
                                            {{ $me === $debt->from_user_id ? 'Lapor bayar' : 'Detail' }}
                                        </a>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            @endif
        </section>

        {{-- Selesai / histori --}}
        <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="font-bold text-slate-900">Udah beres <span class="text-xs font-semibold text-slate-400">({{ $settled->count() }})</span></h2>

            @if ($settled->isEmpty())
                <p class="mt-3 rounded-xl bg-slate-50 px-3 py-3 text-sm text-slate-400">Belum ada yang lunas. Sabar, ikhtiar, ngopi.</p>
            @else
                @foreach ($settled->groupBy('nongkrong_session_id') as $sessionId => $debts)
                    @php $session = $sessionsById->get($sessionId); @endphp
                    <div class="mt-4">
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-400">{{ $session?->name }}</p>
                        <ul class="mt-1 divide-y divide-slate-100">
                            @foreach ($debts as $debt)
                                <li class="flex items-center justify-between gap-2 py-3 text-sm text-slate-400">
                                    <p class="min-w-0 truncate line-through">
                                        <strong class="text-slate-500">{{ $debt->debtor->name }}</strong>
                                        bayar <strong class="text-slate-500">Rp{{ number_format($debt->amount, 0, ',', '.') }}</strong>
                                        ke <strong class="text-slate-500">{{ $debt->creditor->name }}</strong>
                                    </p>
                                    <span class="shrink-0 text-xs">{{ $debt->settled_at?->format('d M Y') }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            @endif
        </section>
    </div>
@endsection