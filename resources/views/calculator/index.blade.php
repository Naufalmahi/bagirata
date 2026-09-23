@extends('layouts.app')

@section('title', 'Kalkulator Patungan')

@section('content')
<div class="mx-auto max-w-2xl" x-data="calculator()">
    <h1 class="text-2xl font-extrabold text-slate-900">Kalkulator Patungan</h1>
    <p class="mt-1 text-slate-500">Hitung tagihan bersama secara presisi.</p>

    <div class="mt-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm space-y-4">
        <div>
            <label class="text-xs font-bold uppercase text-slate-400">Subtotal</label>
            <input type="number" x-model="subtotal" class="mt-1 block w-full rounded-xl border-slate-200 bg-slate-50 text-sm">
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="text-xs font-bold uppercase text-slate-400">Diskon</label>
                <input type="number" x-model="discount_value" class="mt-1 block w-full rounded-xl border-slate-200 bg-slate-50 text-sm">
            </div>
            <div>
                <label class="text-xs font-bold uppercase text-slate-400">Tipe Diskon</label>
                <select x-model="discount_type" class="mt-1 block w-full rounded-xl border-slate-200 bg-slate-50 text-sm">
                    <option value="fixed">Nominal</option>
                    <option value="percent">Persen</option>
                </select>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="text-xs font-bold uppercase text-slate-400">Service (%)</label>
                <input type="number" x-model="service_rate" class="mt-1 block w-full rounded-xl border-slate-200 bg-slate-50 text-sm">
            </div>
            <div>
                <label class="text-xs font-bold uppercase text-slate-400">Pajak (%)</label>
                <input type="number" x-model="tax_rate" class="mt-1 block w-full rounded-xl border-slate-200 bg-slate-50 text-sm">
            </div>
        </div>
        <div>
            <label class="text-xs font-bold uppercase text-slate-400">Jumlah Orang</label>
            <input type="number" x-model="people_count" class="mt-1 block w-full rounded-xl border-slate-200 bg-slate-50 text-sm">
        </div>
        
        <button @click="calculate()" class="w-full rounded-xl bg-indigo-600 py-3 text-sm font-semibold text-white hover:bg-indigo-700">Hitung</button>
    </div>

    <div x-show="result" class="mt-6 rounded-3xl border border-indigo-200 bg-indigo-50 p-6 shadow-sm" x-cloak>
        <h2 class="font-bold text-indigo-900">Hasil Perhitungan</h2>
        <div class="mt-4 space-y-2 text-sm text-indigo-800">
            <div class="flex justify-between"><span>Grand Total:</span> <span class="font-bold" x-text="'Rp ' + result?.grand_total.toLocaleString()"></span></div>
            <div class="flex justify-between"><span>Tiap Orang:</span> <span class="font-bold" x-text="'Rp ' + (result?.grand_total / people_count).toLocaleString()"></span></div>
        </div>
    </div>
</div>

<script>
function calculator() {
    return {
        subtotal: 0,
        discount_type: 'fixed',
        discount_value: 0,
        service_rate: 0,
        tax_rate: 0,
        people_count: 1,
        result: null,
        async calculate() {
            const res = await fetch('/api/v1/calculate-split', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({
                    subtotal: this.subtotal,
                    discount_type: this.discount_type,
                    discount_value: this.discount_value,
                    service_rate: this.service_rate,
                    tax_rate: this.tax_rate,
                    people_count: this.people_count
                })
            });
            this.result = (await res.json()).data;
        }
    }
}
</script>
@endsection
