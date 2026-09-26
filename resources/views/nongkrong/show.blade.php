@extends('layouts.app')

@section('title', $session->name)

@section('content')
    @php $me = auth()->id(); @endphp

    {{-- Header --}}
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <a href="{{ $session->group ? route('groups.show', $session->group) : route('nongkrong.index') }}" class="back-link">
                ← {{ $session->group ? 'Group' : 'Nongkrong' }}
            </a>
            <div class="mt-1 flex flex-wrap items-center gap-2">
                <h1 class="page-title">{{ $session->name }}</h1>
                <span class="badge {{ match ($summary['status']) {
                    'settled' => 'badge-success',
                    'active' => 'badge-warning',
                    default => 'badge-neutral',
                } }}">{{ $summary['status_label'] }}</span>
            </div>
            <p class="muted mt-1">
                {{ $session->date?->format('D, d M Y') }} · dibikin {{ $session->creator->name }}
                @if ($session->group && $session->channel) · #{{ $session->channel->name }} @endif
            </p>
        </div>

        @if ($canAddExpense)
            <a href="{{ route('expenses.create', $session) }}" class="btn btn-primary">+ Catat pengeluaran</a>
        @endif
    </div>

    {{-- Summary --}}
    <div class="mt-6 grid gap-4 sm:grid-cols-3">
        <div class="stat stat-neutral">
            <p class="stat-label">Total dikeluarin</p>
            <p class="stat-value money">{{ $summary['total_spent_formatted'] }}</p>
        </div>
        <div class="stat {{ $summary['total_pending'] > 0 ? 'stat-warning' : 'stat-success' }}">
            <p class="stat-label">Masih pending</p>
            <p class="stat-value money">{{ $summary['total_pending_formatted'] }}</p>
        </div>
        <div class="stat {{ $summary['viewer_role'] === 'creditor' ? 'stat-success' : ($summary['viewer_role'] === 'debtor' ? 'stat-danger' : 'stat-neutral') }}">
            <p class="stat-label">Saldo lo</p>
            @if ($summary['viewer_role'] === 'creditor')
                <p class="stat-value money">+{{ $summary['viewer_balance_formatted'] }}</p>
                <p class="text-xs text-ink-subtle">dipiutangin, tinggal nagih</p>
            @elseif ($summary['viewer_role'] === 'debtor')
                <p class="stat-value money">-{{ $summary['viewer_balance_formatted'] }}</p>
                <p class="text-xs text-ink-subtle">masih ngutang, buruan bayar 😬</p>
            @else
                <p class="stat-value money">0</p>
                <p class="text-xs text-ink-subtle">fair banget</p>
            @endif
        </div>
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            {{-- Debts --}}
            <section class="card">
                <div class="flex items-center justify-between gap-3">
                    <h2 class="section-title">Utang-piutang</h2>
                    <a href="{{ route('debts.index', $session) }}" class="text-sm font-semibold text-brand-text hover:underline">Kelola →</a>
                </div>

                @php
                    $pendingDebts = $session->debts->where('status', '!=', 'settled')->sortBy('amount');
                    $settledDebts = $session->debts->where('status', 'settled')->sortBy('settled_at');
                @endphp

                @if ($pendingDebts->isEmpty())
                    <p class="empty mt-3">Nggak ada utang pending, beres semua!</p>
                @else
                    <ul class="divide-list mt-3">
                        @foreach ($pendingDebts as $debt)
                            @php
                                [$badgeClass, $badgeLabel] = match ($debt->status) {
                                    'payment_reported' => ['badge-warning', 'Nunggu konfirmasi'],
                                    'confirmed' => ['badge-brand', 'Dikonfirmasi'],
                                    'rejected' => ['badge-danger', 'Ditolak'],
                                    default => ['badge-neutral', 'Belum dibayar'],
                                };
                            @endphp
                            <li class="flex flex-wrap items-center justify-between gap-2 py-3">
                                <p class="text-sm text-ink-muted">
                                    @if ($debt->from_user_id === $me)
                                        Lu utang <strong class="money text-danger-text">{{ number_format($debt->outstanding(), 0, ',', '.') }}</strong> ke <strong class="text-ink">{{ $debt->creditor->name }}</strong>
                                    @elseif ($debt->to_user_id === $me)
                                        <strong class="text-ink">{{ $debt->debtor->name }}</strong> utang <strong class="money text-success-text">{{ number_format($debt->outstanding(), 0, ',', '.') }}</strong> ke lu
                                    @else
                                        <strong class="text-ink">{{ $debt->debtor->name }}</strong> utang <strong class="money">{{ number_format($debt->outstanding(), 0, ',', '.') }}</strong> ke <strong class="text-ink">{{ $debt->creditor->name }}</strong>
                                    @endif
                                </p>
                                <div class="flex items-center gap-2">
                                    <span class="badge {{ $badgeClass }}">{{ $badgeLabel }}</span>
                                    <a href="{{ route('debts.show', [$session, $debt]) }}" class="btn btn-secondary btn-sm">
                                        {{ $me === $debt->from_user_id ? 'Udah bayar? Lapor' : 'Detail' }}
                                    </a>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif

                @if ($settledDebts->isNotEmpty())
                    <h3 class="mt-4 text-xs font-semibold uppercase tracking-wide text-ink-subtle">Udah beres</h3>
                    <ul class="mt-2 space-y-1.5">
                        @foreach ($settledDebts as $debt)
                            <li class="text-sm text-ink-subtle line-through">
                                <strong>{{ $debt->debtor->name }}</strong> bayar
                                <strong class="money">{{ number_format($debt->amount, 0, ',', '.') }}</strong> ke
                                <strong>{{ $debt->creditor->name }}</strong>
                                <span class="ml-1 text-xs no-underline">({{ $debt->settled_at?->format('d M Y') }})</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>

            {{-- Expenses --}}
            <section class="card">
                <div class="flex items-center justify-between gap-3">
                    <h2 class="section-title">Pengeluaran</h2>
                    @if ($canAddExpense)
                        <a href="{{ route('expenses.create', $session) }}" class="text-sm font-semibold text-brand-text hover:underline">+ catat</a>
                    @endif
                </div>

                @if ($session->expenses->isEmpty())
                    <p class="empty mt-3">Belum ada pengeluaran. Catat yang pertama dong!</p>
                @else
                    <div class="mt-4 space-y-4">
                        @foreach ($session->expenses->sortByDesc('created_at') as $expense)
                            @php
                                $canEditExpense = $me === $session->user_id || $me === $expense->created_by;
                                if ($session->group && !$canEditExpense) {
                                    $canEditExpense = \App\Services\PermissionService::can(auth()->user(), $session->group, \App\Enums\Permission::MANAGE_PATUNGAN);
                                }
                            @endphp
                            <article class="rounded-card border border-line p-4">
                                <div class="flex flex-wrap items-start justify-between gap-2">
                                    <div class="min-w-0">
                                        <h3 class="font-bold text-ink">{{ $expense->name }}</h3>
                                        <p class="text-sm text-ink-muted">
                                            {{ $expense->category()->label() }} · bayar <strong class="text-ink">{{ $expense->payer->name }}</strong>
                                            @if ($expense->note) · "{{ $expense->note }}" @endif
                                        </p>
                                    </div>
                                    <p class="money text-lg font-extrabold text-ink">{{ number_format($expense->grandTotal(), 0, ',', '.') }}</p>
                                </div>

                                @if ($expense->discount_value > 0 || $expense->service_rate > 0 || $expense->tax_rate > 0)
                                    <p class="mt-1 text-xs text-ink-subtle">
                                        @if ($expense->discount_amount() > 0) diskon -{{ number_format($expense->discount_amount(), 0, ',', '.') }} · @endif
                                        @if ($expense->service_rate > 0) service {{ $expense->service_rate }}% ({{ number_format($expense->serviceAmount(), 0, ',', '.') }}) · @endif
                                        @if ($expense->tax_rate > 0) pajak {{ $expense->tax_rate }}% ({{ number_format($expense->taxAmount(), 0, ',', '.') }}) @endif
                                    </p>
                                @endif

                                @if ($expense->receipt_photo)
                                    <a href="{{ url('storage/' . $expense->receipt_photo) }}" target="_blank" rel="noopener"
                                        class="mt-2 inline-block text-xs font-semibold text-brand-text hover:underline">🖼 Lihat struk</a>
                                @endif

                                <div class="mt-3 flex flex-wrap gap-1.5">
                                    @foreach ($expense->splits as $split)
                                        <span class="badge badge-neutral">
                                            {{ $split->user->name }} <strong class="money">{{ number_format($split->share_amount, 0, ',', '.') }}</strong>
                                        </span>
                                    @endforeach
                                </div>

                                @if ($canEditExpense)
                                    <div class="mt-3 flex gap-2">
                                        <a href="{{ route('expenses.edit', [$session, $expense]) }}" class="btn btn-secondary btn-sm">Edit</a>
                                        <form method="POST" action="{{ route('expenses.cancel', [$session, $expense]) }}"
                                            onsubmit="return confirm('Batalkan pengeluaran {{ $expense->name }}? Utang bakal dihitung ulang.')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-ghost btn-sm text-danger-text hover:bg-danger-soft">Batalkan</button>
                                        </form>
                                    </div>
                                @endif
                            </article>
                        @endforeach
                    </div>
                @endif
            </section>
        </div>

        {{-- Member balances --}}
        <aside class="h-fit space-y-6">
            <section class="card">
                <h2 class="section-title">Posisi tiap orang</h2>
                <ul class="mt-3 space-y-2.5">
                    @foreach ($session->members as $member)
                        @php $bal = $balances[$member->id] ?? 0; @endphp
                        <li class="flex items-center justify-between gap-2 text-sm">
                            <span class="avatar">{{ strtoupper(substr($member->name, 0, 1)) }}</span>
                            <span class="min-w-0 flex-1 truncate font-semibold text-ink">{{ $member->name }}</span>
                            @if ($bal > 0)
                                <span class="money font-bold text-success-text">+{{ number_format($bal, 0, ',', '.') }}</span>
                            @elseif ($bal < 0)
                                <span class="money font-bold text-danger-text">-{{ number_format(abs($bal), 0, ',', '.') }}</span>
                            @else
                                <span class="text-ink-subtle">0</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </section>

            @if ($session->description)
                <section class="card">
                    <h2 class="section-title">Catatan</h2>
                    <p class="mt-1 text-sm text-ink-muted">{{ $session->description }}</p>
                </section>
            @endif
        </aside>
    </div>
@endsection
