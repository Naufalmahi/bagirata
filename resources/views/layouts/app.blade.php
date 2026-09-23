<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'BagiRata') · Nongkrong Fun</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-800 antialiased">
    @auth
        <nav class="sticky top-0 z-30 border-b border-slate-200 bg-white/90 backdrop-blur">
            <div class="mx-auto flex max-w-5xl items-center justify-between gap-3 px-4 py-3">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2 font-extrabold text-indigo-600">
                    <span class="grid h-8 w-8 place-items-center rounded-xl bg-indigo-600 text-white">N</span>
                    <span>BagiRata<span class="text-slate-400 font-semibold">/nongkrong</span></span>
                </a>

                <div class="flex items-center gap-1 text-sm font-medium">
                    @php $active = request()->route()?->getName() ?? ''; @endphp
                    <a href="{{ route('dashboard') }}"
                        class="rounded-lg px-3 py-1.5 {{ str_starts_with($active, 'dashboard') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100' }}">Dashboard</a>
                    <a href="{{ route('groups.index') }}"
                        class="rounded-lg px-3 py-1.5 {{ str_starts_with($active, 'groups') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100' }}">Groups</a>
                    <a href="{{ route('nongkrong.index') }}"
                        class="rounded-lg px-3 py-1.5 {{ str_starts_with($active, 'nongkrong') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100' }}">Nongkrong</a>
<<<<<<< HEAD
                    <a href="{{ route('debts.overview') }}"
                        class="rounded-lg px-3 py-1.5 {{ str_starts_with($active, 'debts') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100' }}">Utang-Piutang</a>
=======
>>>>>>> 6561da739345e3ff0fdab546ef4f928a872f067a
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('groups.create') }}"
                        class="hidden sm:inline-flex rounded-lg bg-indigo-600 px-3 py-1.5 text-sm font-semibold text-white hover:bg-indigo-700">+ Group</a>
                    <div class="hidden md:flex items-center gap-2 text-sm text-slate-700">
                        <span class="grid h-8 w-8 place-items-center rounded-full bg-indigo-100 font-bold text-indigo-600">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                        <span class="max-w-[10rem] truncate font-semibold">{{ auth()->user()->name }}</span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="rounded-lg px-3 py-1.5 text-sm text-slate-500 hover:bg-slate-100">Keluar</button>
                    </form>
                </div>
            </div>
        </nav>
    @endauth

    <main class="mx-auto w-full max-w-5xl px-4 py-6 md:py-10">
        @if (session('success'))
            <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-5 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-800">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="mx-auto max-w-5xl px-4 pb-10 pt-4 text-center text-xs text-slate-400">
        BagiRata · patungan gak ribet, yang penting nggak ada drama soal duit.
    </footer>
</body>
</html>