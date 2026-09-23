@extends('layouts.app')

@section('title', 'Detail Utang')

@section('content')
    @php
        $me = auth()->id();
        $canReport = $debt->canDebtorReport(auth()->user());
        $canReview = $debt->canCreditorReview(auth()->user());
    @endphp

    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <a href="{{ route('debts.index', $session) }}" class="text-sm font-semibold text-indigo-600 hover:underline">← Utang-Piutang</a>
            <h1 class="mt-1 text-2xl font-extrabold text-slate-900">
                {{ $debt->debtor->name }} <span class="text-slate-400">→</span> {{ $debt->creditor->name }}
            </h1>
            <p class="mt-1 text-sm text-slate-500">
                Sumber transaksi: <a href="{{ route('nongkrong.show', $session) }}" class="font-semibold text-indigo-600 hover:underline">{{ $session->name }}</a>
                · Dibuat {{ $debt->created_at?->format('d M Y') }}
            </p>
        </div>
        <span class="rounded-full px-3 py-1 text-xs font-bold
            {{ match ($debt->status) {
                'settled' => 'bg-emerald-50 text-emerald-700',
                'payment_reported' => 'bg-amber-50 text-amber-700',
                'confirmed' => 'bg-indigo-50 text-indigo-700',
                'rejected' => 'bg-rose-50 text-rose-700',
                default => 'bg-slate-100 text-slate-600',
            } }}">
            {{ $debt->status()->label() }}
        </span>
    </div>

    {{-- Info debt --}}
    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-5">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Debtor</p>
            <p class="mt-1 font-bold text-slate-900">{{ $debt->debtor->name }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Creditor</p>
            <p class="mt-1 font-bold text-slate-900">{{ $debt->creditor->name }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Sisa utang</p>
            <p class="mt-1 text-2xl font-extrabold text-rose-600">Rp{{ number_format($debt->outstanding(), 0, ',', '.') }}</p>
            @if ($debt->paid_amount > 0)
                <p class="text-xs text-slate-400">kebayar Rp{{ number_format($debt->paid_amount, 0, ',', '.') }} dari Rp{{ number_format($debt->amount, 0, ',', '.') }}</p>
            @endif
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Nominal</p>
            <p class="mt-1 font-bold text-slate-900">Rp{{ number_format($debt->amount, 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Lapor bayar (debtor only) --}}
    @if ($canReport && ! $debt->hasPendingReport())
        <section class="mt-6 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="font-bold text-slate-900">Udah bayar? Lapor dong 🤑</h2>
            <p class="mt-1 text-sm text-slate-500">Isi nominal + metode, kasih bukti kalau ada. Kreditur bakal konfirmasi.</p>

            <form method="POST" action="{{ route('debts.payments.store', [$session, $debt]) }}" enctype="multipart/form-data" class="mt-4 grid gap-4 sm:grid-cols-2">
                @csrf

                <div>
                    <label class="text-xs font-semibold text-slate-600">Nominal (maks Rp{{ number_format($debt->outstanding(), 0, ',', '.') }})</label>
                    <input type="number" name="amount" min="1" max="{{ $debt->outstanding() }}" required
                        value="{{ old('amount', $debt->outstanding()) }}"
                        class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
                    @error('amount') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-600">Metode pembayaran</label>
                    <select name="method" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
                        @foreach (\App\Enums\PaymentMethod::casesAsList() as $method)
                            <option value="{{ $method }}" @selected(old('method') === $method)>{{ \App\Enums\PaymentMethod::from($method)->label() }}</option>
                        @endforeach
                    </select>
                    @error('method') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-600">Catatan <span class="text-slate-400">(opsional)</span></label>
                    <input type="text" name="note" maxlength="500" value="{{ old('note') }}"
                        class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-600">Bukti pembayaran <span class="text-slate-400">(opsional)</span></label>
                    <input type="file" name="proof_photo" accept="image/*"
                        class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm file:mr-2 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-indigo-600">
                    @error('proof_photo') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div class="sm:col-span-2">
                    <button type="submit" class="rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700">Kirim laporan bayar</button>
                </div>
            </form>
        </section>
    @endif

    {{-- Histori pembayaran --}}
    <section class="mt-6 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-slate-900">Histori pembayaran</h2>
            <span class="text-xs font-semibold text-slate-400">{{ $debt->payments->count() }} laporan</span>
        </div>

        @if ($debt->payments->isEmpty())
            <p class="mt-3 rounded-xl bg-slate-50 px-3 py-3 text-sm text-slate-400">Belum ada laporan pembayaran.</p>
        @else
            <ul class="mt-3 divide-y divide-slate-100">
                @foreach ($debt->payments as $payment)
                    <li class="py-4">
                        <div class="flex flex-wrap items-start justify-between gap-2">
                            <div class="min-w-0">
                                <p class="font-semibold text-slate-900">
                                    Rp{{ number_format($payment->amount, 0, ',', '.') }}
                                    <span class="text-xs font-normal text-slate-400">via {{ $payment->methodLabel() }}</span>
                                </p>
                                <p class="text-xs text-slate-400">
                                    Dilapor {{ $payment->reporter->name }} · {{ $payment->created_at?->format('d M Y H:i') }}
                                </p>
                                @if ($payment->note)
                                    <p class="mt-1 text-sm text-slate-600">"{{ $payment->note }}"</p>
                                @endif
                                @if ($payment->review_note)
                                    <p class="mt-1 text-xs text-amber-700">Catatan {{ $payment->status === 'rejected' ? 'penolakan' : 'kreditur' }}: {{ $payment->review_note }}</p>
                                @endif
                                @if ($payment->proof_photo)
                                    <a href="{{ url('storage/'.$payment->proof_photo) }}" target="_blank" class="mt-1 inline-block text-xs font-semibold text-indigo-600 hover:underline">🖼 Lihat bukti</a>
                                @endif
                            </div>

                            <div class="flex shrink-0 flex-col items-end gap-2">
                                <span class="rounded-full px-2.5 py-1 text-xs font-bold
                                    {{ match ($payment->status) {
                                        'confirmed' => 'bg-emerald-50 text-emerald-700',
                                        'rejected' => 'bg-rose-50 text-rose-700',
                                        default => 'bg-amber-50 text-amber-700',
                                    } }}">
                                    {{ $payment->status()->label() }}
                                    @if ($payment->reviewed_at)
                                        <span class="font-normal">· {{ $payment->reviewed_at->format('d M') }}</span>
                                    @endif
                                </span>

                                @if ($canReview && $payment->isPending())
                                    <div class="flex gap-2">
                                        <form method="POST" action="{{ route('debts.payments.confirm', [$session, $debt, $payment]) }}">
                                            @csrf
                                            <input type="hidden" name="decision" value="confirmed">
                                            <button class="rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-700">Konfirmasi ✓</button>
                                        </form>
                                        <form method="POST" action="{{ route('debts.payments.reject', [$session, $debt, $payment]) }}"
                                            onsubmit="var r=prompt('Alasan nolak (wajib):'); if(!r) return false; this.querySelector('[name=review_note]').value=r;">
                                            @csrf
                                            <input type="hidden" name="decision" value="rejected">
                                            <input type="hidden" name="review_note" value="">
                                            <button class="rounded-lg border border-rose-200 px-3 py-1.5 text-xs font-semibold text-rose-600 hover:bg-rose-50">Tolak</button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </section>
@endsection