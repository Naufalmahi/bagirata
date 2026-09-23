@extends('layouts.app')

@section('title', 'Budgeting')

@section('content')
<div class="mx-auto max-w-4xl" x-data="{ openModal: false }">
    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">Budgeting Lu 💸</h1>
            <p class="mt-1 text-slate-500">Atur pengeluaran biar nggak rata pas tengah bulan.</p>
        </div>
        <button @click="openModal = true" class="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">
            + Bikin Budget
        </button>
    </div>

    {{-- Smart Suggestion --}}
    @if($comparison['suggestion'])
        <div class="mb-8 rounded-2xl border border-indigo-200 bg-indigo-50 px-5 py-4 text-sm font-medium text-indigo-800 shadow-sm">
            <span class="mr-2">💡</span> {{ $comparison['suggestion'] }}
        </div>
    @endif

    {{-- Budget Cards --}}
    <div class="grid gap-6 sm:grid-cols-2">
        @forelse($summaries as $summary)
            @php 
                $b = $summary['budget'];
                $u = $summary['utilization'];
                $color = $u >= 100 ? 'bg-rose-500' : ($u >= 70 ? 'bg-amber-500' : 'bg-emerald-500');
                $bgColor = $u >= 100 ? 'bg-rose-50' : ($u >= 70 ? 'bg-amber-50' : 'bg-emerald-50');
                $textColor = $u >= 100 ? 'text-rose-700' : ($u >= 70 ? 'text-amber-700' : 'text-emerald-700');
            @endphp
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm transition hover:shadow-md">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h2 class="text-xs font-bold uppercase tracking-widest text-slate-400">{{ $b->category }}</h2>
                        <p class="mt-1 text-2xl font-extrabold text-slate-900">Rp {{ number_format($b->amount, 0, ',', '.') }}</p>
                    </div>
                    <form action="{{ route('budgets.destroy', $b) }}" method="POST" onsubmit="return confirm('Hapus budget ini?')">
                        @csrf @method('DELETE')
                        <button class="text-slate-300 hover:text-rose-500">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        </button>
                    </form>
                </div>

                <div class="mt-4">
                    <div class="flex items-center justify-between text-xs font-semibold">
                        <span class="text-slate-500">Terpakai Rp {{ number_format($summary['spent'], 0, ',', '.') }}</span>
                        <span class="{{ $textColor }}">{{ $u }}%</span>
                    </div>
                    <div class="mt-1.5 h-2 w-full rounded-full bg-slate-100">
                        <div class="h-2 rounded-full {{ $color }}" style="width: {{ $u }}%"></div>
                    </div>
                </div>

                @if($summary['alert'])
                    <p class="mt-4 rounded-xl {{ $bgColor }} px-3 py-2 text-xs font-bold {{ $textColor }}">
                        {{ $summary['alert'] }}
                    </p>
                @endif
            </div>
        @empty
            <div class="sm:col-span-2 rounded-3xl border border-dashed border-slate-300 p-12 text-center">
                <p class="text-slate-400">Belum ada budget. Lu mau foya-foya?</p>
                <button @click="openModal = true" class="mt-4 text-sm font-bold text-indigo-600 hover:underline">Gas bikin budget →</button>
            </div>
        @endforelse
    </div>

    {{-- Monthly Comparison --}}
    @if($summaries->isNotEmpty())
        <section class="mt-10 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="font-bold text-slate-900 mb-4">Perbandingan Bulan Ini ({{ $defaultCategory }})</h2>
            <div class="grid gap-4 sm:grid-cols-3 text-center">
                <div class="rounded-2xl bg-slate-50 p-4">
                    <p class="text-xs font-bold text-slate-400 uppercase">Bulan Ini</p>
                    <p class="mt-1 text-xl font-extrabold text-slate-900">Rp {{ number_format($comparison['this_month'], 0, ',', '.') }}</p>
                </div>
                <div class="rounded-2xl bg-slate-50 p-4">
                    <p class="text-xs font-bold text-slate-400 uppercase">Bulan Lalu</p>
                    <p class="mt-1 text-xl font-extrabold text-slate-900">Rp {{ number_format($comparison['last_month'], 0, ',', '.') }}</p>
                </div>
                <div class="rounded-2xl bg-slate-50 p-4">
                    <p class="text-xs font-bold text-slate-400 uppercase">Rata-rata 3 Bln</p>
                    <p class="mt-1 text-xl font-extrabold text-slate-900">Rp {{ number_format($comparison['three_month_avg'], 0, ',', '.') }}</p>
                </div>
            </div>
        </section>
    @endif

    {{-- Modal Create --}}
    <div x-show="openModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex min-h-screen items-center justify-center p-4">
            <div @click.away="openModal = false" class="relative w-full max-w-md rounded-3xl bg-white p-6 shadow-2xl border border-slate-200">
                <h3 class="text-xl font-bold text-slate-900">Bikin Budget Baru</h3>
                <form action="{{ route('budgets.store') }}" method="POST" class="mt-4 space-y-4">
                    @csrf
                    <div>
                        <label class="text-xs font-bold uppercase text-slate-400">Kategori</label>
                        <select name="category" required class="mt-1 block w-full rounded-xl border-slate-200 bg-slate-50 text-sm">
                            @foreach (\App\Enums\ExpenseCategory::cases() as $cat)
                                <option value="{{ $cat->value }}">{{ $cat->label() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-xs font-bold uppercase text-slate-400">Target Nominal (Rp)</label>
                        <input type="number" name="amount" required min="1" placeholder="Contoh: 500000" class="mt-1 block w-full rounded-xl border-slate-200 bg-slate-50 text-sm">
                    </div>

                    <div>
                        <label class="text-xs font-bold uppercase text-slate-400">Periode</label>
                        <div class="mt-1 flex gap-4">
                            <label class="flex items-center gap-2 text-sm text-slate-700">
                                <input type="radio" name="period_type" value="monthly" checked class="text-indigo-600 focus:ring-indigo-500"> Bulanan
                            </label>
                            <label class="flex items-center gap-2 text-sm text-slate-700">
                                <input type="radio" name="period_type" value="weekly" class="text-indigo-600 focus:ring-indigo-500"> Mingguan
                            </label>
                        </div>
                    </div>

                    <div class="mt-6 flex gap-3">
                        <button type="button" @click="openModal = false" class="flex-1 rounded-xl border border-slate-200 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">Batal</button>
                        <button type="submit" class="flex-1 rounded-xl bg-indigo-600 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="fixed inset-0 -z-10 bg-slate-900/40 backdrop-blur-sm"></div>
    </div>
</div>
@endsection
