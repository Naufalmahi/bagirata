@extends('layouts.app')

@section('title', 'Kalkulator Patungan')

@section('content')
<div class="mx-auto max-w-2xl" x-data="calculator()">
    <h1 class="page-title">Kalkulator Patungan</h1>
    <p class="muted mt-1">Hitung tagihan bersama secara presisi.</p>

    <div class="card mt-6 space-y-4 p-6">
        <div>
            <label class="label" for="calc-subtotal">Subtotal</label>
            <input class="input" id="calc-subtotal" type="number" x-model="subtotal">
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="label" for="calc-discount">Diskon</label>
                <input class="input" id="calc-discount" type="number" x-model="discount_value">
            </div>
            <div>
                <label class="label" for="calc-discount-type">Tipe Diskon</label>
                <select class="select" id="calc-discount-type" x-model="discount_type">
                    <option value="fixed">Nominal</option>
                    <option value="percent">Persen</option>
                </select>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="label" for="calc-service">Service (%)</label>
                <input class="input" id="calc-service" type="number" x-model="service_rate">
            </div>
            <div>
                <label class="label" for="calc-tax">Pajak (%)</label>
                <input class="input" id="calc-tax" type="number" x-model="tax_rate">
            </div>
        </div>
        <div>
            <label class="label" for="calc-people">Jumlah Orang</label>
            <input class="input" id="calc-people" type="number" x-model="people_count" min="1">
        </div>

        <button @click="calculate()" :disabled="loading" class="btn btn-primary btn-lg w-full">
            <span x-show="loading" x-cloak>Ngitung...</span>
            <span x-show="!loading">Hitung</span>
        </button>

        <p x-show="error" x-cloak x-text="error" class="field-error"></p>
    </div>

    <div x-show="result && !error" x-cloak class="card mt-6 border-brand/30 bg-brand-soft p-6">
        <h2 class="font-bold text-brand-text">Hasil Perhitungan</h2>
        <div class="mt-4 space-y-2 text-sm text-brand-text">
            <div class="flex justify-between gap-4">
                <span>Grand Total:</span>
                <span class="money font-bold" x-text="'Rp ' + result?.grand_total.toLocaleString('id-ID')"></span>
            </div>
            <div class="flex justify-between gap-4">
                <span>Tiap Orang:</span>
                <span class="money font-bold" x-text="'Rp ' + (result?.grand_total / people_count).toLocaleString('id-ID')"></span>
            </div>
        </div>
    </div>
</div>

@push('scripts')
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
        loading: false,
        error: '',
        async calculate() {
            this.loading = true;
            this.error = '';
            this.result = null;

            try {
                const res = await fetch('/api/v1/calculate-split', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        subtotal: this.subtotal,
                        discount_type: this.discount_type,
                        discount_value: this.discount_value,
                        service_rate: this.service_rate,
                        tax_rate: this.tax_rate,
                        people_count: this.people_count,
                    }),
                });

                const json = await res.json();
                if (!res.ok) {
                    // Sebelum ada penanganan ini, response 401/422 ikut dipakai
                    // sebagai hasil => panel hasil diam-diam nggak muncul.
                    const first = json.errors
                        ? Object.values(json.errors).flat().join(' · ')
                        : json.message;
                    throw new Error(first || 'Gagal ngitung. Coba lagi yaa.');
                }

                this.result = json.data;
            } catch (error) {
                this.error = error.message;
            } finally {
                this.loading = false;
            }
        },
    };
}
</script>
@endpush
@endsection
