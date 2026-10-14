@extends('layouts.app')

@section('title', 'BagiRata · Tempat Nongkrong Santuy, Split Bill Bebas Drama')

@section('content')
<div class="w-full bg-[#0c0814] text-white selection:bg-[#EB9998]/30 selection:text-white overflow-x-hidden">

  {{-- ========================================================
     1. HERO SECTION: Discord 2026 2-Column Hero Aesthetic (Bahasa Gaul)
     ======================================================== --}}
  <section class="relative overflow-hidden pt-8 pb-16 lg:pt-16 lg:pb-24 border-b border-white/5">
    {{-- Ambient background glows --}}
    <div class="pointer-events-none absolute -top-32 left-1/4 -z-10 h-[550px] w-[650px] rounded-full bg-[#5D549C]/20 blur-[140px]"></div>
    <div class="pointer-events-none absolute top-1/3 -right-20 -z-10 h-[500px] w-[600px] rounded-full bg-[#7E3A63]/20 blur-[130px]"></div>
    <div class="pointer-events-none absolute bottom-0 left-10 -z-10 h-[350px] w-[450px] rounded-full bg-[#EB9998]/10 blur-[120px]"></div>

    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <div class="grid items-center gap-12 lg:grid-cols-12 lg:gap-8">
        
        {{-- Left Column: Headline & Action Buttons --}}
        <div class="lg:col-span-7 text-center lg:text-left">
          {{-- Mini Badge / Sparkle --}}
          <div class="inline-flex items-center gap-2 rounded-full border border-[#A373D3]/40 bg-[#5D549C]/30 px-3.5 py-1.5 text-xs font-black tracking-wide uppercase text-[#A373D3] shadow-md mb-6">
            <i class="ph-fill ph-sparkle text-sm text-[#EB9998]"></i>
            <span>Platform Nongki & Split Bill #1 Buat Circle Lu</span>
          </div>

          {{-- Discord-Style Punchy Headline --}}
          <h1 class="text-4xl sm:text-6xl xl:text-7xl font-black uppercase tracking-tight text-white leading-[1.05]">
            GROUP CHAT SERU,<br>
            <span class="text-[#EB9998]">PATUNGAN GAK PAKE DRAMA.</span>
          </h1>

          {{-- Subtitle --}}
          <p class="mt-6 text-sm sm:text-base lg:text-lg text-[#a19db0] leading-relaxed max-w-2xl mx-auto lg:mx-0">
            BagiRata tempat paling santuy buat seru-seruan bareng circle lu, nyatet jajan kafe, ngurus uang kas bareng, dan bagi tagihan otomatis tanpa rasa canggung atau drama <span class="text-white font-semibold">"entar gue transfer ya"</span> yang berujung amnesia.
          </p>

          {{-- Action Buttons --}}
          <div class="mt-8 flex flex-wrap items-center justify-center lg:justify-start gap-3 sm:gap-4">
            <a href="{{ route('register') }}" class="pushable inline-block">
              <span class="shadow"></span>
              <span class="edge"></span>
              <span class="front" style="padding: 14px 28px; font-size: 0.95rem; display: inline-flex; align-items: center; gap: 8px;">
                <i class="ph-bold ph-rocket-launch text-base"></i>
                <span>Gas, Mulai Sekarang — Gratis!</span>
              </span>
            </a>

            <a href="{{ route('calculator.index') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 px-6 py-3.5 text-xs sm:text-sm font-bold text-white transition hover:scale-[1.02] active:scale-[0.98]">
              <i class="ph-bold ph-calculator text-base text-[#EB9998]"></i>
              <span>Kalkulator Cepat (Sat-Set)</span>
            </a>
          </div>

          {{-- Mini Trust Indicators ala Discord --}}
          <div class="mt-10 flex flex-wrap items-center justify-center lg:justify-start gap-6 text-xs font-bold text-[#6F70A2]">
            <div class="flex items-center gap-2">
              <i class="ph-fill ph-check-circle text-base text-emerald-400"></i>
              <span class="text-gray-300">100% Gratis, Gak Ada Jebakan</span>
            </div>
            <div class="flex items-center gap-2">
              <i class="ph-fill ph-shield-check text-base text-[#A373D3]"></i>
              <span class="text-gray-300">Uang Kas Aman Anti Lenyap</span>
            </div>
            <div class="flex items-center gap-2">
              <i class="ph-fill ph-lightning text-base text-[#EB9998]"></i>
              <span class="text-gray-300">Pangkas Utang Sat-Set</span>
            </div>
          </div>
        </div>

        {{-- Right Column: Interactive Discord-Style Card Preview --}}
        <div class="lg:col-span-5 relative">
          {{-- Main Discord App Window Mockup --}}
          <div class="relative rounded-3xl border border-white/10 bg-[#181224] p-4 sm:p-5 shadow-2xl backdrop-blur-xl ring-1 ring-white/5">
            
            {{-- Window Top Bar --}}
            <div class="flex items-center justify-between border-b border-white/5 pb-3">
              <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-[#5D549C] flex items-center justify-center font-black text-xs text-white shadow-sm">
                  BK
                </div>
                <div>
                  <h4 class="text-xs font-black text-white flex items-center gap-1.5">
                    <span>Tongkrongan Kopi Senja</span>
                    <i class="ph-fill ph-seal-check text-xs text-[#A373D3]"></i>
                  </h4>
                  <p class="text-[10px] text-[#6F70A2]">#nongkrong-sore · 4 Anak Online</p>
                </div>
              </div>
              <span class="px-2 py-0.5 rounded-md bg-emerald-500/20 border border-emerald-500/30 text-[10px] font-bold text-emerald-400">
                Sesi Aktif 
              </span>
            </div>

            {{-- Chat & Receipt Stream --}}
            <div class="mt-4 space-y-3">
              {{-- Chat Bubble 1 --}}
              <div class="flex items-start gap-2.5">
                <div class="w-7 h-7 rounded-full bg-[#7E3A63] text-white flex items-center justify-center text-[10px] font-black shrink-0">
                  DM
                </div>
                <div class="flex-1 rounded-2xl bg-white/5 p-3 text-xs border border-white/5">
                  <div class="flex items-center justify-between mb-1">
                    <span class="font-bold text-[#EB9998]">Dimas</span>
                    <span class="text-[10px] text-gray-500">17:42</span>
                  </div>
                  <p class="text-[#a19db0]">
                    Woy gais, totalan kafe sore tadi udah gue input di bill ya! Buruan cek jatah masing-masing 
                  </p>
                </div>
              </div>

              {{-- Embedded Split Bill Card Widget --}}
              <div class="rounded-2xl border border-[#A373D3]/30 bg-[#110b17] p-3.5 shadow-lg">
                <div class="flex items-center justify-between border-b border-white/5 pb-2">
                  <div class="flex items-center gap-1.5">
                    <i class="ph-bold ph-receipt text-sm text-[#EB9998]"></i>
                    <span class="text-xs font-bold text-white">Bill: Kopi & Toast Senja</span>
                  </div>
                  <span class="text-xs font-black text-[#EB9998]">Rp 276.000</span>
                </div>

                {{-- Bill Breakdown Items --}}
                <div class="mt-2.5 space-y-1.5 text-[11px]">
                  <div class="flex items-center justify-between rounded-lg bg-white/5 px-2.5 py-1.5">
                    <div class="flex items-center gap-2">
                      <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                      <span class="font-semibold text-white">Rian (Latte + Croissant)</span>
                    </div>
                    <span class="font-bold text-emerald-400">Rp 74.750 (Lunas QRIS) ✓</span>
                  </div>
                  <div class="flex items-center justify-between rounded-lg bg-white/5 px-2.5 py-1.5">
                    <div class="flex items-center gap-2">
                      <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                      <span class="font-semibold text-white">Sarah (Americano)</span>
                    </div>
                    <span class="font-bold text-emerald-400">Rp 40.250 (Udah Transfer) ✓</span>
                  </div>
                  <div class="flex items-center justify-between rounded-lg bg-white/5 px-2.5 py-1.5">
                    <div class="flex items-center gap-2">
                      <span class="w-1.5 h-1.5 rounded-full bg-[#EB9998]"></span>
                      <span class="font-semibold text-white">Dimas (Pasta Creamy)</span>
                    </div>
                    <span class="font-bold text-[#EB9998]">Rp 161.000 (Nalangi Dulu)</span>
                  </div>
                </div>

                <div class="mt-2.5 flex items-center justify-between pt-2 border-t border-white/5 text-[10px] text-[#6F70A2]">
                  <span>Pajak resto 10% & serv 5% kehitung adil proporsional</span>
                  <span class="text-emerald-400 font-bold">2/3 Anak Udah Lunas</span>
                </div>
              </div>
            </div>

            {{-- Floating Badges --}}
            <div class="mt-3 flex items-center justify-between gap-2">
              <div class="flex-1 flex items-center gap-2 rounded-xl bg-[#5D549C]/20 border border-[#A373D3]/30 px-3 py-2 text-xs">
                <i class="ph-bold ph-vault text-base text-[#EB9998]"></i>
                <div>
                  <p class="text-[10px] text-[#6F70A2] font-bold uppercase">Uang Kas Circle</p>
                  <p class="text-xs font-black text-white">Rp 1.850.000 (Aman Bos)</p>
                </div>
              </div>
              <div class="flex-1 flex items-center gap-2 rounded-xl bg-[#7E3A63]/20 border border-[#EB9998]/30 px-3 py-2 text-xs">
                <i class="ph-bold ph-arrows-split text-base text-emerald-400"></i>
                <div>
                  <p class="text-[10px] text-[#6F70A2] font-bold uppercase">Debt Algoritma</p>
                  <p class="text-xs font-black text-white">Cukup 1x Transfer!</p>
                </div>
              </div>
            </div>

          </div>
        </div>

      </div>
    </div>
  </section>

  {{-- ========================================================
     2. ANIMATED MARQUEE TICKER TAPE (Chunky Banner ala Discord dengan Logo BagiRata)
     ======================================================== --}}
  <div class="relative w-full border-y-4 border-[#1c1537] bg-[#3b326b] py-10 sm:py-14 lg:py-16 overflow-hidden select-none shadow-[inset_0_4px_25px_rgba(0,0,0,0.35)]">
    <div class="animate-marquee flex items-center text-3xl sm:text-5xl lg:text-7xl font-black uppercase tracking-tight text-white whitespace-nowrap">
      {{-- Loop 1 --}}
      <span class="flex items-center">
        <span>NONGKI</span>
        <img src="{{ asset('image/logo.png') }}" alt="BagiRata" class="h-9 sm:h-14 lg:h-18 w-auto object-contain mx-6 sm:mx-10 lg:mx-14 inline-block shrink-0 drop-shadow-[0_4px_16px_rgba(0,0,0,0.4)]">
        <span>PLAY</span>
        <img src="{{ asset('image/logo.png') }}" alt="BagiRata" class="h-9 sm:h-14 lg:h-18 w-auto object-contain mx-6 sm:mx-10 lg:mx-14 inline-block shrink-0 drop-shadow-[0_4px_16px_rgba(0,0,0,0.4)]">
        <span>CHAT</span>
        <img src="{{ asset('image/logo.png') }}" alt="BagiRata" class="h-9 sm:h-14 lg:h-18 w-auto object-contain mx-6 sm:mx-10 lg:mx-14 inline-block shrink-0 drop-shadow-[0_4px_16px_rgba(0,0,0,0.4)]">
        <span>HANG OUT</span>
        <img src="{{ asset('image/logo.png') }}" alt="BagiRata" class="h-9 sm:h-14 lg:h-18 w-auto object-contain mx-6 sm:mx-10 lg:mx-14 inline-block shrink-0 drop-shadow-[0_4px_16px_rgba(0,0,0,0.4)]">
        <span>SPLIT BILL</span>
        <img src="{{ asset('image/logo.png') }}" alt="BagiRata" class="h-9 sm:h-14 lg:h-18 w-auto object-contain mx-6 sm:mx-10 lg:mx-14 inline-block shrink-0 drop-shadow-[0_4px_16px_rgba(0,0,0,0.4)]">
        <span>UANG KAS</span>
        <img src="{{ asset('image/logo.png') }}" alt="BagiRata" class="h-9 sm:h-14 lg:h-18 w-auto object-contain mx-6 sm:mx-10 lg:mx-14 inline-block shrink-0 drop-shadow-[0_4px_16px_rgba(0,0,0,0.4)]">
        <span>ANTI BONCOS</span>
        <img src="{{ asset('image/logo.png') }}" alt="BagiRata" class="h-9 sm:h-14 lg:h-18 w-auto object-contain mx-6 sm:mx-10 lg:mx-14 inline-block shrink-0 drop-shadow-[0_4px_16px_rgba(0,0,0,0.4)]">
      </span>
      {{-- Loop 2 (Continuous Loop) --}}
      <span class="flex items-center">
        <span>NONGKI</span>
        <img src="{{ asset('image/logo.png') }}" alt="BagiRata" class="h-9 sm:h-14 lg:h-18 w-auto object-contain mx-6 sm:mx-10 lg:mx-14 inline-block shrink-0 drop-shadow-[0_4px_16px_rgba(0,0,0,0.4)]">
        <span>PLAY</span>
        <img src="{{ asset('image/logo.png') }}" alt="BagiRata" class="h-9 sm:h-14 lg:h-18 w-auto object-contain mx-6 sm:mx-10 lg:mx-14 inline-block shrink-0 drop-shadow-[0_4px_16px_rgba(0,0,0,0.4)]">
        <span>CHAT</span>
        <img src="{{ asset('image/logo.png') }}" alt="BagiRata" class="h-9 sm:h-14 lg:h-18 w-auto object-contain mx-6 sm:mx-10 lg:mx-14 inline-block shrink-0 drop-shadow-[0_4px_16px_rgba(0,0,0,0.4)]">
        <span>HANG OUT</span>
        <img src="{{ asset('image/logo.png') }}" alt="BagiRata" class="h-9 sm:h-14 lg:h-18 w-auto object-contain mx-6 sm:mx-10 lg:mx-14 inline-block shrink-0 drop-shadow-[0_4px_16px_rgba(0,0,0,0.4)]">
        <span>SPLIT BILL</span>
        <img src="{{ asset('image/logo.png') }}" alt="BagiRata" class="h-9 sm:h-14 lg:h-18 w-auto object-contain mx-6 sm:mx-10 lg:mx-14 inline-block shrink-0 drop-shadow-[0_4px_16px_rgba(0,0,0,0.4)]">
        <span>UANG KAS</span>
        <img src="{{ asset('image/logo.png') }}" alt="BagiRata" class="h-9 sm:h-14 lg:h-18 w-auto object-contain mx-6 sm:mx-10 lg:mx-14 inline-block shrink-0 drop-shadow-[0_4px_16px_rgba(0,0,0,0.4)]">
        <span>ANTI BONCOS</span>
        <img src="{{ asset('image/logo.png') }}" alt="BagiRata" class="h-9 sm:h-14 lg:h-18 w-auto object-contain mx-6 sm:mx-10 lg:mx-14 inline-block shrink-0 drop-shadow-[0_4px_16px_rgba(0,0,0,0.4)]">
      </span>
    </div>
  </div>

  {{-- ========================================================
     3. SECTION 1: Obrolan & Bill Jadi Satu (Visual Left, Text Right)
     ======================================================== --}}
  <section id="fitur" class="py-16 sm:py-24 border-b border-white/5 relative">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <div class="grid items-center gap-12 lg:grid-cols-12 lg:gap-16">
        
        {{-- Visual Card (Left) --}}
        <div class="lg:col-span-7">
          <div class="rounded-3xl border border-white/10 bg-[#181224] p-5 sm:p-7 shadow-2xl relative">
            <div class="flex items-center justify-between border-b border-white/5 pb-4 mb-4">
              <div class="flex items-center gap-2">
                <span class="text-lg font-black text-white"># nongki-kulineran</span>
                <span class="text-xs px-2 py-0.5 rounded-full bg-white/5 text-[#6F70A2] font-semibold">Sub-Channel</span>
              </div>
              <span class="text-xs font-bold text-[#EB9998] flex items-center gap-1">
                <i class="ph-bold ph-check text-xs"></i> Auto Sinkron Bill
              </span>
            </div>

            {{-- Discord Channel Simulated Layout --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
              {{-- Mini Channels List --}}
              <div class="rounded-2xl bg-[#110b17] p-3 border border-white/5 space-y-1.5 text-xs">
                <span class="text-[10px] font-black uppercase text-[#6F70A2] tracking-wider block px-1 mb-2">Channels</span>
                <div class="px-2.5 py-1.5 rounded-lg bg-white/10 text-white font-bold flex items-center justify-between">
                  <span># nongkrong</span>
                  <span class="w-2 h-2 rounded-full bg-[#EB9998]"></span>
                </div>
                <div class="px-2.5 py-1.5 rounded-lg text-[#a19db0] hover:text-white flex items-center justify-between">
                  <span># trip-pantai</span>
                  <span class="text-[10px] text-gray-500">2 bill</span>
                </div>
                <div class="px-2.5 py-1.5 rounded-lg text-[#a19db0] hover:text-white flex items-center justify-between">
                  <span># uang-kas</span>
                  <span class="text-[10px] text-emerald-400">Aktif</span>
                </div>
              </div>

              {{-- Chat Feed with Embedded Bill --}}
              <div class="sm:col-span-2 rounded-2xl bg-[#110b17] p-3 border border-white/5 space-y-2.5">
                <div class="flex items-start gap-2 text-xs">
                  <div class="w-6 h-6 rounded-full bg-[#5D549C] text-white flex items-center justify-center text-[10px] font-bold shrink-0">B</div>
                  <div>
                    <p class="font-bold text-white text-[11px]">Budi <span class="text-[9px] text-[#6F70A2] font-normal">19:15</span></p>
                    <p class="text-[#a19db0] text-xs">Nasi goreng gila tadi udah gue nalangin dulu ya bro!</p>
                  </div>
                </div>

                <div class="rounded-xl border border-white/10 bg-black/40 p-2.5 text-xs">
                  <div class="flex justify-between items-center font-bold text-white mb-1">
                    <span> Nasi Goreng Gila (3 Porsi)</span>
                    <span class="text-[#EB9998]">Rp 96.000</span>
                  </div>
                  <p class="text-[11px] text-[#6F70A2]">Bagi rata: Budi, Rian, Sarah (Rp 32.000 / anak)</p>
                  <div class="mt-2 flex items-center justify-between pt-1.5 border-t border-white/5 text-[10px]">
                    <span class="text-emerald-400 font-semibold">Udah masuk buku utang</span>
                    <span class="px-2 py-0.5 rounded bg-[#5D549C] text-white font-bold">Bayar via QRIS</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        {{-- Text Content (Right) --}}
        <div class="lg:col-span-5 text-center lg:text-left">
          <span class="inline-block rounded-full bg-[#5D549C]/30 border border-[#A373D3]/30 px-3.5 py-1 text-xs font-bold uppercase tracking-wider text-[#A373D3] mb-3">
            Chat + Bill Sat-Set
          </span>
          <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black uppercase tracking-tight text-white leading-tight">
            CHAT DAN PATUNGAN<br>
            <span class="text-[#EB9998]">LANGSUNG DI SATU TEMPAT.</span>
          </h2>
          <p class="mt-4 text-sm sm:text-base text-[#a19db0] leading-relaxed">
            Gak usah lagi ribet lempar-lemparan foto struk lecek di grup WA terus pusing buka kalkulator di HP. Di BagiRata, lu bisa ngobrol santai di channel nongkrong sambil nyelipin bill patungan. Semua orang tau persis siapa bayar apa, gak pake tekor!
          </p>
          <ul class="mt-6 space-y-3 text-xs sm:text-sm font-semibold text-gray-300">
            <li class="flex items-center gap-3">
              <i class="ph-bold ph-chat-circle-dots text-base text-[#EB9998]"></i>
              <span>Bikin channel sesuka circle: geng ngopi, anak kantor, atau kawan kosan</span>
            </li>
            <li class="flex items-center gap-3">
              <i class="ph-bold ph-receipt text-base text-[#A373D3]"></i>
              <span>Bill otomatis nempel di chat tanpa perlu kirim screenshot struk kusut</span>
            </li>
            <li class="flex items-center gap-3">
              <i class="ph-bold ph-bell-simple-ringing text-base text-emerald-400"></i>
              <span>Notifikasi tagihan langsung meluncur ke akun temen yang bersangkutan</span>
            </li>
          </ul>
        </div>

      </div>
    </div>
  </section>

  {{-- ========================================================
     4. SECTION 2: Kas Grup Bersama (Text Left, Visual Right)
     ======================================================== --}}
  <section id="kas-grup" class="py-16 sm:py-24 border-b border-white/5 relative">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <div class="grid items-center gap-12 lg:grid-cols-12 lg:gap-16">
        
        {{-- Text Content (Left) --}}
        <div class="lg:col-span-5 text-center lg:text-left order-2 lg:order-1">
          <span class="inline-block rounded-full bg-[#7E3A63]/30 border border-[#EB9998]/30 px-3.5 py-1 text-xs font-bold uppercase tracking-wider text-[#EB9998] mb-3">
            Uang Kas Bersama
          </span>
          <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black uppercase tracking-tight text-white leading-tight">
            KAS CIRCLE TRANSPARAN,<br>
            <span class="text-[#EB9998]">ANTI DUIT MISTERIUS HILANG.</span>
          </h2>
          <p class="mt-4 text-sm sm:text-base text-[#a19db0] leading-relaxed">
            Punya uang kas rutin buat geng futsal, circle liburan, atau iuran kosan? Catat kas masuk sama kas keluar secara real-time. Semua anak tongkrongan bisa liat saldo, bukti struk, dan mutasi kapan aja tanpa curiga bendahara kabur!
          </p>
          <div class="mt-6 flex flex-wrap gap-3">
            <div class="rounded-xl border border-white/5 bg-[#181224] px-4 py-2.5 text-xs">
              <p class="text-[10px] text-[#6F70A2] font-bold uppercase">Kas Masuk</p>
              <p class="font-black text-emerald-400 text-sm">Iuran & Saweran</p>
            </div>
            <div class="rounded-xl border border-white/5 bg-[#181224] px-4 py-2.5 text-xs">
              <p class="text-[10px] text-[#6F70A2] font-bold uppercase">Kas Keluar</p>
              <p class="font-black text-[#EB9998] text-sm">Jajan Bareng</p>
            </div>
            <div class="rounded-xl border border-white/5 bg-[#181224] px-4 py-2.5 text-xs">
              <p class="text-[10px] text-[#6F70A2] font-bold uppercase">Audit Saldo</p>
              <p class="font-black text-[#A373D3] text-sm">Pantau 24/7</p>
            </div>
          </div>
        </div>

        {{-- Visual Card (Right) --}}
        <div class="lg:col-span-7 order-1 lg:order-2">
          <div class="rounded-3xl border border-white/10 bg-[#181224] p-5 sm:p-7 shadow-2xl relative">
            
            {{-- Top Treasury Balance Display --}}
            <div class="rounded-2xl bg-[#110b17] border border-white/5 p-4 sm:p-5 mb-4">
              <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-[#6F70A2]">Total Saldo Kas Circle</span>
                <span class="px-2 py-0.5 rounded-full bg-emerald-500/20 text-[10px] font-bold text-emerald-400">Aman & Terverifikasi</span>
              </div>
              <div class="mt-2 flex items-baseline gap-2">
                <span class="text-3xl sm:text-4xl font-black text-white">Rp 2.450.000</span>
                <span class="text-xs text-emerald-400 font-bold">+Rp 600.000 bulan ini</span>
              </div>
              <div class="mt-4 flex gap-2">
                <button type="button" class="flex-1 py-2 rounded-xl bg-[#5D549C] text-white text-xs font-bold shadow-sm">
                  + Kas Masuk (Iuran)
                </button>
                <button type="button" class="flex-1 py-2 rounded-xl bg-white/5 hover:bg-white/10 text-white text-xs font-bold border border-white/5">
                  - Kas Keluar (Jajan)
                </button>
              </div>
            </div>

            {{-- Mutation List --}}
            <div class="space-y-2">
              <span class="text-[11px] font-black uppercase tracking-wider text-[#6F70A2] block px-1">Aktivitas Terakhir</span>
              
              <div class="flex items-center justify-between rounded-xl bg-white/5 px-3 py-2.5 text-xs">
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold text-sm">
                    ↓
                  </div>
                  <div>
                    <p class="font-bold text-white">Iuran Bulanan (8/8 Anak Lunas)</p>
                    <p class="text-[10px] text-[#6F70A2]">Diinput sama Bendahara (Rian)</p>
                  </div>
                </div>
                <span class="font-bold text-emerald-400">+Rp 400.000</span>
              </div>

              <div class="flex items-center justify-between rounded-xl bg-white/5 px-3 py-2.5 text-xs">
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 rounded-lg bg-[#EB9998]/20 text-[#EB9998] flex items-center justify-center font-bold text-sm">
                    ↑
                  </div>
                  <div>
                    <p class="font-bold text-white">Sewa Lapangan Badminton 2 Jam</p>
                    <p class="text-[10px] text-[#6F70A2]">Ada Bukti Transfer Struk</p>
                  </div>
                </div>
                <span class="font-bold text-[#EB9998]">-Rp 160.000</span>
              </div>
            </div>

          </div>
        </div>

      </div>
    </div>
  </section>

  {{-- ========================================================
     5. SECTION 3: Hitung Pajak & Service (Visual Left, Text Right)
     ======================================================== --}}
  <section id="itemized" class="py-16 sm:py-24 border-b border-white/5 relative">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <div class="grid items-center gap-12 lg:grid-cols-12 lg:gap-16">
        
        {{-- Visual Card (Left) --}}
        <div class="lg:col-span-7">
          <div class="rounded-3xl border border-white/10 bg-[#181224] p-5 sm:p-7 shadow-2xl">
            <div class="flex items-center justify-between border-b border-white/5 pb-3 mb-4">
              <span class="text-xs font-black uppercase tracking-wider text-[#A373D3]">Simulasi Split Bill Proporsional</span>
              <span class="text-xs text-[#a19db0]">Pajak Resto: 10% · Service: 5%</span>
            </div>

            <div class="space-y-2.5 text-xs">
              {{-- Item 1 --}}
              <div class="rounded-2xl border border-white/5 bg-[#110b17] p-3 flex items-center justify-between">
                <div>
                  <p class="font-bold text-white"> Sirloin Steak (Dimas)</p>
                  <p class="text-[11px] text-[#6F70A2]">Harga Menu: Rp 160.000 + Pajak (Rp 24.000)</p>
                </div>
                <span class="font-black text-white text-sm">Rp 184.000</span>
              </div>

              {{-- Item 2 --}}
              <div class="rounded-2xl border border-white/5 bg-[#110b17] p-3 flex items-center justify-between">
                <div>
                  <p class="font-bold text-white"> Es Teh Manis (Rian)</p>
                  <p class="text-[11px] text-[#6F70A2]">Harga Menu: Rp 15.000 + Pajak (Rp 2.250)</p>
                </div>
                <span class="font-black text-white text-sm">Rp 17.250</span>
              </div>

              {{-- Item 3 Shared --}}
              <div class="rounded-2xl border border-white/5 bg-[#110b17] p-3 flex items-center justify-between">
                <div>
                  <p class="font-bold text-white"> Platter Camilan (Bagi Berdua)</p>
                  <p class="text-[11px] text-[#6F70A2]">Rp 40.000 (Rp 20.000 / anak + Pajak Rp 3.000)</p>
                </div>
                <span class="font-black text-white text-sm">Rp 23.000 /anak</span>
              </div>
            </div>

            <div class="mt-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 p-3 text-xs text-emerald-300 flex items-center gap-2">
              <i class="ph-fill ph-check-circle text-base shrink-0"></i>
              <span>Yang cuma pesen es teh gak bakal nombokin pajak yang pesen steak. Adil parah!</span>
            </div>
          </div>
        </div>

        {{-- Text Content (Right) --}}
        <div class="lg:col-span-5 text-center lg:text-left">
          <span class="inline-block rounded-full bg-[#5D549C]/30 border border-[#A373D3]/30 px-3.5 py-1 text-xs font-bold uppercase tracking-wider text-[#A373D3] mb-3">
            Itemized Split
          </span>
          <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black uppercase tracking-tight text-white leading-tight">
            YANG MAKAN BANYAK BAYAR LEBIH,<br>
            <span class="text-[#EB9998]">CUMA NUMPANG ES TEH AMAN.</span>
          </h2>
          <p class="mt-4 text-sm sm:text-base text-[#a19db0] leading-relaxed">
            Ada temen yang kalap pesen wagyu mahal, tapi ada yang cuma pesen es teh tawar? Fitur itemized split BagiRata ngebagi tagihan per menu pesanan secara presisi. Pajak resto PB1 10% dan service charge dihitung proporsional sesuai nominal porsi masing-masing, jadi kagak ada yang ngebatin nombokin!
          </p>
          <div class="mt-6 space-y-2.5 text-xs sm:text-sm font-semibold text-gray-300">
            <div class="flex items-center gap-3">
              <i class="ph-bold ph-divide text-base text-[#EB9998]"></i>
              <span>Bisa bagi rata murni (equal) atau sesuai pesanan masing-masing</span>
            </div>
            <div class="flex items-center gap-3">
              <i class="ph-bold ph-percent text-base text-[#A373D3]"></i>
              <span>Otomatis potong diskon promo, voucher kafe, sama tip pelayan</span>
            </div>
            <div class="flex items-center gap-3">
              <i class="ph-bold ph-scales text-base text-emerald-400"></i>
              <span>Kagak ada lagi sisa pembulatan sen misterius yang bikin tekor</span>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>

  {{-- ========================================================
     6. SECTION 4: Debt Simplification (Text Left, Visual Right)
     ======================================================== --}}
  <section id="debt-simplification" class="py-16 sm:py-24 border-b border-white/5 relative">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <div class="grid items-center gap-12 lg:grid-cols-12 lg:gap-16">
        
        {{-- Text Content (Left) --}}
        <div class="lg:col-span-5 text-center lg:text-left order-2 lg:order-1">
          <span class="inline-block rounded-full bg-[#7E3A63]/30 border border-[#EB9998]/30 px-3.5 py-1 text-xs font-bold uppercase tracking-wider text-[#EB9998] mb-3">
            Algoritma Pintar
          </span>
          <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black uppercase tracking-tight text-white leading-tight">
            UTANG SILANG PANGKAS TOTAL,<br>
            <span class="text-[#EB9998]">GAK BOROS BIAYA ADMIN!</span>
          </h2>
          <p class="mt-4 text-sm sm:text-base text-[#a19db0] leading-relaxed">
            Si A utang ke B 50 ribu, si B utang ke C 50 ribu. Ngapain repot transfer 2 kali dan tekor biaya admin antar-bank? Algoritma BagiRata otomatis ngehitung selisih bersih circle lu. Jalur transfer berbelit dipangkas abis, sekali transfer langsung lunas semua!
          </p>
          <div class="mt-6 flex items-center gap-4 text-xs font-bold text-[#A373D3]">
            <div class="flex items-center gap-1.5">
              <i class="ph-bold ph-check text-sm text-emerald-400"></i>
              <span>Nol transfer berputar</span>
            </div>
            <div class="flex items-center gap-1.5">
              <i class="ph-bold ph-check text-sm text-emerald-400"></i>
              <span>Auto lunas seketika</span>
            </div>
          </div>
        </div>

        {{-- Visual Card (Right) --}}
        <div class="lg:col-span-7 order-1 lg:order-2">
          <div class="rounded-3xl border border-white/10 bg-[#181224] p-5 sm:p-7 shadow-2xl">
            <span class="text-xs font-black uppercase tracking-wider text-[#6F70A2] block mb-4">
              Perbandingan Jalur Transaksi Circle
            </span>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              {{-- Sebelum --}}
              <div class="rounded-2xl border border-rose-500/20 bg-rose-950/20 p-4">
                <span class="px-2 py-0.5 rounded bg-rose-500/20 text-rose-400 text-[10px] font-bold uppercase">
                  Cara Lama (Ribet & Capek)
                </span>
                <div class="mt-3 space-y-2 text-xs text-rose-200">
                  <div class="flex items-center justify-between">
                    <span>Andi ➔ Budi</span>
                    <span class="font-bold">Rp 50.000</span>
                  </div>
                  <div class="flex items-center justify-between">
                    <span>Budi ➔ Citra</span>
                    <span class="font-bold">Rp 50.000</span>
                  </div>
                  <div class="flex items-center justify-between">
                    <span>Citra ➔ Andi</span>
                    <span class="font-bold">Rp 20.000</span>
                  </div>
                </div>
                <div class="mt-3 pt-2 border-t border-rose-500/20 text-[11px] text-rose-300 font-semibold">
                  Total: 3x Transfer & Tekor Biaya Admin!
                </div>
              </div>

              {{-- Sesudah BagiRata --}}
              <div class="rounded-2xl border border-emerald-500/30 bg-emerald-950/20 p-4">
                <span class="px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-400 text-[10px] font-bold uppercase">
                  Pakai BagiRata (Sat-Set)
                </span>
                <div class="mt-3 space-y-2 text-xs text-emerald-200">
                  <div class="flex items-center justify-between font-bold text-white bg-black/30 p-2 rounded-lg border border-emerald-500/20">
                    <span>Andi ➔ Citra</span>
                    <span class="text-emerald-400">Rp 30.000</span>
                  </div>
                  <p class="text-[11px] text-emerald-300/80 pt-1">
                    Budi otomatis lunas tanpa keluar duit sepeser pun!
                  </p>
                </div>
                <div class="mt-3 pt-2 border-t border-emerald-500/20 text-[11px] text-emerald-300 font-bold">
                  Total: Cukup 1x Transfer Saja Beres! 
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>

  {{-- ========================================================
     7. GRID FITUR LAINNYA: 4 Kartu Unggulan
     ======================================================== --}}
  <section class="py-16 sm:py-24 border-b border-white/5 bg-black/20">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <div class="text-center max-w-2xl mx-auto mb-14">
        <span class="inline-block rounded-full bg-[#5D549C]/30 border border-[#A373D3]/30 px-3.5 py-1 text-xs font-bold uppercase tracking-wider text-[#A373D3] mb-3">
          Fitur Lengkap
        </span>
        <h2 class="text-3xl sm:text-4xl font-black uppercase tracking-tight text-white">
          Semua Kebutuhan Tongkrongan Ada di Sini
        </h2>
        <p class="mt-3 text-xs sm:text-sm text-[#a19db0]">
          Dibuat spesifik buat gaya nongkrong anak muda Indonesia yang aktif, seru, dan anti drama uang.
        </p>
      </div>

      <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
        {{-- Card 1 --}}
        <div class="rounded-3xl border border-white/10 bg-[#181224] p-6 hover:border-[#A373D3]/40 transition group">
          <div class="w-12 h-12 rounded-2xl bg-[#5D549C] text-white flex items-center justify-center text-xl mb-4 group-hover:scale-105 transition">
            <i class="ph-bold ph-calculator"></i>
          </div>
          <h3 class="text-base font-black text-white">Kalkulator Sat-Set</h3>
          <p class="mt-2 text-xs text-[#a19db0] leading-relaxed">
            Lagi buru-buru di kasir? Buka kalkulator cepat tanpa perlu register akun. Cemplungin total & jumlah anak, kelar dalam 3 detik.
          </p>
        </div>

        {{-- Card 2 --}}
        <div class="rounded-3xl border border-white/10 bg-[#181224] p-6 hover:border-[#EB9998]/40 transition group">
          <div class="w-12 h-12 rounded-2xl bg-[#7E3A63] text-white flex items-center justify-center text-xl mb-4 group-hover:scale-105 transition">
            <i class="ph-bold ph-qr-code"></i>
          </div>
          <h3 class="text-base font-black text-white">Scan & Bayar QRIS</h3>
          <p class="mt-2 text-xs text-[#a19db0] leading-relaxed">
            Pajang QRIS lu biar temen langsung scan lewat m-banking atau e-wallet tanpa nanya nomor rekening berkali-kali.
          </p>
        </div>

        {{-- Card 3 --}}
        <div class="rounded-3xl border border-white/10 bg-[#181224] p-6 hover:border-emerald-500/40 transition group">
          <div class="w-12 h-12 rounded-2xl bg-emerald-600 text-white flex items-center justify-center text-xl mb-4 group-hover:scale-105 transition">
            <i class="ph-bold ph-chart-pie-slice"></i>
          </div>
          <h3 class="text-base font-black text-white">Budgeting Anti Boncos</h3>
          <p class="mt-2 text-xs text-[#a19db0] leading-relaxed">
            Pasang batas budget nongki bulanan lu. Ada alarm pengingat pas jatah jajan udah mulai tipis biar gak kanker (kantong kering).
          </p>
        </div>

        {{-- Card 4 --}}
        <div class="rounded-3xl border border-white/10 bg-[#181224] p-6 hover:border-amber-500/40 transition group">
          <div class="w-12 h-12 rounded-2xl bg-amber-600 text-white flex items-center justify-center text-xl mb-4 group-hover:scale-105 transition">
            <i class="ph-bold ph-users-three"></i>
          </div>
          <h3 class="text-base font-black text-white">Circle & Role Grup</h3>
          <p class="mt-2 text-xs text-[#a19db0] leading-relaxed">
            Bikin circle privat bareng geng lu. Tunjuk siapa yang jadi bendahara kas, admin channel, dan sebar link invite biar auto gabung.
          </p>
        </div>
      </div>
    </div>
  </section>

  {{-- ========================================================
     8. FAQ SECTION: Pertanyaan yang Sering Diajukan
     ======================================================== --}}
  <section id="faq" class="py-16 sm:py-24 border-b border-white/5">
    <div class="mx-auto max-w-4xl px-4 sm:px-6">
      <div class="text-center mb-12">
        <span class="inline-block rounded-full bg-[#5D549C]/30 border border-[#A373D3]/30 px-3.5 py-1 text-xs font-bold uppercase tracking-wider text-[#A373D3] mb-3">
          Tanya Jawab Santuy
        </span>
        <h2 class="text-3xl sm:text-4xl font-black uppercase tracking-tight text-white">
          Pertanyaan yang Sering Muncul
        </h2>
        <p class="mt-2 text-xs sm:text-sm text-[#a19db0]">
          Kepo seputar cara kerja BagiRata? Cek jawabannya di bawah ini!
        </p>
      </div>

      <div class="space-y-3.5">
        <div class="rounded-2xl border border-white/10 bg-[#181224] p-5">
          <h4 class="text-sm sm:text-base font-bold text-white flex items-center gap-2">
            <i class="ph-bold ph-question text-[#EB9998]"></i>
            <span>Beneran 100% gratis nih, gak ada jebakan betmen?</span>
          </h4>
          <p class="mt-2 text-xs sm:text-sm text-[#a19db0] leading-relaxed pl-6">
            Beneran dong, gratis selamanya! Lu bisa pake kalkulator kilat, bikin grup tongkrongan, sesi nongki, sampe pembukuan kas bareng tanpa dipungut biaya langganan sepeser pun.
          </p>
        </div>

        <div class="rounded-2xl border border-white/10 bg-[#181224] p-5">
          <h4 class="text-sm sm:text-base font-bold text-white flex items-center gap-2">
            <i class="ph-bold ph-question text-[#EB9998]"></i>
            <span>Temen tongkrongan gue pada mager bikin akun, gimana dong?</span>
          </h4>
          <p class="mt-2 text-xs sm:text-sm text-[#a19db0] leading-relaxed pl-6">
            Santuy abis! Kalo cuma mau hitung kilat gak perlu login sama sekali. Kalo buat grup permanen, tinggal lempar 1 link invite, temen lu klik langsung nimbrung gak pake ribet.
          </p>
        </div>

        <div class="rounded-2xl border border-white/10 bg-[#181224] p-5">
          <h4 class="text-sm sm:text-base font-bold text-white flex items-center gap-2">
            <i class="ph-bold ph-question text-[#EB9998]"></i>
            <span>Gimana cara ngitung pajak resto sama service charge biar adil?</span>
          </h4>
          <p class="mt-2 text-xs sm:text-sm text-[#a19db0] leading-relaxed pl-6">
            BagiRata pake rumus proporsional. Pajak PB1 (biasanya 10%) sama service fee dihitung dari persentase harga makanan masing-masing anak, bukan dibagi rata buta. Adil parah!
          </p>
        </div>

        <div class="rounded-2xl border border-white/10 bg-[#181224] p-5">
          <h4 class="text-sm sm:text-base font-bold text-white flex items-center gap-2">
            <i class="ph-bold ph-question text-[#EB9998]"></i>
            <span>Fitur Kas Grup itu gunanya buat apaan sih?</span>
          </h4>
          <p class="mt-2 text-xs sm:text-sm text-[#a19db0] leading-relaxed pl-6">
            Ibarat dompet digital transparan bareng geng lu. Bendahara tinggal catet uang kas masuk atau jajan bareng, semua anak tongkrongan bisa liat mutasinya secara transparan 24/7.
          </p>
        </div>
      </div>
    </div>
  </section>

  {{-- ========================================================
     9. BIG BOTTOM CTA (Discord Signature "YOU CAN'T SCROLL ANYMORE")
     ======================================================== --}}
  <section class="py-20 sm:py-28 relative overflow-hidden bg-transparent">
    {{-- Ambient glow behind CTA --}}
    <div class="pointer-events-none absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-[700px] h-[350px] bg-[#5D549C]/25 blur-[150px] rounded-full"></div>

    <div class="mx-auto max-w-4xl px-4 sm:px-6 text-center relative z-10">
      <span class="text-xs font-black uppercase tracking-widest text-[#EB9998] block mb-3">
        Waktunya Nongki Bebas Drama
      </span>

      <h2 class="text-3xl sm:text-5xl lg:text-6xl font-black uppercase tracking-tight text-white leading-tight">
        UDAH SAMPAI BAWAH?<br>
        <span class="text-[#EB9998]">YUK GAS NONGKI SEKARANG!</span>
      </h2>

      <p class="mt-4 text-xs sm:text-base text-[#a19db0] max-w-xl mx-auto leading-relaxed">
        Ribuan anak nongki udah pada pindah ke BagiRata biar tongkrongan makin asik tanpa beban drama uang. Buruan join, 100% gratis selamanya!
      </p>

      <div class="mt-8 flex flex-wrap items-center justify-center gap-4">
        <a href="{{ route('register') }}" class="pushable inline-block">
          <span class="shadow"></span>
          <span class="edge"></span>
          <span class="front" style="padding: 16px 36px; font-size: 1rem; display: inline-flex; align-items: center; gap: 8px;">
            <i class="ph-bold ph-user-plus text-lg"></i>
            <span>Gas Daftar — Gratis!</span>
          </span>
        </a>

        <a href="{{ route('calculator.index') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 px-6 py-4 text-xs sm:text-sm font-bold text-white transition hover:scale-[1.02] active:scale-[0.98]">
          <i class="ph-bold ph-calculator text-base text-[#EB9998]"></i>
          <span>Coba Kalkulator Cepat</span>
        </a>
      </div>
    </div>
  </section>

  {{-- ========================================================
     10. DETAILED FOOTER (Discord Homepage Style)
     ======================================================== --}}
  <footer class="border-t border-white/10 bg-[#09060f] pt-14 pb-8 relative overflow-hidden">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      
      <div class="grid grid-cols-2 md:grid-cols-5 gap-8 pb-12 border-b border-white/10">
        {{-- Column 1: Brand & Language --}}
        <div class="col-span-2">
          <div class="flex items-center gap-2.5 mb-3">
            <img src="{{ asset('image/logo.png') }}" alt="BagiRata" class="w-8 h-8 object-contain">
            <span class="text-lg font-black text-white">BagiRata</span>
          </div>
          <p class="text-xs text-[#a19db0] max-w-sm leading-relaxed mb-4">
            Platform nongki, split bill, dan kas circle paling asik di Indonesia. Nongkrong makin seru tanpa ada lagi drama uang antar kawan tongkrongan.
          </p>
          <div class="inline-flex items-center gap-2 rounded-lg bg-white/5 border border-white/10 px-3 py-1.5 text-xs text-gray-300 font-semibold">
            <span>🇮🇩</span>
            <span>Bahasa Indonesia (Gaul Santuy)</span>
          </div>
        </div>

        {{-- Column 2: Produk --}}
        <div class="space-y-2.5 text-xs">
          <p class="font-black uppercase tracking-wider text-white text-xs mb-3">Fitur Andalan</p>
          <p><a href="{{ route('calculator.index') }}" class="text-[#a19db0] hover:text-white transition">Kalkulator Cepat</a></p>
          <p><a href="{{ route('nongkrong.index') }}" class="text-[#a19db0] hover:text-white transition">Sesi Nongki</a></p>
          <p><a href="{{ route('groups.index') }}" class="text-[#a19db0] hover:text-white transition">Kas Circle (Treasury)</a></p>
          <p><a href="{{ route('debts.overview') }}" class="text-[#a19db0] hover:text-white transition">Buku Utang</a></p>
          <p><a href="{{ route('budgets.index') }}" class="text-[#a19db0] hover:text-white transition">Budgeting Anti Boncos</a></p>
        </div>

        {{-- Column 3: Fitur --}}
        <div class="space-y-2.5 text-xs">
          <p class="font-black uppercase tracking-wider text-white text-xs mb-3">Fitur Cerdas</p>
          <p><a href="#itemized" class="text-[#a19db0] hover:text-white transition">Itemized Split</a></p>
          <p><a href="#itemized" class="text-[#a19db0] hover:text-white transition">Hitung Pajak PB1 & Servis</a></p>
          <p><a href="#debt-simplification" class="text-[#a19db0] hover:text-white transition">Pangkas Utang Cerdas</a></p>
          <p><a href="#fitur" class="text-[#a19db0] hover:text-white transition">Scan QRIS Dinamis</a></p>
          <p><a href="#fitur" class="text-[#a19db0] hover:text-white transition">Channel Tongkrongan</a></p>
        </div>

        {{-- Column 4: Bantuan & Legal --}}
        <div class="space-y-2.5 text-xs">
          <p class="font-black uppercase tracking-wider text-white text-xs mb-3">Panduan</p>
          <p><a href="#faq" class="text-[#a19db0] hover:text-white transition">Pusat Bantuan / FAQ</a></p>
          <p><a href="#cara-kerja" class="text-[#a19db0] hover:text-white transition">Cara Kerja</a></p>
          <p><a href="{{ route('login') }}" class="text-[#a19db0] hover:text-white transition">Masuk Akun</a></p>
          <p><a href="{{ route('register') }}" class="text-[#a19db0] hover:text-white transition">Gas Bikin Akun</a></p>
        </div>
      </div>

      {{-- Bottom Watermark Wordmark (Discord Signature Huge Wordmark) --}}
      <div class="pt-8 flex flex-col items-center justify-center relative">
        <div class="w-full text-center overflow-hidden py-4 select-none pointer-events-none">
          <span class="text-6xl sm:text-8xl md:text-9xl lg:text-[140px] font-black tracking-tighter uppercase text-white/[0.04] leading-none block">
            BAGIRATA
          </span>
        </div>
        <div class="flex flex-col sm:flex-row items-center justify-between w-full text-xs text-[#6F70A2] pt-4">
          <p>© 2026 BagiRata. Dibuat buat circle nongkrong se-Indonesia.</p>
          <p class="mt-2 sm:mt-0">Semua hitungan transparan, zero drama.</p>
        </div>
      </div>

    </div>
  </footer>

</div>
@endsection
