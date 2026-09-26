@php
    $pending = $session->debts->where('status', '!=', \App\Enums\DebtStatus::SETTLED->value)->sum(fn ($debt) => $debt->outstanding());
    $statusBadge = match ($session->status()) {
        'settled' => 'badge-success',
        'active' => 'badge-warning',
        default => 'badge-neutral',
    };
@endphp
<a href="{{ route('nongkrong.show', $session) }}" class="list-row">
    <div class="min-w-0">
        <div class="flex items-center gap-2">
            <h4 class="truncate font-semibold text-ink">{{ $session->name }}</h4>
            @if ($session->group && $session->channel)
                <span class="badge badge-brand shrink-0">#{{ $session->channel->name }}</span>
            @elseif ($session->group)
                <span class="badge badge-neutral shrink-0">group</span>
            @endif
        </div>
        <p class="mt-0.5 truncate text-sm text-ink-muted">
            {{ $session->date?->format('D, d M Y') }} · {{ $session->members->count() }} orang
            @if ($pending > 0)
                · <span class="font-semibold text-warning-text">pending {{ number_format($pending, 0, ',', '.') }}</span>
            @endif
        </p>
    </div>
    <span class="badge {{ $statusBadge }} shrink-0">{{ $session->statusLabel() }}</span>
</a>
