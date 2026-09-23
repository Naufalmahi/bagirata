@php
<<<<<<< HEAD
    $pending = $session->debts->where('status', '!=', \App\Enums\DebtStatus::SETTLED->value)->sum(fn ($debt) => $debt->outstanding());
=======
    $pending = $session->debts->where('status', 'pending')->sum('amount');
>>>>>>> 6561da739345e3ff0fdab546ef4f928a872f067a
    $status = $session->status();
@endphp
<a href="{{ route('nongkrong.show', $session) }}"
    class="flex items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-3 transition hover:border-indigo-300">
    <div class="min-w-0">
        <div class="flex items-center gap-2">
            <h4 class="truncate font-semibold text-slate-900">{{ $session->name }}</h4>
            @if ($session->group && $session->channel)
                <span class="rounded-full bg-indigo-50 px-2 py-0.5 text-[11px] font-semibold text-indigo-600">#{{ $session->channel->name }}</span>
            @elseif ($session->group)
                <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-500">group</span>
            @endif
        </div>
        <p class="mt-0.5 truncate text-sm text-slate-500">
            {{ $session->date?->format('D, d M Y') }} · {{ $session->members->count() }} orang
            @if ($pending > 0)
                · <span class="font-semibold text-amber-600">pending {{ number_format($pending, 0, ',', '.') }}</span>
            @endif
        </p>
    </div>
    <span class="shrink-0 text-xs font-bold {{ $status === 'settled' ? 'text-emerald-600' : ($status === 'active' ? 'text-amber-600' : 'text-slate-400') }}">
        {{ $session->statusLabel() }}
    </span>
</a>