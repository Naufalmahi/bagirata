<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'BagiRata') · Nongkrong Fun</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{--
        Anti-flash: pasang kelas `dark` sebelum CSS pertama kali di-render.
        Nggak boleh pakai Alpine di sini karena Alpine dimuat lewat @vite dan
        sudah terlambat. Inline script harus tetap sinkron & tanpa dependensi.
    --}}
    <script>
        (function () {
            try {
                // Kunci ini harus sama dengan THEME_KEY di resources/js/app.js.
                var pref = localStorage.getItem('bagirata-theme') || 'system';
                var dark = pref === 'dark' ||
                    (pref === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
                document.documentElement.classList.toggle('dark', dark);
                document.documentElement.style.colorScheme = dark ? 'dark' : 'light';
            } catch (e) {
                // localStorage diblokir (mode privat / iframe sandbox) — pakai default sistem.
            }
        })();
    </script>
</head>
<body class="flex min-h-screen flex-col">
    @php
        $active = request()->route()?->getName() ?? '';
        $navItems = [
            ['route' => 'dashboard', 'label' => 'Dashboard', 'match' => 'dashboard'],
            ['route' => 'groups.index', 'label' => 'Groups', 'match' => 'groups'],
            ['route' => 'nongkrong.index', 'label' => 'Nongkrong', 'match' => 'nongkrong'],
            ['route' => 'calculator.index', 'label' => 'Kalkulator', 'match' => 'calculator'],
            ['route' => 'budgets.index', 'label' => 'Budgeting', 'match' => 'budgets'],
            ['route' => 'debts.overview', 'label' => 'Utang-Piutang', 'match' => 'debts'],
        ];
    @endphp

    <header x-data="{ navOpen: false }" class="sticky top-0 z-30 border-b border-line bg-raised/85 backdrop-blur">
        <div class="mx-auto flex max-w-5xl items-center gap-3 px-4 py-3">
            <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" class="flex items-center gap-2">
                <span class="brand-mark">N</span>
                <span class="text-base font-extrabold text-ink">
                    BagiRata<span class="font-semibold text-ink-subtle">/nongkrong</span>
                </span>
            </a>

            @auth
                {{-- Navigasi desktop --}}
                <nav class="ml-4 hidden items-center gap-1 lg:flex" aria-label="Utama">
                    @foreach ($navItems as $item)
                        <a href="{{ route($item['route']) }}"
                            @class([
                                'nav-link',
                                'nav-link-active' => str_starts_with($active, $item['match']),
                            ])
                            @if (str_starts_with($active, $item['match'])) aria-current="page" @endif>
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </nav>
            @endauth

            <div class="ml-auto flex items-center gap-2">
                <button type="button" @click="$store.theme.toggle()"
                    class="btn btn-ghost btn-icon"
                    :aria-label="$store.theme.dark ? 'Ganti ke mode terang' : 'Ganti ke mode gelap'"
                    :title="$store.theme.dark ? 'Mode terang' : 'Mode gelap'">
                    <svg x-show="!$store.theme.dark" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                        <circle cx="12" cy="12" r="4" />
                        <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41" />
                    </svg>
                    <svg x-show="$store.theme.dark" x-cloak class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z" />
                    </svg>
                </button>

                @auth
                    <a href="{{ route('groups.create') }}" class="btn btn-primary btn-sm hidden sm:inline-flex">+ Group</a>

                    <div class="hidden items-center gap-2 md:flex">
                        <span class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                        <span class="max-w-[10rem] truncate text-sm font-semibold text-ink">{{ auth()->user()->name }}</span>
                    </div>

                    <form method="POST" action="{{ route('logout') }}" class="hidden lg:block">
                        @csrf
                        <button class="btn btn-ghost btn-sm">Keluar</button>
                    </form>

                    {{-- Tombol menu untuk layar kecil --}}
                    <button type="button" @click="navOpen = !navOpen" class="btn btn-ghost btn-icon lg:hidden"
                        :aria-expanded="navOpen" aria-controls="menu-mobile" aria-label="Buka menu">
                        <svg x-show="!navOpen" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" aria-hidden="true">
                            <path d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg x-show="navOpen" x-cloak class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" aria-hidden="true">
                            <path d="M6 6l12 12M18 6L6 18" />
                        </svg>
                    </button>
                @else
                    <a href="{{ route('login') }}" class="btn btn-ghost btn-sm">Masuk</a>
                    <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Daftar</a>
                @endauth
            </div>
        </div>

        @auth
            {{-- Panel navigasi layar kecil --}}
            <div id="menu-mobile" x-show="navOpen" x-cloak
                class="border-t border-line bg-raised lg:hidden">
                <nav class="mx-auto max-w-5xl space-y-1 px-4 py-3" aria-label="Utama (mobile)">
                    @foreach ($navItems as $item)
                        <a href="{{ route($item['route']) }}"
                            @class([
                                'nav-link block w-full text-left',
                                'nav-link-active' => str_starts_with($active, $item['match']),
                            ])
                            @if (str_starts_with($active, $item['match'])) aria-current="page" @endif>
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                    <a href="{{ route('groups.create') }}" class="nav-link block w-full text-left">+ Bikin Group</a>

                    <div class="flex items-center justify-between gap-3 pt-3 sm:hidden">
                        <span class="flex items-center gap-2">
                            <span class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                            <span class="text-sm font-semibold">{{ auth()->user()->name }}</span>
                        </span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="btn btn-secondary btn-sm">Keluar</button>
                        </form>
                    </div>
                </nav>
            </div>
        @endauth
    </header>

    <main class="mx-auto w-full max-w-5xl flex-1 px-4 py-6 md:py-10">
        @include('partials.flash')

        @yield('content')
    </main>

    <footer class="mx-auto w-full max-w-5xl px-4 pb-10 pt-4 text-center text-xs text-ink-subtle">
        BagiRata · patungan gak ribet, yang penting nggak ada drama soal duit.
    </footer>
</body>
</html>
