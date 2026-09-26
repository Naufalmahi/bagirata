@extends('layouts.app')

@section('title', 'Budgeting')

@section('content')
<div class="mx-auto max-w-4xl" x-data="{ openModal: false }">
    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="page-title">Budgeting Lu 💸</h1>
            <p class="muted mt-1">Atur pengeluaran biar nggak rata pas tengah bulan.</p>
        </div>
        <button @click="openModal = true" class="btn btn-primary">+ Bikin Budget</button>
    </div>

    {{-- Smart Suggestion --}}
    @if($comparison['suggestion'])
        <div class="alert alert-info mb-8 py-4">
            <span class="mr-2">💡</span> {{ $comparison['suggestion'] }}
        </div>
    @endif

    {{-- Budget Cards --}}
    <div class="grid gap-6 sm:grid-cols-2">
        @forelse($summaries as $summary)
            @php
                $b = $summary['budget'];
                $u = $summary['utilization'];
                $bar = $u >= 100 ? 'bg-danger' : ($u >= 70 ? 'bg-warning' : 'bg-success');
                $pct = $u >= 100 ? 'text-danger-text' : ($u >= 70 ? 'text-warning-text' : 'text-success-text');
                $chip = $u >= 100 ? 'bg-danger-soft' : ($u >= 70 ? 'bg-warning-soft' : 'bg-success-soft');
            @endphp
            <div class="card">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <h2 class="text-xs font-bold uppercase tracking-widest text-ink-subtle">{{ $b->category }}</h2>
                        <p class="money mt-1 text-2xl font-extrabold text-ink">Rp {{ number_format($b->amount, 0, ',', '.') }}</p>
                    </div>
                    <form action="{{ route('budgets.destroy', $b) }}" method="POST" onsubmit="return confirm('Hapus budget ini?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-ghost btn-icon text-ink-subtle hover:text-danger-text" aria-label="Hapus budget {{ $b->category }}">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </form>
                </div>

                <div class="mt-4">
                    <div class="flex items-center justify-between text-xs font-semibold">
                        <span class="text-ink-muted">Terpakai Rp {{ number_format($summary['spent'], 0, ',', '.') }}</span>
                        <span class="{{ $pct }}">{{ $u }}%</span>
                    </div>
                    <div class="mt-1.5 h-2 w-full overflow-hidden rounded-full bg-sunken">
                        <div class="h-2 rounded-full {{ $bar }}" style="width: {{ min(100, $u) }}%"></div>
                    </div>
                </div>

                @if($summary['alert'])
                    <p class="mt-4 rounded-control {{ $chip }} px-3 py-2 text-xs font-bold {{ $pct }}">{{ $summary['alert'] }}</p>
                @endif
            </div>
        @empty
            <div class="empty-panel sm:col-span-2">
                <p class="text-ink-subtle">Belum ada budget. Lu mau foya-foya?</p>
                <button @click="openModal = true" class="mt-4 text-sm font-bold text-brand-text hover:underline">Gas bikin budget →</button>
            </div>
        @endforelse
    </div>

    {{-- Monthly Comparison --}}
    @if($summaries->isNotEmpty())
        <section class="card mt-10 p-6">
            <h2 class="section-title mb-4">Perbandingan Bulan Ini ({{ $defaultCategory }})</h2>
            <div class="grid gap-4 text-center sm:grid-cols-3">
                @foreach ([
                    'Bulan Ini' => $comparison['this_month'],
                    'Bulan Lalu' => $comparison['last_month'],
                    'Rata-rata 3 Bln' => $comparison['three_month_avg'],
                ] as $label => $value)
                    <div class="card-sunken">
                        <p class="text-xs font-bold uppercase text-ink-subtle">{{ $label }}</p>
                        <p class="money mt-1 text-xl font-extrabold text-ink">Rp {{ number_format($value, 0, ',', '.') }}</p>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Modal Create --}}
    <div x-show="openModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-screen items-center justify-center p-4">
            <div @click.away="openModal = false" role="dialog" aria-modal="true" aria-label="Bikin budget baru"
                class="relative w-full max-w-md rounded-card border border-line bg-raised p-6 shadow-pop">
                <h3 class="text-xl font-bold text-ink">Bikin Budget Baru</h3>
                <form action="{{ route('budgets.store') }}" method="POST" class="mt-4 space-y-4">
                    @csrf
                    <div>
                        <label class="label" for="budget-category">Kategori</label>
                        <select class="select" id="budget-category" name="category" required>
                            @foreach (\App\Enums\ExpenseCategory::cases() as $cat)
                                <option value="{{ $cat->value }}">{{ $cat->label() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="label" for="budget-amount">Target Nominal (Rp)</label>
                        <input class="input" id="budget-amount" type="number" name="amount" required min="1" placeholder="Contoh: 500000">
                    </div>

                    <div>
                        <span class="label">Periode</span>
                        <div class="flex gap-4">
                            <label class="flex cursor-pointer items-center gap-2 text-sm text-ink-muted">
                                <input type="radio" name="period_type" value="monthly" checked class="checkbox"> Bulanan
                            </label>
                            <label class="flex cursor-pointer items-center gap-2 text-sm text-ink-muted">
                                <input type="radio" name="period_type" value="weekly" class="checkbox"> Mingguan
                            </label>
                        </div>
                    </div>

                    <div class="mt-6 flex gap-3">
                        <button type="button" @click="openModal = false" class="btn btn-secondary flex-1">Batal</button>
                        <button type="submit" class="btn btn-primary flex-1">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="fixed inset-0 -z-10 bg-ink/40 backdrop-blur-sm"></div>
    </div>
</div>
@endsection
