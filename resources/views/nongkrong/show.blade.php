@extends('layouts.app')

@section('title', $session->name)

@section('content')
    @php $me = auth()->id(); @endphp

    {{-- Header --}}
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <a href="{{ $session->group ? route('groups.show', $session->group) : route('nongkrong.index') }}" class="text-sm font-semibold text-indigo-600 hover:underline">← {{ $session->group ? 'Group' : 'Nongkrong' }}</a>
            <div class="mt-1 flex flex-wrap items-center gap-2">
                <h1 class="text-2xl font-extrabold text-slate-900">{{ $session->name }}</h1>
                <span class="rounded-full px-3 py-1 text-xs font-bold
                    {{ $summary['status'] === 'settled' ? 'bg-emerald-50 text-emerald-700' : ($summary['status'] === 'active' ? 'bg-amber-50 text-amber-700' : 'bg-slate-100 text-slate-600') }}">
                    {{ $summary['status_label'] }}
                </span>
            </div>
            <p class="mt-1 text-sm text-slate-500">
                {{ $session->date?->format('D, d M Y') }} · dibikin {{ $session->creator->name }}
                @if ($session->group && $session->channel) · #{{ $session->channel->name }} @endif
            </p>
        </div>

        @if ($canAddExpense)
            <a href="{{ route('expenses.create', $session) }}" class="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">+ Catat pengeluaran</a>
        @endif
    </div>

    {{-- Summary --}}
    <div class="mt-6 grid gap-4 sm:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-5">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Total dikeluarin</p>
            <p class="mt-1 text-2xl font-extrabold text-slate-900">{{ $summary['total_spent_formatted'] }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Masih pending</p>
            <p class="mt-1 text-2xl font-extrabold {{ $summary['total_pending'] > 0 ? 'text-amber-600' : 'text-emerald-600' }}">{{ $summary['total_pending_formatted'] }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Saldo lo</p>
            @if ($summary['viewer_role'] === 'creditor')
                <p class="mt-1 text-2xl font-extrabold text-emerald-600">+{{ $summary['viewer_balance_formatted'] }}</p>
                <p class="text-xs text-slate-400">dipiutangin, tinggal nagih</p>
            @elseif ($summary['viewer_role'] === 'debtor')
                <p class="mt-1 text-2xl font-extrabold text-rose-600">-{{ $summary['viewer_balance_formatted'] }}</p>
                <p class="text-xs text-slate-400">masih ngutang, buruan bayar 😬</p>
            @else
                <p class="mt-1 text-2xl font-extrabold text-slate-900">0</p>
                <p class="text-xs text-slate-400">fair banget</p>
            @endif
        </div>
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            {{-- Debts --}}
            <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <h2 class="font-bold text-slate-900">Utang-piutang</h2>
                    <a href="{{ route('debts.index', $session) }}" class="text-sm font-semibold text-indigo-600 hover:underline">Kelola →</a>
                </div>

                @php
                    $pendingDebts = $session->debts->where('status', '!=', 'settled')->sortBy('amount');
                    $settledDebts = $session->debts->where('status', 'settled')->sortBy('settled_at');
                @endphp

                @if ($pendingDebts->isEmpty())
                    <p class="mt-3 rounded-xl bg-slate-50 px-3 py-3 text-sm text-slate-400">Nggak ada utang pending, beres semua!</p>
                @else
                    <ul class="mt-3 divide-y divide-slate-100">
                        @foreach ($pendingDebts as $debt)
                            @php
                                $badge = match ($debt->status) {
                                    'payment_reported' => ['bg-amber-50 text-amber-700', 'Nunggu konfirmasi'],
                                    'confirmed' => ['bg-indigo-50 text-indigo-700', 'Dikonfirmasi'],
                                    'rejected' => ['bg-rose-50 text-rose-700', 'Ditolak'],
                                    default => ['bg-slate-100 text-slate-600', 'Belum dibayar'],
                                };
                            @endphp
                            <li class="flex flex-wrap items-center justify-between gap-2 py-3">
                                <p class="text-sm text-slate-700">
                                    @if ($debt->from_user_id === $me)
                                        Lu utang <strong class="text-rose-600">{{ number_format($debt->outstanding(), 0, ',', '.') }}</strong> ke <strong>{{ $debt->creditor->name }}</strong>
                                    @elseif ($debt->to_user_id === $me)
                                        <strong>{{ $debt->debtor->name }}</strong> utang <strong class="text-emerald-600">{{ number_format($debt->outstanding(), 0, ',', '.') }}</strong> ke lu
                                    @else
                                        <strong>{{ $debt->debtor->name }}</strong> utang <strong>{{ number_format($debt->outstanding(), 0, ',', '.') }}</strong> ke <strong>{{ $debt->creditor->name }}</strong>
                                    @endif
                                </p>
                                <div class="flex items-center gap-2">
                                    <span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $badge[0] }}">{{ $badge[1] }}</span>
                                    <a href="{{ route('debts.show', [$session, $debt]) }}"
                                        class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-indigo-600 hover:bg-indigo-50">
                                        {{ $me === $debt->from_user_id ? 'Udah bayar? Lapor' : 'Detail' }}
                                    </a>
                                </div>
                            </li>
                            </li>
                        @endforeach
                    </ul>
                @endif

                @if ($settledDebts->isNotEmpty())
                    <h3 class="mt-4 text-xs font-semibold uppercase tracking-wide text-slate-400">Udah beres</h3>
                    <ul class="mt-2 space-y-1.5">
                        @foreach ($settledDebts as $debt)
                            <li class="text-sm text-slate-400 line-through">
                                <strong>{{ $debt->debtor->name }}</strong> bayar <strong>{{ number_format($debt->amount, 0, ',', '.') }}</strong> ke <strong>{{ $debt->creditor->name }}</strong>
                                <span class="ml-1 text-xs no-underline text-slate-400">({{ $debt->settled_at?->format('d M Y') }})</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>

            {{-- Expenses --}}
            <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <h2 class="font-bold text-slate-900">Pengeluaran</h2>
                    @if ($canAddExpense)
                        <a href="{{ route('expenses.create', $session) }}" class="text-sm font-semibold text-indigo-600 hover:underline">+ catat</a>
                    @endif
                </div>

                @if ($session->expenses->isEmpty())
                    <p class="mt-3 rounded-xl bg-slate-50 px-3 py-3 text-sm text-slate-400">Belum ada pengeluaran. Catat yang pertama dong!</p>
                @else
                    <div class="mt-4 space-y-4">
                        @foreach ($session->expenses->sortByDesc('created_at') as $expense)
                            @php
                                $canEditExpense = $me === $session->user_id || $me === $expense->created_by;
                                if ($session->group && !$canEditExpense) {
                                    $canEditExpense = \App\Services\PermissionService::can(auth()->user(), $session->group, \App\Enums\Permission::MANAGE_PATUNGAN);
                                }
                            @endphp
                            <article class="rounded-2xl border border-slate-200 p-4">
                                <div class="flex flex-wrap items-start justify-between gap-2">
                                    <div>
                                        <h3 class="font-bold text-slate-900">{{ $expense->name }}</h3>
                                        <p class="text-sm text-slate-500">
                                            {{ $expense->category()->label() }} · bayar <strong>{{ $expense->payer->name }}</strong>
                                            @if ($expense->note) · "{{ $expense->note }}" @endif
                                        </p>
                                    </div>
                                    <p class="text-lg font-extrabold text-slate-900">{{ number_format($expense->grandTotal(), 0, ',', '.') }}</p>
                                </div>

                                @if ($expense->discount_value > 0 || $expense->service_rate > 0 || $expense->tax_rate > 0)
                                    <p class="mt-1 text-xs text-slate-400">
                                        @if ($expense->discount_amount() > 0) diskon -{{ number_format($expense->discount_amount(), 0, ',', '.') }} · @endif
                                        @if ($expense->service_rate > 0) service {{ $expense->service_rate }}% ({{ number_format($expense->serviceAmount(), 0, ',', '.') }}) · @endif
                                        @if ($expense->tax_rate > 0) pajak {{ $expense->tax_rate }}% ({{ number_format($expense->taxAmount(), 0, ',', '.') }}) @endif
                                    </p>
                                @endif

                                @if ($expense->receipt_photo)
                                    <a href="{{ url('storage/' . $expense->receipt_photo) }}" target="_blank" class="mt-2 inline-block text-xs font-semibold text-indigo-600 hover:underline">🖼 Lihat struk</a>
                                @endif

                                <div class="mt-3 flex flex-wrap gap-1.5">
                                    @foreach ($expense->splits as $split)
                                        <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs text-slate-600">
                                            {{ $split->user->name }} <strong>{{ number_format($split->share_amount, 0, ',', '.') }}</strong>
                                        </span>
                                    @endforeach
                                </div>

                                @if ($canEditExpense)
                                    <div class="mt-3 flex gap-2">
                                        <a href="{{ route('expenses.edit', [$session, $expense]) }}" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-600 hover:bg-slate-50">Edit</a>
                                        <form method="POST" action="{{ route('expenses.cancel', [$session, $expense]) }}"
                                            onsubmit="return confirm('Batalkan pengeluaran {{ $expense->name }}? Utang bakal diitung ulang.')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="rounded-lg border border-rose-200 px-3 py-1.5 text-xs font-semibold text-rose-600 hover:bg-rose-50">Batalkan</button>
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
            <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="font-bold text-slate-900">Posisi tiap orang</h2>
                <ul class="mt-3 space-y-2.5">
                    @foreach ($session->members as $member)
                        @php $bal = $balances[$member->id] ?? 0; @endphp
                        <li class="flex items-center justify-between gap-2 text-sm">
                            <span class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-indigo-100 font-bold text-indigo-600">{{ strtoupper(substr($member->name, 0, 1)) }}</span>
                            <span class="min-w-0 flex-1 truncate font-semibold text-slate-800">{{ $member->name }}</span>
                            @if ($bal > 0)
                                <span class="font-bold text-emerald-600">+{{ number_format($bal, 0, ',', '.') }}</span>
                            @elseif ($bal < 0)
                                <span class="font-bold text-rose-600">-{{ number_format(abs($bal), 0, ',', '.') }}</span>
                            @else
                                <span class="text-slate-400">0</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </section>

            @if ($session->description)
                <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                    <h2 class="font-bold text-slate-900">Catatan</h2>
                    <p class="mt-1 text-sm text-slate-600">{{ $session->description }}</p>
                </section>
            @endif
        </aside>
    </div>
@endsection