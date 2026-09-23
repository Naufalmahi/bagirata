@extends('layouts.app')

@section('title', 'Edit Pengeluaran')

@section('content')
    <div class="mx-auto max-w-xl">
        <a href="{{ route('nongkrong.show', $session) }}" class="text-sm font-semibold text-indigo-600 hover:underline">← Balik ke {{ $session->name }}</a>
        <h1 class="mt-1 text-2xl font-extrabold text-slate-900">Edit pengeluaran</h1>
        <p class="mt-1 text-sm text-slate-500">Bagian dan utang diitung ulang sesuai perubahan lu.</p>

        @php
            $participantIds = $expense->splits->pluck('user_id')->all();
            $initial = [
                'name' => old('name') ?? $expense->name,
                'amount' => old('amount') ?? $expense->amount,
                'category' => old('category') ?? $expense->category,
                'discount_type' => old('discount_type') ?? $expense->discount_type,
                'discount_value' => old('discount_value') ?? $expense->discount_value,
                'service_rate' => old('service_rate') ?? $expense->service_rate,
                'tax_rate' => old('tax_rate') ?? $expense->tax_rate,
                'split_type' => old('split_type') ?? $expense->split_type,
                'paid_by' => old('paid_by_user_id') ?? $expense->paid_by_user_id,
                'note' => old('note') ?? $expense->note,
                'members' => $session->members->map(fn ($m) => ['id' => $m->id, 'name' => $m->name])->values()->all(),
                'participants' => $session->members->pluck('id')->mapWithKeys(fn ($id) => [$id => in_array($id, $participantIds)])->all(),
                'custom_amounts' => $expense->splits->mapWithKeys(fn ($s) => [$s->user_id => $s->custom_amount])->all(),
                'percentages' => $expense->splits->mapWithKeys(fn ($s) => [$s->user_id => $s->percentage])->all(),
                'preview_url' => route('expenses.preview', $session),
            ];
        @endphp

        <div class="mt-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
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