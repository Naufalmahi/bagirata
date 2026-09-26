@extends('layouts.app')

@section('title', 'Catat Pengeluaran')

@section('content')
    <div class="mx-auto max-w-xl">
        <a href="{{ route('nongkrong.show', $session) }}" class="back-link">← Balik ke {{ $session->name }}</a>
        <h1 class="page-title mt-1">Catat pengeluaran</h1>
        <p class="muted mt-1">Utang-piutang bakal langsung diitung ulang otomatis.</p>

        @php
            $initial = [
                'name' => old('name') ?? '',
                'amount' => old('amount') ?? '',
                'category' => old('category') ?? 'makan',
                'discount_type' => old('discount_type') ?? 'fixed',
                'discount_value' => old('discount_value') ?? '',
                'service_rate' => old('service_rate') ?? '',
                'tax_rate' => old('tax_rate') ?? '',
                'split_type' => old('split_type') ?? 'equal',
                'paid_by' => old('paid_by_user_id') ?? auth()->id(),
                'note' => old('note') ?? '',
                'members' => $session->members->map(fn ($m) => ['id' => $m->id, 'name' => $m->name])->values()->all(),
                'participants' => $session->members->pluck('id')->mapWithKeys(fn ($id) => [$id => true])->all(),
                'custom_amounts' => new stdClass(),
                'percentages' => new stdClass(),
                'preview_url' => route('expenses.preview', $session),
            ];
        @endphp

        <div class="card mt-6 p-6">
            @include('expenses._form', [
                'action' => route('expenses.store', $session),
                'method' => 'POST',
                'buttonLabel' => 'Catat & hitung!',
                'initial' => $initial,
            ])
        </div>
    </div>
@endsection
