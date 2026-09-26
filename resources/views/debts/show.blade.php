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
            <a href="{{ route('debts.index', $session) }}" class="back-link">← Utang-Piutang</a>
            <h1 class="page-title mt-1">
                {{ $debt->debtor->name }} <span class="text-ink-subtle">→</span> {{ $debt->creditor->name }}
            </h1>
            <p class="muted mt-1">
                Sumber transaksi:
                <a href="{{ route('nongkrong.show', $session) }}" class="font-semibold text-brand-text hover:underline">{{ $session->name }}</a>
                · Dibuat {{ $debt->created_at?->format('d M Y') }}
            </p>
        </div>
        <span class="badge {{ match ($debt->status) {
            'settled' => 'badge-success',
            'payment_reported' => 'badge-warning',
            'confirmed' => 'badge-brand',
            'rejected' => 'badge-danger',
            default => 'badge-neutral',
        } }}">{{ $debt->status()->label() }}</span>
    </div>

    {{-- Info debt --}}
    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="stat stat-neutral">
            <p class="stat-label">Debtor</p>
            <p class="mt-1 font-bold text-ink">{{ $debt->debtor->name }}</p>
        </div>
        <div class="stat stat-neutral">
            <p class="stat-label">Creditor</p>
            <p class="mt-1 font-bold text-ink">{{ $debt->creditor->name }}</p>
        </div>
        <div class="stat stat-danger">
            <p class="stat-label">Sisa utang</p>
            <p class="stat-value money">Rp{{ number_format($debt->outstanding(), 0, ',', '.') }}</p>
            @if ($debt->paid_amount > 0)
                <p class="text-xs text-ink-subtle">kebayar Rp{{ number_format($debt->paid_amount, 0, ',', '.') }} dari Rp{{ number_format($debt->amount, 0, ',', '.') }}</p>
            @endif
        </div>
        <div class="stat stat-neutral">
            <p class="stat-label">Nominal</p>
            <p class="mt-1 font-bold text-ink">Rp{{ number_format($debt->amount, 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Lapor bayar (debtor only) --}}
    @if ($canReport && ! $debt->hasPendingReport())
        <section class="card mt-6">
            <h2 class="section-title">Udah bayar? Lapor dong 🤑</h2>
            <p class="muted mt-1">Isi nominal + metode, kasih bukti kalau ada. Kreditur bakal konfirmasi.</p>

            <form method="POST" action="{{ route('debts.payments.store', [$session, $debt]) }}" enctype="multipart/form-data"
                class="mt-4 grid gap-4 sm:grid-cols-2">
                @csrf

                <div>
                    <label class="label" for="pay-amount">Nominal (maks Rp{{ number_format($debt->outstanding(), 0, ',', '.') }})</label>
                    <input class="input" id="pay-amount" type="number" name="amount" min="1" max="{{ $debt->outstanding() }}" required
                        value="{{ old('amount', $debt->outstanding()) }}">
                    @error('amount')<p class="field-error">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="label" for="pay-method">Metode pembayaran</label>
                    <select class="select" id="pay-method" name="method">
                        @foreach (\App\Enums\PaymentMethod::casesAsList() as $method)
                            <option value="{{ $method }}" @selected(old('method') === $method)>{{ \App\Enums\PaymentMethod::from($method)->label() }}</option>
                        @endforeach
                    </select>
                    @error('method')<p class="field-error">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="label" for="pay-note">Catatan <span class="font-normal text-ink-subtle">(opsional)</span></label>
                    <input class="input" id="pay-note" type="text" name="note" maxlength="500" value="{{ old('note') }}">
                </div>

                <div>
                    <label class="label" for="pay-proof">Bukti pembayaran <span class="font-normal text-ink-subtle">(opsional)</span></label>
                    <input class="file-input" id="pay-proof" type="file" name="proof_photo" accept="image/*">
                    @error('proof_photo')<p class="field-error">{{ $message }}</p>@enderror
                </div>

                <div class="sm:col-span-2">
                    <button type="submit" class="btn btn-primary">Kirim laporan bayar</button>
                </div>
            </form>
        </section>
    @endif

    {{-- Histori pembayaran --}}
    <section class="card mt-6">
        <div class="flex items-center justify-between gap-3">
            <h2 class="section-title">Histori pembayaran</h2>
            <span class="text-xs font-semibold text-ink-subtle">{{ $debt->payments->count() }} laporan</span>
        </div>

        @if ($debt->payments->isEmpty())
            <p class="empty mt-3">Belum ada laporan pembayaran.</p>
        @else
            <ul class="divide-list mt-3">
                @foreach ($debt->payments as $payment)
                    <li class="py-4">
                        <div class="flex flex-wrap items-start justify-between gap-2">
                            <div class="min-w-0">
                                <p class="font-semibold text-ink">
                                    <span class="money">Rp{{ number_format($payment->amount, 0, ',', '.') }}</span>
                                    <span class="text-xs font-normal text-ink-subtle">via {{ $payment->methodLabel() }}</span>
                                </p>
                                <p class="text-xs text-ink-subtle">
                                    Dilapor {{ $payment->reporter->name }} · {{ $payment->created_at?->format('d M Y H:i') }}
                                </p>
                                @if ($payment->note)
                                    <p class="mt-1 text-sm text-ink-muted">"{{ $payment->note }}"</p>
                                @endif
                                @if ($payment->review_note)
                                    <p class="mt-1 text-xs text-warning-text">Catatan {{ $payment->status === 'rejected' ? 'penolakan' : 'kreditur' }}: {{ $payment->review_note }}</p>
                                @endif
                                @if ($payment->proof_photo)
                                    <a href="{{ url('storage/'.$payment->proof_photo) }}" target="_blank" rel="noopener"
                                        class="mt-1 inline-block text-xs font-semibold text-brand-text hover:underline">🖼 Lihat bukti</a>
                                @endif
                            </div>

                            <div class="flex shrink-0 flex-col items-end gap-2">
                                <span class="badge {{ match ($payment->status) {
                                    'confirmed' => 'badge-success',
                                    'rejected' => 'badge-danger',
                                    default => 'badge-warning',
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
                                            <button class="btn btn-primary btn-sm">Konfirmasi ✓</button>
                                        </form>
                                        <form method="POST" action="{{ route('debts.payments.reject', [$session, $debt, $payment]) }}"
                                            onsubmit="var r=prompt('Alasan nolak (wajib):'); if(!r) return false; this.querySelector('[name=review_note]').value=r;">
                                            @csrf
                                            <input type="hidden" name="decision" value="rejected">
                                            <input type="hidden" name="review_note" value="">
                                            <button class="btn btn-ghost btn-sm text-danger-text hover:bg-danger-soft">Tolak</button>
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
