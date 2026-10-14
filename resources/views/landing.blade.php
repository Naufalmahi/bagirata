@extends('layouts.app')

@section('title', 'BagiRata - Solusi Patungan & Split Bill Tanpa Drama')

@section('content')
<div class="w-full bg-transparent text-white">
    {{-- ========================================================
         HERO SECTION: Discord Dark Aesthetic
         ======================================================== --}}
    <section class="relative overflow-hidden pt-8 pb-16 lg:pt-14 lg:pb-24">
        {{-- Ambient glowing blur --}}
        <div class="pointer-events-none absolute -top-24 left-1/2 -z-10 h-[500px] w-[800px] -translate-x-1/2 rounded-full bg-gradient-to-tr from-[#5D549C]/20 via-[#7E3A63]/20 to-[#EB9998]/20 blur-3xl"></div>

        <div class="mx-auto max-w-6xl px-4 sm:px-6">
            <div class="flex flex-col items-center text-center">
                {{-- Badge Pengumuman --}}
                <div class="inline-flex items-center gap-2.5 rounded-full border border-[#A373D3]/30 bg-[#5D549C]/30 px-4 py-1.5 text-xs font-bold text-[#A373D3] shadow-md transition-all hover:bg-[#5D549C]/40">
                    <span class="flex h-2 w-2 rounded-full bg-[#EB9998] animate-pulse"></span>
                    <span>Platform Patungan & Split Bill #1 Terlengkap</span>
                    <span class="rounded-md bg-white/10 border border-white/10 px-1.5 py-0.5 text-[10px] font-extrabold text-[#EB9998]">GRATIS</span>
                </div>

                {{-- Headline Utama --}}
                <h1 class="mt-8 max-w-4xl text-3xl font-black tracking-tight text-white sm:text-5xl lg:text-6xl lg:leading-[1.15]">
                    Nongkrong Lebih Santai,<br>
                    <span class="bg-gradient-to-r from-[#5D549C] via-[#7E3A63] to-[#EB9998] bg-clip-text text-transparent">
                        Hitung Patungan Tanpa Drama Uang.
                    </span>
                </h1>

                {{-- Deskripsi Pendukung --}}
                <p class="mt-5 max-w-2xl text-sm sm:text-base text-[#a19db0] leading-relaxed">
                    BagiRata membantu kamu dan circle nongkrong membagi tagihan makan, kafe, jalan-jalan, hingga kas bersama secara adil, cepat, dan transparan. Nggak ada lagi hitung manual bertele-tele!
                </p>

                {{-- Tombol Aksi CTA --}}
                <div class="mt-8 flex flex-wrap items-center justify-center gap-3 sm:gap-4">
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-[#5D549C] via-[#7E3A63] to-[#EB9998] px-7 py-3.5 text-sm sm:text-base font-bold text-white shadow-lg shadow-[#5D549C]/30 transition-all duration-200 hover:opacity-90 hover:shadow-xl hover:-translate-y-0.5">
                        <span>Mulai Sekarang — Gratis</span>
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>

                    <a href="{{ route('calculator.index') }}" class="inline-flex items-center justify-center gap-2 rounded-2xl border border-white/10 bg-white/5 px-6 py-3.5 text-sm sm:text-base font-bold text-white shadow-sm transition hover:bg-white/10 hover:border-white/20">
                        <svg class="h-5 w-5 text-[#A373D3]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        <span>Coba Kalkulator Cepat</span>
                    </a>
                </div>

                {{-- Mini Trust Indicators --}}
                <div class="mt-10 flex flex-wrap items-center justify-center gap-6 sm:gap-10 text-xs sm:text-sm font-semibold text-[#6F70A2]">
                    <div class="flex items-center gap-2">
                        <svg class="h-5 w-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                        <span class="text-gray-300">100% Bebas Biaya</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="h-5 w-5 text-[#A373D3]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        <span class="text-gray-300">Hitung Adil & Transparan</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="h-5 w-5 text-[#EB9998]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        <span class="text-gray-300">Penyederhanaan Utang Otomatis</span>
                    </div>
                </div>
            </div>

            {{-- INTERACTIVE PREVIEW CARD SHOWCASE --}}
            <div class="mt-14 sm:mt-16 mx-auto max-w-4xl">
                <div class="glass-card rounded-3xl border border-white/10 p-3 sm:p-5 shadow-2xl backdrop-blur-xl">
                    {{-- Browser-like bar --}}
                    <div class="flex items-center justify-between border-b border-white/10 pb-3 px-2">
                        <div class="flex items-center gap-2">
                            <span class="h-3 w-3 rounded-full bg-red-500"></span>
                            <span class="h-3 w-3 rounded-full bg-yellow-500"></span>
                            <span class="h-3 w-3 rounded-full bg-green-500"></span>
                        </div>
                        <div class="flex items-center gap-2 rounded-lg bg-black/40 border border-white/5 px-4 py-1 text-xs text-[#6F70A2] font-mono">
                            <svg class="h-3.5 w-3.5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            bagirata.id/nongkrong/kopi-sore-circle
                        </div>
                        <span class="text-xs font-semibold text-emerald-400 bg-emerald-500/20 border border-emerald-500/30 px-2 py-0.5 rounded-md">Live Split Bill</span>
                    </div>

                    {{-- Mockup Content --}}
                    <div class="mt-4 grid gap-4 md:grid-cols-3 p-2 sm:p-4">
                        {{-- Ringkasan Sesi --}}
                        <div class="rounded-2xl border border-white/5 bg-[#110b17]/80 p-4">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold uppercase tracking-wider text-[#6F70A2]">Sesi Terpilih</span>
                                <span class="rounded-full bg-[#5D549C]/30 border border-[#A373D3]/30 px-2 py-0.5 text-[11px] font-bold text-[#A373D3]">4 Teman</span>
                            </div>
                            <h4 class="mt-2 text-base font-black text-white">☕ Kopi & Makan Sore</h4>
                            <p class="text-xs text-[#a19db0]">Cafe Senja, Jakarta Selatan</p>

                            <div class="mt-4 border-t border-white/10 pt-3 space-y-1.5 text-xs">
                                <div class="flex justify-between text-[#a19db0]">
                                    <span>Subtotal (8 item)</span>
                                    <span class="font-semibold text-white">Rp240.000</span>
                                </div>
                                <div class="flex justify-between text-[#a19db0]">
                                    <span>Pajak PB1 (10%)</span>
                                    <span class="font-semibold text-white">Rp24.000</span>
                                </div>
                                <div class="flex justify-between text-[#a19db0]">
                                    <span>Service (5%)</span>
                                    <span class="font-semibold text-white">Rp12.000</span>
                                </div>
                                <div class="flex justify-between font-bold text-white border-t border-dashed border-white/10 pt-1.5 text-sm">
                                    <span>Total Akhir</span>
                                    <span class="text-[#EB9998]">Rp276.000</span>
                                </div>
                            </div>
                        </div>

                        {{-- Siapa Bayar Apa --}}
                        <div class="rounded-2xl border border-white/5 bg-[#1f1a2b]/80 p-4 shadow-sm md:col-span-2">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-xs font-bold uppercase tracking-wider text-[#6F70A2]">Breakdown Patungan Otomatis</span>
                                <span class="text-xs font-semibold text-[#A373D3]">Talangan oleh Budi</span>
                            </div>

                            <div class="space-y-2.5">
                                <div class="flex items-center justify-between rounded-xl bg-black/30 border border-white/5 p-2.5 text-xs">
                                    <div class="flex items-center gap-2.5">
                                        <div class="flex h-7 w-7 items-center justify-center rounded-full bg-[#5D549C] text-white font-bold text-xs">R</div>
                                        <div>
                                            <p class="font-bold text-white">Rian (Latte + Croissant)</p>
                                            <p class="text-[11px] text-[#6F70A2]">Item: 65k + Pajak proporsional</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold text-white">Rp74.750</p>
                                        <span class="inline-block rounded-md bg-[#EB9998]/20 border border-[#EB9998]/40 px-1.5 py-0.5 text-[10px] font-bold text-[#EB9998]">Pending</span>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between rounded-xl bg-black/30 border border-white/5 p-2.5 text-xs">
                                    <div class="flex items-center gap-2.5">
                                        <div class="flex h-7 w-7 items-center justify-center rounded-full bg-[#7E3A63] text-white font-bold text-xs">S</div>
                                        <div>
                                            <p class="font-bold text-white">Sarah (Americano)</p>
                                            <p class="text-[11px] text-[#6F70A2]">Item: 35k + Pajak proporsional</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold text-white">Rp40.250</p>
                                        <span class="inline-block rounded-md bg-emerald-500/20 border border-emerald-500/30 px-1.5 py-0.5 text-[10px] font-bold text-emerald-400">Lunas via QRIS</span>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between rounded-xl bg-black/30 border border-white/5 p-2.5 text-xs">
                                    <div class="flex items-center gap-2.5">
                                        <div class="flex h-7 w-7 items-center justify-center rounded-full bg-[#A373D3] text-white font-bold text-xs">D</div>
                                        <div>
                                            <p class="font-bold text-white">Dimas (Pasta Creamy)</p>
                                            <p class="text-[11px] text-[#6F70A2]">Item: 75k + Pajak proporsional</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold text-white">Rp86.250</p>
                                        <span class="inline-block rounded-md bg-emerald-500/20 border border-emerald-500/30 px-1.5 py-0.5 text-[10px] font-bold text-emerald-400">Lunas</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ========================================================
         SECTION FITUR LENGKAP: Menjelaskan Semua Fitur BagiRata
         ======================================================== --}}
    <section id="fitur" class="border-t border-white/5 bg-black/20 py-16 sm:py-24">
        <div class="mx-auto max-w-6xl px-4 sm:px-6">
            <div class="text-center max-w-3xl mx-auto">
                <span class="inline-block rounded-full bg-[#5D549C]/30 border border-[#A373D3]/30 px-3.5 py-1 text-xs font-bold uppercase tracking-wider text-[#A373D3] mb-3">
                    Fitur Lengkap
                </span>
                <h2 class="text-2xl font-black text-white sm:text-4xl">
                    Semua Kebutuhan Patungan Kamu,<br>
                    <span class="bg-gradient-to-r from-[#5D549C] via-[#7E3A63] to-[#EB9998] bg-clip-text text-transparent">
                        Tersedia Lengkap di BagiRata
                    </span>
                </h2>
                <p class="mt-3 text-sm sm:text-base text-[#a19db0]">
                    Bukan sekadar kalkulator biasa. BagiRata dirancang untuk menyelesaikan semua kerumitan urusan bayar-membayar bareng teman.
                </p>
            </div>

            {{-- Grid Fitur 6 Kartu Utama --}}
            <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                {{-- Fitur 1: Split Bill Cepat --}}
                <div class="glass-card group relative rounded-3xl p-7 transition-all duration-300 hover:-translate-y-1.5 hover:border-[#A373D3]/50 hover:shadow-[0_0_30px_rgba(163,115,211,0.2)]">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-tr from-[#5D549C] to-[#A373D3] text-white shadow-md shadow-[#5D549C]/40 mb-6 transition-transform group-hover:scale-110">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-white">Kalkulator Split Bill Instan</h3>
                    <p class="mt-2 text-xs sm:text-sm text-[#a19db0] leading-relaxed">
                        Mau hitung cepat tanpa repot? Cukup masukkan total bill dan jumlah orang. Otomatis bagi rata beserta tip, diskon, dan pajak secara akurat tanpa sisa sen.
                    </p>
                    <div class="mt-5 flex items-center text-xs font-bold text-[#A373D3]">
                        <span>Langsung Coba Tanpa Login</span>
                        <svg class="h-3.5 w-3.5 ml-1 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </div>

                {{-- Fitur 2: Sesi Nongkrong & Split Itemized --}}
                <div class="glass-card group relative rounded-3xl p-7 transition-all duration-300 hover:-translate-y-1.5 hover:border-[#EB9998]/50 hover:shadow-[0_0_30px_rgba(235,153,152,0.2)]">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-tr from-[#7E3A63] to-[#EB9998] text-white shadow-md shadow-[#7E3A63]/40 mb-6 transition-transform group-hover:scale-110">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-white">Sesi Nongkrong (Itemized Split)</h3>
                    <p class="mt-2 text-xs sm:text-sm text-[#a19db0] leading-relaxed">
                        Pesanan tiap orang beda-beda? Catat per item menu, tetapkan siapa saja yang makan makanan tersebut, dan sistem akan membagi biaya + pajak secara proporsional.
                    </p>
                    <div class="mt-5 flex items-center text-xs font-bold text-[#EB9998]">
                        <span>Adil Sesuai Pesanan Masing-Masing</span>
                    </div>
                </div>

                {{-- Fitur 3: Algoritma Penyederhanaan Utang --}}
                <div class="glass-card group relative rounded-3xl p-7 transition-all duration-300 hover:-translate-y-1.5 hover:border-[#A373D3]/50 hover:shadow-[0_0_30px_rgba(163,115,211,0.2)]">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-tr from-[#5D549C] to-[#7E3A63] text-white shadow-md shadow-[#5D549C]/40 mb-6 transition-transform group-hover:scale-110">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-white">Debt Simplification Pintar</h3>
                    <p class="mt-2 text-xs sm:text-sm text-[#a19db0] leading-relaxed">
                        A utang ke B, B utang ke C? Algoritma BagiRata memangkas jalur transfer berbelit-belit menjadi jumlah transaksi paling minimum. Lebih hemat biaya admin transfer!
                    </p>
                    <div class="mt-5 flex items-center text-xs font-bold text-[#A373D3]">
                        <span>Minimal Transaksi Antar Teman</span>
                    </div>
                </div>

                {{-- Fitur 4: Group Circles & Kas Bersama (Treasury) --}}
                <div class="glass-card group relative rounded-3xl p-7 transition-all duration-300 hover:-translate-y-1.5 hover:border-emerald-400/50 hover:shadow-[0_0_30px_rgba(52,211,153,0.2)]">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-600 text-white shadow-md shadow-emerald-500/30 mb-6 transition-transform group-hover:scale-110">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-white">Group Circles & Kas Grup</h3>
                    <p class="mt-2 text-xs sm:text-sm text-[#a19db0] leading-relaxed">
                        Buat circle tetap untuk geng nongkrong, trip liburan, atau teman kost. Kelola saldo kas bersama (treasury) dengan transparansi pencatatan dan approval.
                    </p>
                    <div class="mt-5 flex items-center text-xs font-bold text-emerald-400">
                        <span>Pencatatan Saldo Transparan</span>
                    </div>
                </div>

                {{-- Fitur 5: Budgeting & Monitoring Pengeluaran --}}
                <div class="glass-card group relative rounded-3xl p-7 transition-all duration-300 hover:-translate-y-1.5 hover:border-amber-400/50 hover:shadow-[0_0_30px_rgba(251,191,36,0.2)]">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-tr from-amber-600 to-[#7E3A63] text-white shadow-md shadow-amber-500/30 mb-6 transition-transform group-hover:scale-110">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-white">Budgeting Nongkrong</h3>
                    <p class="mt-2 text-xs sm:text-sm text-[#a19db0] leading-relaxed">
                        Pasang batas budget nongkrong tiap bulan. Ketahui persis sisa jatah nongkrong kamu agar keuangan tetap aman dan tidak boncos di akhir bulan.
                    </p>
                    <div class="mt-5 flex items-center text-xs font-bold text-amber-400">
                        <span>Anti Kanker (Kantong Kering)</span>
                    </div>
                </div>

                {{-- Fitur 6: Konfirmasi Pembayaran & Riwayat Digital --}}
                <div class="glass-card group relative rounded-3xl p-7 transition-all duration-300 hover:-translate-y-1.5 hover:border-[#6F70A2]/50 hover:shadow-[0_0_30px_rgba(111,112,162,0.2)]">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-tr from-[#5D549C] to-[#6F70A2] text-white shadow-md shadow-[#5D549C]/30 mb-6 transition-transform group-hover:scale-110">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-white">Konfirmasi Status & Bukti Transfer</h3>
                    <p class="mt-2 text-xs sm:text-sm text-[#a19db0] leading-relaxed">
                        Tandai pembayaran yang sudah ditransfer, upload bukti bayar, dan minta konfirmasi penerima. Status utang otomatis terupdate jadi lunas!
                    </p>
                    <div class="mt-5 flex items-center text-xs font-bold text-[#6F70A2]">
                        <span>Pencatatan Rapi & Terverifikasi</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ========================================================
         CARA KERJA: 3 Langkah Mudah BagiRata
         ======================================================== --}}
    <section id="cara-kerja" class="py-16 sm:py-24 bg-transparent">
        <div class="mx-auto max-w-6xl px-4 sm:px-6">
            <div class="text-center max-w-2xl mx-auto mb-14">
                <span class="inline-block rounded-full bg-[#5D549C]/30 border border-[#A373D3]/30 px-3.5 py-1 text-xs font-bold uppercase tracking-wider text-[#A373D3] mb-3">
                    Mudah & Cepat
                </span>
                <h2 class="text-2xl font-black text-white sm:text-4xl">
                    Cara Kerja BagiRata
                </h2>
                <p class="mt-3 text-sm sm:text-base text-[#a19db0]">
                    Hanya butuh 3 langkah singkat untuk menyelesaikan urusan patungan tanpa ribet.
                </p>
            </div>

            <div class="grid gap-8 md:grid-cols-3 relative">
                {{-- Step 1 --}}
                <div class="glass-card flex flex-col items-center text-center p-6 rounded-3xl border border-white/10">
                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-tr from-[#5D549C] to-[#A373D3] text-white text-xl font-black shadow-lg shadow-[#5D549C]/30 mb-5">
                        1
                    </div>
                    <h3 class="text-base sm:text-lg font-bold text-white">Bikin Sesi atau Masukkan Bill</h3>
                    <p class="mt-2 text-xs sm:text-sm text-[#a19db0] leading-relaxed">
                        Tulis nama tempat nongkrong atau acara, lalu masukkan total tagihan struk atau input rincian pesanan masing-masing.
                    </p>
                </div>

                {{-- Step 2 --}}
                <div class="glass-card flex flex-col items-center text-center p-6 rounded-3xl border border-white/10">
                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-tr from-[#7E3A63] to-[#EB9998] text-white text-xl font-black shadow-lg shadow-[#7E3A63]/30 mb-5">
                        2
                    </div>
                    <h3 class="text-base sm:text-lg font-bold text-white">Tandai Teman & Talangan</h3>
                    <p class="mt-2 text-xs sm:text-sm text-[#a19db0] leading-relaxed">
                        Pilih siapa saja teman yang ikut nongkrong dan siapa yang menalangi pembayaran pertama di kasir.
                    </p>
                </div>

                {{-- Step 3 --}}
                <div class="glass-card flex flex-col items-center text-center p-6 rounded-3xl border border-white/10">
                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-tr from-[#5D549C] to-[#6F70A2] text-white text-xl font-black shadow-lg shadow-[#5D549C]/30 mb-5">
                        3
                    </div>
                    <h3 class="text-base sm:text-lg font-bold text-white">BagiRata Bereskan Semuanya</h3>
                    <p class="mt-2 text-xs sm:text-sm text-[#a19db0] leading-relaxed">
                        Kalkulasi keluar seketika, tagihan langsung dibagi adil, dan alur transfer disederhanakan tanpa ribet.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- ========================================================
         FAQ SECTION: Pertanyaan yang Sering Diajukan
         ======================================================== --}}
    <section id="faq" class="border-t border-white/5 bg-black/20 py-16 sm:py-24">
        <div class="mx-auto max-w-4xl px-4 sm:px-6">
            <div class="text-center mb-12">
                <span class="inline-block rounded-full bg-[#5D549C]/30 border border-[#A373D3]/30 px-3.5 py-1 text-xs font-bold uppercase tracking-wider text-[#A373D3] mb-3">
                    Bantuan
                </span>
                <h2 class="text-2xl font-black text-white sm:text-4xl">
                    Pertanyaan yang Sering Diajukan
                </h2>
                <p class="mt-3 text-sm sm:text-base text-[#a19db0]">
                    Punya pertanyaan seputar BagiRata? Temukan jawabannya di sini.
                </p>
            </div>

            <div class="space-y-4">
                <div class="glass-card rounded-2xl border border-white/10 p-6 shadow-sm">
                    <h4 class="text-base font-bold text-white">Apakah BagiRata benar-benar gratis?</h4>
                    <p class="mt-2 text-xs sm:text-sm text-[#a19db0] leading-relaxed">
                        Ya, 100% gratis! Kamu bisa menggunakan kalkulator cepat, membuat sesi nongkrong, mengelola grup, hingga kas bersama tanpa dipungut biaya apapun.
                    </p>
                </div>

                <div class="glass-card rounded-2xl border border-white/10 p-6 shadow-sm">
                    <h4 class="text-base font-bold text-white">Bagaimana jika salah satu teman memesan menu yang jauh lebih mahal?</h4>
                    <p class="mt-2 text-xs sm:text-sm text-[#a19db0] leading-relaxed">
                        Fitur itemized split di BagiRata dirancang khusus untuk ini. Kamu cukup mencatat menu masing-masing, sehingga yang memesan menu mahal hanya membayar porsinya sendiri ditambah persentase pajak yang adil.
                    </p>
                </div>

                <div class="glass-card rounded-2xl border border-white/10 p-6 shadow-sm">
                    <h4 class="text-base font-bold text-white">Apa itu fitur penyederhanaan utang (debt simplification)?</h4>
                    <p class="mt-2 text-xs sm:text-sm text-[#a19db0] leading-relaxed">
                        Jika dalam circle beberapa orang saling berutang di momen berbeda, BagiRata akan mengkalkulasikan saldo bersih dan menyederhanakan transaksi sehingga transfer antar teman jadi seminimal mungkin.
                    </p>
                </div>

                <div class="glass-card rounded-2xl border border-white/10 p-6 shadow-sm">
                    <h4 class="text-base font-bold text-white">Apakah semua teman harus mendaftar akun?</h4>
                    <p class="mt-2 text-xs sm:text-sm text-[#a19db0] leading-relaxed">
                        Untuk kalkulator instan, tidak perlu mendaftar sama sekali. Untuk sesi nongkrong grup permanen, teman dapat diundang lewat link invite untuk langsung bergabung dan melihat catatan patungan bersama.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- ========================================================
         BANNER CTA BAWAH: Gradien Palette Ungu-Plum-Coral
         ======================================================== --}}
    <section class="py-14 sm:py-20 bg-transparent">
        <div class="mx-auto max-w-5xl px-4 sm:px-6">
            <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-[#5D549C] via-[#7E3A63] to-[#EB9998] px-6 py-12 text-center text-white shadow-2xl shadow-[#5D549C]/30 sm:px-12 sm:py-16 border border-white/15">
                {{-- Decorative circles --}}
                <div class="absolute -top-12 -right-12 h-48 w-48 rounded-full bg-white/10 blur-xl"></div>
                <div class="absolute -bottom-12 -left-12 h-48 w-48 rounded-full bg-white/10 blur-xl"></div>

                <div class="relative z-10 max-w-2xl mx-auto">
                    <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-white/20 p-2 shadow-inner backdrop-blur-md">
                        <img src="{{ asset('image/logo.png') }}" alt="Logo BagiRata" class="h-10 w-auto object-contain brightness-0 invert">
                    </div>
                    <h2 class="text-2xl sm:text-4xl font-black">
                        Siap Nongkrong Bebas Drama?
                    </h2>
                    <p class="mt-4 text-xs sm:text-base text-white/90">
                        Gabung ribuan anak nongkrong yang sudah beralih ke BagiRata. Cepat, transparan, dan pastinya gratis selamanya.
                    </p>
                    <div class="mt-8 flex flex-wrap items-center justify-center gap-4">
                        <a href="{{ route('register') }}" class="rounded-2xl bg-white px-8 py-3.5 text-xs sm:text-sm font-bold text-[#1f1a2b] shadow-xl transition-all duration-200 hover:bg-gray-100 hover:scale-105">
                            Daftar Sekarang — Gratis
                        </a>
                        <a href="{{ route('login') }}" class="rounded-2xl border border-white/30 bg-white/10 px-6 py-3.5 text-xs sm:text-sm font-bold text-white backdrop-blur-sm transition hover:bg-white/20">
                            Masuk ke Akun
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
