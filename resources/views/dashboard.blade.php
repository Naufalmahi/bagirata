@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">Halo, {{ auth()->user()->name }} 👋</h1>
            <p class="mt-1 text-slate-500">Tinggal berapa sih yang nanggung nongkrong kemarin?</p>
        </div>
        <a href="{{ route('nongkrong.create') }}" class="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">
            + Nongkrong baru
        </a>
    </div>

    @if ($pendingTotal === 0)
        <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            Bersih! Nggak ada utang pending di semua patungan.
        </div>
    @else
        <div class="mb-6 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
            Ada {{ $pendingCount }} utang-piutang pending total <strong>{{ number_format($pendingTotal, 0, ',', '.') }}</strong>. Jangan kelamaan diutangi ya.
        </div>
    @endif

    <section class="mb-10">
        <div class="mb-3 flex items-center justify-between">
            <h2 class="text-lg font-bold text-slate-800">Groups</h2>
            <a href="{{ route('groups.index') }}" class="text-sm font-semibold text-indigo-600 hover:underline">Semua group →</a>
        </div>

        @if ($groups->isEmpty())
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-6 text-center">
                <p class="text-slate-500">Belum ada group.</p>
                <a href="{{ route('groups.create') }}" class="mt-2 inline-block text-sm font-semibold text-indigo-600 hover:underline">Bikin group sekarang →</a>
            </div>
        @else
            <div class="grid gap-4 sm:grid-cols-2">
                @foreach ($groups->take(6) as $group)
                    <a href="{{ route('groups.show', $group) }}" class="rounded-2xl border border-slate-200 bg-white p-5 transition hover:border-indigo-300 hover:shadow-sm">
                        <div class="flex items-start justify-between gap-2">
                            <h3 class="font-bold text-slate-900">{{ $group->name }}</h3>
                            <span class="shrink-0 rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-600">{{ $group->members_count }} orang</span>
                        </div>
                        @if ($group->description)
                            <p class="mt-1 line-clamp-2 text-sm text-slate-500">{{ $group->description }}</p>
                        @endif
                        <p class="mt-3 text-xs text-slate-400">by {{ $group->owner->name }}</p>
                    </a>
                @endforeach
            </div>
        @endif
    </section>

    <section>
        <div class="mb-3 flex items-center justify-between">
            <h2 class="text-lg font-bold text-slate-800">Patungan & nongkrong</h2>
            <a href="{{ route('nongkrong.index') }}" class="text-sm font-semibold text-indigo-600 hover:underline">Semua nongkrong →</a>
        </div>

        @if ($sessions->isEmpty())
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-6 text-center">
                <p class="text-slate-500">Belum ada patungan, mamang.</p>
                <a href="{{ route('nongkrong.create') }}" class="mt-2 inline-block text-sm font-semibold text-indigo-600 hover:underline">Ajak temen nongkrong →</a>
            </div>
        @else
            <div class="space-y-3">
                @foreach ($sessions->take(6) as $session)
                    <a href="{{ route('nongkrong.show', $session) }}" class="flex items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-white p-4 transition hover:border-indigo-300 hover:shadow-sm">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <h3 class="truncate font-bold text-slate-900">{{ $session->name }}</h3>
                                @if ($session->group)
                                    <span class="shrink-0 rounded-full bg-indigo-50 px-2 py-0.5 text-xs font-semibold text-indigo-600">{{ $session->group->name }}</span>
                                @endif
                            </div>
                            <p class="mt-0.5 text-sm text-slate-500">
                                {{ $session->date?->format('D, d M Y') }} · {{ $session->members_count }} orang ·
<<<<<<< HEAD
                                @php $pending = $session->debts->where('status', '!=', \App\Enums\DebtStatus::SETTLED->value)->sum(fn ($d) => $d->outstanding()); @endphp
                                @if ($pending > 0)
                                    <span class="font-semibold text-amber-600">pending {{ number_format($pending, 0, ',', '.') }}</span>
                                @elseif ($session->status() === 'settled')
                                    <span class="font-semibold text-emerald-600">beres semua</span>
                                @else
                                    <span class="font-semibold text-slate-400">belum ada pengeluaran</span>
=======
                                @php $pending = $session->debts->where('status', 'pending')->sum('amount'); @endphp
                                @if ($pending > 0)
                                    <span class="font-semibold text-amber-600">pending {{ number_format($pending, 0, ',', '.') }}</span>
                                @else
                                    <span class="font-semibold text-emerald-600">{{ $session->status() === 'settled' ? 'beres semua' : 'belum ada pengeluaran' }}</span>
>>>>>>> 6561da739345e3ff0fdab546ef4f928a872f067a
                                @endif
                            </p>
                        </div>
                        <span class="shrink-0 text-slate-300">→</span>
                    </a>
                @endforeach
            </div>
        @endif
    </section>
@endsection