@extends('layouts.app')

@section('title', 'Kalkulator Split Bill Sat-Set')

@section('content')
<div class="p-4 sm:p-6 mx-auto max-w-2xl w-full" x-data="calculator()">
  <div class="flex items-center gap-3 mb-1">
    <div class="w-10 h-10 rounded-2xl bg-[#5D549C]/20 border border-[#A373D3]/30 flex items-center justify-center text-[#A373D3] text-xl">
      <i class="ph-bold ph-calculator"></i>
    </div>
    <div>
      <h1 class="page-title text-xl sm:text-2xl font-black text-white">Kalkulator Split Bill Sat-Set</h1>
      <p class="muted text-xs sm:text-sm text-[#6F70A2]">Hitung patungan makan & nongki bareng circle lu tanpa pusing kalkulator manual.</p>
    </div>
  </div>

  <div class="card mt-6 space-y-4 p-6 bg-[#181224] border border-white/5 rounded-3xl shadow-xl">
    <div>
      <label class="label text-xs font-bold uppercase tracking-wider text-[#6F70A2] mb-1.5 block" for="calc-subtotal">Total Tagihan / Subtotal (Rp)</label>
      <input class="input w-full bg-[#110b17] border border-white/10 rounded-2xl px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:border-[#A373D3]" id="calc-subtotal" type="number" placeholder="cth: 150000" x-model="subtotal">
    </div>
    <div class="grid grid-cols-2 gap-4">
      <div>
        <label class="label text-xs font-bold uppercase tracking-wider text-[#6F70A2] mb-1.5 block" for="calc-discount">Potongan / Diskon</label>
        <input class="input w-full bg-[#110b17] border border-white/10 rounded-2xl px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:border-[#A373D3]" id="calc-discount" type="number" placeholder="0" x-model="discount_value">
      </div>
      <div>
        <label class="label text-xs font-bold uppercase tracking-wider text-[#6F70A2] mb-1.5 block" for="calc-discount-type">Tipe Diskon</label>
        <select class="select w-full bg-[#110b17] border border-white/10 rounded-2xl px-4 py-3 text-white focus:outline-none focus:border-[#A373D3]" id="calc-discount-type" x-model="discount_type">
          <option value="fixed">Nominal (Rp)</option>
          <option value="percent">Persen (%)</option>
        </select>
      </div>
    </div>
    <div class="grid grid-cols-2 gap-4">
      <div>
        <label class="label text-xs font-bold uppercase tracking-wider text-[#6F70A2] mb-1.5 block" for="calc-service">Biaya Service (%)</label>
        <input class="input w-full bg-[#110b17] border border-white/10 rounded-2xl px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:border-[#A373D3]" id="calc-service" type="number" placeholder="0" x-model="service_rate">
      </div>
      <div>
        <label class="label text-xs font-bold uppercase tracking-wider text-[#6F70A2] mb-1.5 block" for="calc-tax">PB1 / Pajak (%)</label>
        <input class="input w-full bg-[#110b17] border border-white/10 rounded-2xl px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:border-[#A373D3]" id="calc-tax" type="number" placeholder="10" x-model="tax_rate">
      </div>
    </div>
    <div>
      <label class="label text-xs font-bold uppercase tracking-wider text-[#6F70A2] mb-1.5 block" for="calc-people">Berapa Anak yang Ikut Patungan?</label>
      <input class="input w-full bg-[#110b17] border border-white/10 rounded-2xl px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:border-[#A373D3]" id="calc-people" type="number" x-model="people_count" min="1">
    </div>

    <button @click="calculate()" :disabled="loading" class="w-full py-3.5 px-4 rounded-2xl bg-[#5D549C] hover:bg-[#4e4587] text-white font-bold text-sm transition shadow-lg flex items-center justify-center gap-2">
      <span x-show="loading" x-cloak class="flex items-center gap-2">
        <i class="ph-bold ph-spinner animate-spin"></i> Lagi Ngitung...
      </span>
      <span x-show="!loading" class="flex items-center gap-2">
        <i class="ph-bold ph-lightning text-[#EB9998]"></i> Gas Hitung Sekarang
      </span>
    </button>

    <p x-show="error" x-cloak x-text="error" class="p-3 rounded-xl bg-red-500/10 border border-red-500/20 text-red-300 text-xs font-medium"></p>
  </div>

  <div x-show="result && !error" x-cloak class="card mt-6 p-6 bg-gradient-to-br from-[#1c142b] to-[#251a3a] border border-[#A373D3]/30 rounded-3xl shadow-xl">
    <div class="flex items-center gap-2 text-[#EB9998] font-bold text-sm">
      <i class="ph-fill ph-check-circle text-lg"></i>
      <span>Beres! Ini Rincian Patungannya</span>
    </div>
    <div class="mt-4 space-y-3 text-sm">
      <div class="flex justify-between items-center py-2 border-b border-white/5 text-gray-300">
        <span>Total Tagihan Bersih:</span>
        <span class="font-black text-white text-base" x-text="'Rp ' + result?.grand_total.toLocaleString('id-ID')"></span>
      </div>
      <div class="flex justify-between items-center py-2 text-white">
        <span class="font-bold text-[#A373D3]">Jatah Patungan Tiap Anak:</span>
        <span class="font-black text-2xl text-[#EB9998]" x-text="'Rp ' + Math.ceil(result?.grand_total / people_count).toLocaleString('id-ID')"></span>
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
