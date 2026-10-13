<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'BagiRata') · Solusi Patungan & Split Bill Tanpa Drama</title>
    <link rel="icon" type="image/png" href="{{ asset('image/logo.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Script anti-flash sinkron sebelum CSS/body di-render --}}
    <script>
        (function () {
            try {
                const stored = localStorage.getItem('bagirata-theme');
                const dark = stored === 'dark' || (!stored && window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches);
                if (dark) {
                    document.documentElement.classList.add('dark');
                    document.documentElement.style.colorScheme = 'dark';
                } else {
                    document.documentElement.classList.remove('dark');
                    document.documentElement.style.colorScheme = 'light';
                }
            } catch (e) {}
        })();
    </script>
</head>
<body class="flex min-h-screen flex-col bg-[#f8fafc] text-slate-800 antialiased selection:bg-purple-100 selection:text-purple-900">
    @php
        $active = request()->route()?->getName() ?? '';
        $isAuthPage = request()->routeIs('login', 'register');
        $isLanding = request()->routeIs('landing');
        $navItems = [
            ['route' => 'dashboard', 'label' => 'Dashboard', 'match' => 'dashboard'],
            ['route' => 'groups.index', 'label' => 'Groups', 'match' => 'groups'],
            ['route' => 'nongkrong.index', 'label' => 'Nongkrong', 'match' => 'nongkrong'],
            ['route' => 'calculator.index', 'label' => 'Kalkulator', 'match' => 'calculator'],
            ['route' => 'budgets.index', 'label' => 'Budgeting', 'match' => 'budgets'],
            ['route' => 'debts.overview', 'label' => 'Utang-Piutang', 'match' => 'debts'],
        ];
    @endphp

    {{-- NAVBAR: Hanya tampil jika BUKAN di halaman login atau register --}}
    @unless ($isAuthPage)
        <header x-data="{ navOpen: false }" class="sticky top-0 z-40 border-b border-indigo-400/20 bg-gradient-to-r from-purple-700 via-indigo-600 to-blue-600 shadow-md shadow-indigo-950/10 backdrop-blur-md">
            <div class="mx-auto flex max-w-6xl items-center justify-between gap-3 px-4 py-3 sm:px-6">
                {{-- Logo & Brand --}}
                <a href="{{ auth()->check() ? route('dashboard') : url('/') }}" class="group flex items-center gap-3 transition-transform duration-200 hover:scale-[1.02]">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/95 p-1 shadow-sm ring-2 ring-white/30 transition-transform duration-300 group-hover:rotate-6">
                        <img src="{{ asset('image/logo.png') }}" alt="Logo BagiRata" class="h-8 w-auto object-contain">
                    </div>
                    <div class="flex flex-col">
                        <span class="flex items-center text-xl font-black tracking-tight text-white">
                            Bagi<span class="text-sky-300">Rata</span>
                        </span>
                        <span class="text-[10px] font-semibold uppercase tracking-wider text-indigo-100/80 -mt-1">
                            Patungan Tanpa Drama
                        </span>
                    </div>
                </a>

                {{-- Navigasi Desktop (Auth User) --}}
                @auth
                    <nav class="hidden items-center gap-1.5 lg:flex" aria-label="Utama">
                        @foreach ($navItems as $item)
                            @php $isCurrent = str_starts_with($active, $item['match']); @endphp
                            <a href="{{ route($item['route']) }}"
                                @class([
                                    'nav-link px-3.5 py-2 rounded-xl text-xs font-bold transition-all duration-150 flex items-center gap-1.5',
                                    'nav-link-active bg-white text-indigo-700 shadow-sm' => $isCurrent,
                                    'text-white/90 hover:text-white hover:bg-white/15' => !$isCurrent,
                                ])
                                @if ($isCurrent) aria-current="page" @endif>
                                <span>{{ $item['label'] }}</span>
                            </a>
                        @endforeach
                    </nav>
                @else
                    {{-- Navigasi Desktop (Guest Landing) --}}
                    <nav class="hidden items-center gap-6 md:flex text-sm font-semibold text-white/90" aria-label="Landing Links">
                        <a href="{{ url('/') }}" class="hover:text-white transition-colors">Beranda</a>
                        <a href="{{ url('/#fitur') }}" class="hover:text-white transition-colors">Fitur</a>
                        <a href="{{ url('/#cara-kerja') }}" class="hover:text-white transition-colors">Cara Kerja</a>
                        <a href="{{ url('/#faq') }}" class="hover:text-white transition-colors">FAQ</a>
                    </nav>
                @endauth

                {{-- Tombol Aksi Kanan --}}
                <div class="flex items-center gap-2.5 sm:gap-3">
                    {{-- Toggle Tema Cepat --}}
                    <button type="button" @click="$store.theme.toggle()" class="flex h-8 w-8 items-center justify-center rounded-xl bg-white/10 text-white/90 hover:bg-white/20 hover:text-white transition" title="Ganti Tema">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                    </button>

                    @auth
                        <a href="{{ route('groups.create') }}" class="inline-flex items-center gap-1.5 rounded-xl bg-white/15 px-3 py-1.5 text-xs font-bold text-white backdrop-blur-sm transition hover:bg-white/25 border border-white/20">
                            <span>+ Group</span>
                        </a>

                        <a href="{{ route('nongkrong.create') }}" class="inline-flex items-center gap-1.5 rounded-xl bg-white px-3.5 py-1.5 text-xs font-bold text-indigo-700 shadow-sm transition hover:bg-indigo-50">
                            <span>+ Nongkrong</span>
                        </a>

                        {{-- Link ke Profil Pengguna --}}
                        <a href="{{ route('profile.show') }}" class="hidden items-center gap-2.5 pl-2 md:flex border-l border-white/20 group hover:opacity-90 transition" title="Lihat Profil Saya">
                            @if (auth()->user()->avatar_path)
                                <img src="{{ asset('storage/' . auth()->user()->avatar_path) }}" alt="{{ auth()->user()->name }}" class="h-8 w-8 rounded-full object-cover ring-2 ring-white/40">
                            @else
                                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-white/20 text-xs font-black text-white ring-1 ring-white/40">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </span>
                            @endif
                            <div class="flex flex-col text-left">
                                <span class="max-w-[7.5rem] truncate text-xs font-bold text-white group-hover:text-sky-200 transition">{{ auth()->user()->name }}</span>
                                <span class="text-[10px] text-indigo-200">Profil Saya</span>
                            </div>
                        </a>

                        <form method="POST" action="{{ route('logout') }}" class="hidden sm:block">
                            @csrf
                            <button type="submit" class="rounded-xl px-2.5 py-1.5 text-xs font-semibold text-white/80 hover:bg-white/15 hover:text-white transition" title="Keluar">
                                Keluar
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="rounded-xl px-3.5 py-2 text-xs sm:text-sm font-bold text-white/90 transition hover:bg-white/15 hover:text-white">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" class="rounded-xl bg-white px-4 py-2 text-xs sm:text-sm font-bold text-indigo-700 shadow-md transition hover:bg-indigo-50 hover:shadow-lg">
                            Daftar Gratis
                        </a>
                    @endauth

                    {{-- Hamburger Mobile --}}
                    <button type="button" @click="navOpen = !navOpen" class="rounded-xl border border-white/20 p-2 text-white hover:bg-white/15 focus:outline-none lg:hidden"
                        :aria-expanded="navOpen" aria-controls="menu-mobile" aria-label="Buka menu navigasi">
                        <svg x-show="!navOpen" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 6h16M4 12h16m-7 6h7" stroke-linecap="round" />
                        </svg>
                        <svg x-show="navOpen" x-cloak class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M6 18L18 6M6 6l12 12" stroke-linecap="round" />
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Mobile Drawer Menu --}}
            <div id="menu-mobile" x-show="navOpen" x-cloak
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 -translate-y-2"
                class="border-t border-white/15 bg-gradient-to-b from-indigo-700 to-purple-800 px-4 py-4 text-white lg:hidden">
                @auth
                    <a href="{{ route('profile.show') }}" class="mb-3 flex items-center gap-3 border-b border-white/15 pb-3 hover:bg-white/5 p-2 rounded-xl transition">
                        @if (auth()->user()->avatar_path)
                            <img src="{{ asset('storage/' . auth()->user()->avatar_path) }}" alt="{{ auth()->user()->name }}" class="h-9 w-9 rounded-full object-cover">
                        @else
                            <span class="flex h-9 w-9 items-center justify-center rounded-full bg-white text-indigo-700 font-bold">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </span>
                        @endif
                        <div>
                            <p class="text-sm font-bold">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-indigo-200">Lihat & Edit Profil Saya →</p>
                        </div>
                    </a>

                    <nav class="space-y-1.5" aria-label="Utama (mobile)">
                        @foreach ($navItems as $item)
                            @php $isCurrent = str_starts_with($active, $item['match']); @endphp
                            <a href="{{ route($item['route']) }}"
                                @class([
                                    'nav-link flex items-center gap-2.5 rounded-xl px-3 py-2 text-sm font-semibold transition',
                                    'nav-link-active bg-white text-indigo-700' => $isCurrent,
                                    'text-white/90 hover:bg-white/10' => !$isCurrent,
                                ])>
                                <span>{{ $item['label'] }}</span>
                            </a>
                        @endforeach

                        <div class="pt-3 border-t border-white/15 flex flex-col gap-2">
                            <a href="{{ route('profile.show') }}" class="flex items-center justify-center rounded-xl bg-white/20 px-4 py-2 text-sm font-bold text-white hover:bg-white/30">
                                Profil Saya
                            </a>
                            <a href="{{ route('nongkrong.create') }}" class="flex items-center justify-center rounded-xl bg-white px-4 py-2 text-sm font-bold text-indigo-700">
                                + Buat Nongkrong Baru
                            </a>
                            <a href="{{ route('groups.create') }}" class="flex items-center justify-center rounded-xl bg-white/15 px-4 py-2 text-sm font-bold text-white hover:bg-white/20">
                                + Buat Group Circle
                            </a>
                            <form method="POST" action="{{ route('logout') }}" class="pt-2">
                                @csrf
                                <button class="w-full rounded-xl bg-red-500/20 px-4 py-2 text-sm font-semibold text-red-100 hover:bg-red-500/30">
                                    Keluar Akun
                                </button>
                            </form>
                        </div>
                    </nav>
                @else
                    <nav class="space-y-2 text-sm font-semibold" aria-label="Landing Links Mobile">
                        <a href="{{ url('/') }}" class="block rounded-lg px-3 py-2 hover:bg-white/10">Beranda</a>
                        <a href="{{ url('/#fitur') }}" class="block rounded-lg px-3 py-2 hover:bg-white/10">Fitur Kami</a>
                        <a href="{{ url('/#cara-kerja') }}" class="block rounded-lg px-3 py-2 hover:bg-white/10">Cara Kerja</a>
                        <a href="{{ url('/#faq') }}" class="block rounded-lg px-3 py-2 hover:bg-white/10">Pertanyaan Umum (FAQ)</a>
                        <div class="mt-4 flex flex-col gap-2 pt-2 border-t border-white/15">
                            <a href="{{ route('login') }}" class="text-center rounded-xl bg-white/15 py-2.5 text-sm font-bold">Masuk ke Akun</a>
                            <a href="{{ route('register') }}" class="text-center rounded-xl bg-white py-2.5 text-sm font-bold text-indigo-700">Daftar Akun Baru</a>
                        </div>
                    </nav>
                @endauth
            </div>
        </header>
    @else
        {{-- Hidden element theme toggle helper untuk auth pages --}}
        <div class="hidden" aria-hidden="true">
            <button @click="$store.theme.toggle()" type="button">Theme</button>
        </div>
    @endunless

    {{-- Main Content --}}
    <main class="w-full flex-1">
        @if (trim($__env->yieldContent('content')))
            <div class="{{ $isLanding ? 'w-full' : 'mx-auto w-full max-w-5xl px-4 py-6 md:py-10' }}">
                @include('partials.flash')
                @yield('content')
            </div>
        @endif
    </main>

    {{-- FOOTER: HANYA TAMPIL DI HALAMAN LANDING SAJA --}}
    @if ($isLanding)
        <footer class="mt-auto border-t border-slate-200/80 bg-white py-12 text-slate-600">
            <div class="mx-auto max-w-6xl px-4 sm:px-6">
                <div class="grid gap-8 md:grid-cols-4">
                    <div class="md:col-span-2">
                        <div class="flex items-center gap-3">
                            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-tr from-purple-600 to-blue-600 p-1 shadow-sm">
                                <img src="{{ asset('image/logo.png') }}" alt="BagiRata" class="h-7 w-auto object-contain brightness-0 invert">
                            </div>
                            <span class="text-xl font-black tracking-tight text-slate-900">
                                Bagi<span class="text-indigo-600">Rata</span>
                            </span>
                        </div>
                        <p class="mt-3 max-w-sm text-sm text-slate-500 leading-relaxed">
                            Platform pintar pengelola split bill, kas grup circle, dan penyederhanaan utang piutang nongkrong otomatis. Nongkrong jadi seru, bebas drama uang.
                        </p>
                        <div class="mt-4 flex items-center gap-2 text-xs font-semibold text-slate-500">
                            <span class="inline-block h-2 w-2 rounded-full bg-emerald-500"></span>
                            100% Aman & Transparan untuk semua kalangan
                        </div>
                    </div>

                    <div>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900">Navigasi</h3>
                        <ul class="mt-3 space-y-2 text-sm">
                            <li><a href="{{ url('/') }}" class="hover:text-indigo-600 transition">Beranda</a></li>
                            <li><a href="{{ url('/#fitur') }}" class="hover:text-indigo-600 transition">Fitur Unggulan</a></li>
                            <li><a href="{{ url('/#cara-kerja') }}" class="hover:text-indigo-600 transition">Cara Kerja</a></li>
                            <li><a href="{{ route('calculator.index') }}" class="hover:text-indigo-600 transition">Kalkulator Cepat</a></li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900">Akses Cepat</h3>
                        <ul class="mt-3 space-y-2 text-sm">
                            @auth
                                <li><a href="{{ route('dashboard') }}" class="hover:text-indigo-600 transition">Dashboard Saya</a></li>
                                <li><a href="{{ route('nongkrong.index') }}" class="hover:text-indigo-600 transition">Daftar Nongkrong</a></li>
                                <li><a href="{{ route('groups.index') }}" class="hover:text-indigo-600 transition">Grup & Circle</a></li>
                                <li><a href="{{ route('debts.overview') }}" class="hover:text-indigo-600 transition">Cek Utang-Piutang</a></li>
                            @else
                                <li><a href="{{ route('login') }}" class="hover:text-indigo-600 transition">Masuk Akun</a></li>
                                <li><a href="{{ route('register') }}" class="hover:text-indigo-600 transition">Daftar Akun Gratis</a></li>
                            @endauth
                        </ul>
                    </div>
                </div>

                <div class="mt-10 border-t border-slate-100 pt-6 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-400">
                    <p>&copy; {{ date('Y') }} BagiRata. Semua hak dilindungi undang-undang.</p>
                    <p>Dibuat untuk nongkrong yang lebih santai & tanpa drama uang.</p>
                </div>
            </div>
        </footer>
    @endif

    @stack('scripts')
</body>
</html>
