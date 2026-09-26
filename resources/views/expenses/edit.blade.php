@extends('layouts.app')

@section('title', 'Edit Pengeluaran')

@section('content')
    <div class="mx-auto max-w-xl">
        <a href="{{ route('nongkrong.show', $session) }}" class="back-link">← Balik ke {{ $session->name }}</a>
        <h1 class="page-title mt-1">Edit pengeluaran</h1>
        <p class="muted mt-1">Bagian dan utang diitung ulang sesuai perubahan lu.</p>

        @php
            $participantIds = $expense->splits->pluck('user_id')->all();
            // split_type disimpan per baris di expense_splits, bukan di tabel expenses.
            // Semua baris berbagi nilai yang sama karena form cuma punya satu pilihan.
            $currentSplitType = $expense->splits->first()?->split_type ?? \App\Enums\SplitType::EQUAL->value;
            $initial = [
                'name' => old('name') ?? $expense->name,
                'amount' => old('amount') ?? $expense->amount,
                'category' => old('category') ?? $expense->category,
                'discount_type' => old('discount_type') ?? $expense->discount_type,
                'discount_value' => old('discount_value') ?? $expense->discount_value,
                'service_rate' => old('service_rate') ?? $expense->service_rate,
                'tax_rate' => old('tax_rate') ?? $expense->tax_rate,
                'split_type' => old('split_type') ?? $currentSplitType,
                'paid_by' => old('paid_by_user_id') ?? $expense->paid_by_user_id,
                'note' => old('note') ?? $expense->note,
                'members' => $session->members->map(fn ($m) => ['id' => $m->id, 'name' => $m->name])->values()->all(),
                'participants' => $session->members->pluck('id')->mapWithKeys(fn ($id) => [$id => in_array($id, $participantIds)])->all(),
                'custom_amounts' => $expense->splits->mapWithKeys(fn ($s) => [$s->user_id => $s->custom_amount])->all(),
                'percentages' => $expense->splits->mapWithKeys(fn ($s) => [$s->user_id => $s->percentage])->all(),
                'preview_url' => route('expenses.preview', $session),
            ];
        @endphp

        <div class="card mt-6 p-6">
            @include('expenses._form', [
                'action' => route('expenses.update', [$session, $expense]),
                'method' => 'PATCH',
                'buttonLabel' => 'Simpan perubahan',
                'initial' => $initial,
                'expense' => $expense,
            ])
        </div>
    </div>
@endsection
