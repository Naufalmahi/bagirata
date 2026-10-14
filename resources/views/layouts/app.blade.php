<!DOCTYPE html>
<html lang="id" class="dark scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'BagiRata') · Solusi Patungan & Split Bill Tanpa Drama</title>
    <link rel="icon" type="image/png" href="{{ asset('image/logo.png') }}">

    <!-- Google Fonts Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Font Awesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Phosphor Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="overflow-hidden flex h-screen w-screen bg-[#110b17] text-white selection:bg-[#EB9998]/30 selection:text-white"
      x-data="{ mobileNav: false, rightSidebarOpen: false }">

    @php
        $activeRoute = request()->route()?->getName() ?? '';
        $isAuth = auth()->check();
        $user = auth()->user();
        $userInitials = $isAuth ? strtoupper(substr($user->name, 0, 2)) : 'BR';
        $userTag = $isAuth ? ($user->username ? '@' . $user->username : '#' . str_pad($user->id, 4, '0', STR_PAD_LEFT)) : '@tamu';
        $allGroups = $isAuth ? $user->groups : collect([]);
    @endphp

    <!-- Mobile Nav Backdrop -->
    <div x-show="mobileNav"
         x-cloak
         @click="mobileNav = false"
         class="fixed inset-0 z-40 bg-black/70 backdrop-blur-sm lg:hidden">
    </div>

    <!-- 1. LEFT SIDEBAR CONTAINER -->
    <aside id="sidebar-container"
           :class="mobileNav ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
           class="fixed inset-y-0 left-0 z-50 flex h-full w-[340px] bg-custom-darker border-r border-white/5 transition-transform duration-300 ease-in-out lg:relative lg:translate-x-0 shrink-0">

        <!-- Dynamic SVG Background for Discord Curve -->
        <svg id="svg-bg" class="absolute top-0 left-0 w-full h-full z-0 pointer-events-none">
            <path id="sidebar-path" d="" stroke="rgba(255,255,255,0.15)" stroke-width="1.5" fill="none" />
        </svg>

        <!-- Mac Window Controls -->
        <div class="absolute top-4 left-4 flex gap-2 z-50">
            <div class="w-3 h-3 rounded-full bg-red-500 cursor-pointer hover:bg-red-600 transition"></div>
            <div class="w-3 h-3 rounded-full bg-yellow-500 cursor-pointer hover:bg-yellow-600 transition"></div>
            <div class="w-3 h-3 rounded-full bg-green-500 cursor-pointer hover:bg-green-600 transition"></div>
        </div>

        <!-- 1A. Leftmost Server Column (72px) -->
        <div class="w-[72px] h-full flex flex-col items-center pt-16 pb-4 space-y-3 overflow-y-auto relative z-10 shrink-0 custom-scrollbar">
            
            <!-- BagiRata Home Logo Button -->
            @php 
                $isHomeActive = request()->routeIs('dashboard', 'landing') || request()->is('/');
                $homeTarget = ($isAuth && $allGroups->isNotEmpty()) ? route('groups.show', $allGroups->first()) : ($isAuth ? route('dashboard') : url('/'));
            @endphp
            <a href="{{ $homeTarget }}"
               id="discord-logo"
               title="Beranda & Explore BagiRata"
               class="server-icon {{ $isHomeActive && !request()->is('groups*') ? 'active' : '' }} w-12 h-12 rounded-[24px] bg-[#5D549C] flex items-center justify-center text-white mb-10 shadow-lg shadow-[#5D549C]/30 hover:rounded-2xl transition-all shrink-0">
                <img src="{{ asset('image/logo.png') }}" alt="BagiRata" class="w-8 h-8 object-contain brightness-0 invert">
            </a>

            <!-- DAFTAR SEMUA GROUP USER (ALA DISCORD SERVER LIST) -->
            @if ($isAuth && $allGroups->isNotEmpty())
                @foreach ($allGroups as $index => $grp)
                    @php 
                        $isGrpActive = (isset($group) && $group->id === $grp->id) || request()->is('groups/' . $grp->id . '*');
                    @endphp
                    <a href="{{ route('groups.show', $grp) }}"
                       @if ($index === 0) id="first-server" @endif
                       title="{{ $grp->name }}"
                       class="server-icon {{ $isGrpActive ? 'active' : '' }} w-12 h-12 rounded-[24px] bg-[#2b253a] flex items-center justify-center cursor-pointer overflow-hidden border {{ $isGrpActive ? 'border-[#EB9998] shadow-[0_0_20px_rgba(235,153,152,0.4)] rounded-2xl' : 'border-white/10 hover:border-[#A373D3]/50' }} shrink-0">
                        @if ($grp->avatar_path)
                            <img src="{{ asset('storage/' . $grp->avatar_path) }}" alt="{{ $grp->name }}" class="w-full h-full object-cover">
                        @else
                            <span class="font-black text-sm {{ $isGrpActive ? 'text-[#EB9998]' : 'text-[#A373D3]' }}">
                                {{ strtoupper(substr($grp->name, 0, 2)) }}
                            </span>
                        @endif
                    </a>
                @endforeach
            @else
                <a href="{{ route('groups.create') }}"
                   id="first-server"
                   title="Buat Circle Pertama"
                   class="server-icon w-12 h-12 rounded-[24px] bg-gradient-to-br from-[#5D549C] to-[#7E3A63] flex items-center justify-center cursor-pointer overflow-hidden shrink-0">
                    <i class="ph-bold ph-users-three text-white text-xl"></i>
                </a>
            @endif

            <!-- Plus Icon (Buat Group Baru) -->
            <a href="{{ route('groups.create') }}"
               id="plus-icon"
               title="Buat Circle / Group Baru"
               class="server-icon w-12 h-12 rounded-[24px] bg-white/5 flex items-center justify-center cursor-pointer text-[#EB9998] hover:bg-[#EB9998] hover:text-white transition-colors border border-dashed border-white/20 mt-2 shrink-0">
                <i class="fa-solid fa-plus text-lg"></i>
            </a>
        </div>

        <!-- 1B. Main Sidebar (Channels / Explore Menu) -->
        <div class="flex-1 h-full flex flex-col pt-16 relative z-10 overflow-hidden">
            
            {{-- Dynamic Content: jika di dalam group tampilkan channels, jika umum tampilkan explore menu --}}
            @hasSection('sidebar')
                <div class="flex-1 overflow-y-auto px-3.5 space-y-3 custom-scrollbar">
                    @yield('sidebar')
                </div>
            @else
                <!-- Default Explore Navigation -->
                <div class="px-6 pb-4 relative z-10 flex items-center justify-between">
                    <div>
                        <h1 class="text-[20px] font-bold text-white tracking-wide">Explore</h1>
                        <span class="text-[10px] uppercase font-bold tracking-wider text-[#6F70A2]">BagiRata Workspace</span>
                    </div>
                    <button @click="mobileNav = false" class="lg:hidden text-gray-400 hover:text-white p-1">
                        <i class="ph-bold ph-x text-lg"></i>
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto px-4 space-y-1 custom-scrollbar">
                    <!-- Home / Dashboard -->
                    <a href="{{ $isAuth ? route('dashboard') : url('/') }}"
                       class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ ($activeRoute === 'dashboard' || request()->is('/')) ? 'bg-white/10 text-white border border-[#A373D3]/30 shadow-sm font-semibold' : 'text-[#a19db0] hover:bg-white/5 hover:text-white' }}">
                        <i class="ph-fill ph-compass text-[20px] w-6 text-center text-[#A373D3]"></i>
                        <span class="font-medium text-[14px]">Home</span>
                    </a>

                    <div class="mt-5 mb-2 px-4 text-[11px] font-bold text-white/30 tracking-wider uppercase">Features</div>
                    
                    <!-- Groups -->
                    <a href="{{ route('groups.index') }}"
                       class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ str_starts_with($activeRoute, 'groups') ? 'bg-white/10 text-white border border-[#A373D3]/30 font-semibold' : 'text-[#a19db0] hover:bg-white/5 hover:text-white' }}">
                        <i class="ph ph-users-three text-[20px] w-6 text-center text-[#6F70A2]"></i>
                        <span class="font-medium text-[14px]">Groups</span>
                    </a>
                    
                    <!-- Nongkrong -->
                    <a href="{{ route('nongkrong.index') }}"
                       class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ str_starts_with($activeRoute, 'nongkrong') ? 'bg-white/10 text-white border border-[#A373D3]/30 font-semibold' : 'text-[#a19db0] hover:bg-white/5 hover:text-white' }}">
                        <i class="ph ph-coffee text-[20px] w-6 text-center text-[#6F70A2]"></i>
                        <span class="font-medium text-[14px]">Nongkrong</span>
                    </a>
                    
                    <!-- Kalkulator -->
                    <a href="{{ route('calculator.index') }}"
                       class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ str_starts_with($activeRoute, 'calculator') ? 'bg-white/10 text-white border border-[#A373D3]/30 font-semibold' : 'text-[#a19db0] hover:bg-white/5 hover:text-white' }}">
                        <i class="ph ph-calculator text-[20px] w-6 text-center text-[#6F70A2]"></i>
                        <span class="font-medium text-[14px]">Kalkulator</span>
                    </a>
                    
                    <!-- Budgeting -->
                    <a href="{{ route('budgets.index') }}"
                       class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ str_starts_with($activeRoute, 'budgets') ? 'bg-white/10 text-white border border-[#A373D3]/30 font-semibold' : 'text-[#a19db0] hover:bg-white/5 hover:text-white' }}">
                        <i class="ph ph-chart-pie-slice text-[20px] w-6 text-center text-[#6F70A2]"></i>
                        <span class="font-medium text-[14px]">Budgeting</span>
                    </a>

                    <!-- Utang-Piutang -->
                    <div class="flex items-center justify-between px-4 py-2.5 rounded-lg cursor-pointer transition-colors {{ str_starts_with($activeRoute, 'debts') ? 'bg-white/10 border border-[#A373D3]/30 text-white' : 'hover:bg-white/5 text-[#a19db0] hover:text-white' }}">
                        <a href="{{ route('debts.overview') }}" class="flex items-center gap-3 flex-1">
                            <i class="ph-fill ph-hand-coins text-[20px] text-[#EB9998] w-6 text-center"></i>
                            <span class="font-bold text-[14px]">Utang-Piutang</span>
                        </a>
                        <button type="button"
                                onclick="openModal('modal-hutang')"
                                title="Buka Scanner / Form Pembayaran"
                                class="p-1 rounded-md hover:bg-white/10 text-[#EB9998]">
                            <div class="w-2.5 h-2.5 rounded-full bg-[#EB9998] shadow-[0_0_8px_#EB9998]"></div>
                        </button>
                    </div>

                    <!-- Teman -->
                    <a href="{{ route('friends.index') }}"
                       class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ str_starts_with($activeRoute, 'friends') ? 'bg-white/10 text-white border border-[#A373D3]/30 font-semibold' : 'text-[#a19db0] hover:bg-white/5 hover:text-white' }}">
                        <i class="ph ph-user-list text-[20px] w-6 text-center text-[#6F70A2]"></i>
                        <span class="font-medium text-[14px]">Teman</span>
                    </a>
                </div>
            @endif

            <!-- Bottom Voice & User Section -->
            <div class="mt-auto px-4 pb-4 pt-3 relative bg-[#1f1a2b] border-t border-white/5 shrink-0">
                
                <!-- Audio Frequency Equalizer (Colors: #EB9998 to #A373D3) -->
                <div class="audio-bars mb-3 opacity-90" title="Audio Activity Status">
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                </div>

                <!-- User Profile & Controls -->
                <div class="flex items-center justify-between relative z-10 border-t border-white/5 pt-2.5">
                    @if ($isAuth)
                        <a href="{{ route('profile.show') }}" class="flex items-center gap-2.5 cursor-pointer group hover:bg-white/5 p-1 -ml-1 rounded-lg transition-colors flex-1 min-w-0">
                            <div class="relative shrink-0">
                                @if ($user->avatar_path)
                                    <img src="{{ asset('storage/' . $user->avatar_path) }}" alt="{{ $user->name }}" class="w-9 h-9 rounded-full object-cover ring-1 ring-white/20">
                                @else
                                    <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-[#5D549C] to-[#7E3A63] flex items-center justify-center text-xs font-bold text-white shadow-md">
                                        {{ $userInitials }}
                                    </div>
                                @endif
                                <div class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-emerald-500 rounded-full border-2 border-[#1f1a2b]"></div>
                            </div>
                            <div class="flex flex-col min-w-0">
                                <span class="text-xs font-semibold text-white leading-tight truncate">{{ $user->name }}</span>
                                <span class="text-[10px] text-[#6F70A2] truncate">{{ $userTag }}</span>
                            </div>
                        </a>

                        <div class="flex items-center gap-1 text-[#6F70A2]">
                            <button type="button" class="p-1.5 rounded-lg hover:text-white hover:bg-white/5 transition" title="Mikrofon"><i class="fa-solid fa-microphone text-xs"></i></button>
                            <button type="button" class="p-1.5 rounded-lg hover:text-white hover:bg-white/5 transition" title="Headset"><i class="fa-solid fa-headphones text-xs"></i></button>
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="p-1.5 rounded-lg hover:text-[#EB9998] hover:bg-white/5 transition" title="Keluar / Logout">
                                    <i class="fa-solid fa-arrow-right-from-bracket text-xs"></i>
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="flex items-center justify-between w-full gap-2">
                            <a href="{{ route('login') }}" class="flex-1 text-center py-2 text-xs font-bold rounded-lg bg-white/5 hover:bg-white/10 text-white transition">Masuk</a>
                            <a href="{{ route('register') }}" class="flex-1 text-center py-2 text-xs font-bold rounded-lg bg-gradient-to-r from-[#5D549C] to-[#7E3A63] text-white hover:opacity-90 transition shadow-sm">Daftar</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </aside>

    <!-- 2. CENTER MAIN CONTENT AREA -->
    <main class="flex-1 bg-[#110b17] h-full flex flex-col relative overflow-hidden z-0">
        
        <!-- Ambient Blur Blobs -->
        <div class="absolute -top-[10%] -left-[10%] w-[45%] h-[45%] bg-[#5D549C]/15 blur-[120px] rounded-full pointer-events-none"></div>
        <div class="absolute top-[30%] -right-[15%] w-[40%] h-[40%] bg-[#7E3A63]/15 blur-[120px] rounded-full pointer-events-none"></div>

        <!-- Header Bar -->
        <header class="h-[64px] flex items-center justify-between px-4 sm:px-6 border-b border-white/5 bg-black/30 shrink-0 relative z-20 backdrop-blur-md">
            <div class="flex items-center gap-3">
                <!-- Mobile Menu Button -->
                <button type="button"
                        @click="mobileNav = !mobileNav"
                        class="p-2 rounded-xl text-gray-300 hover:text-white hover:bg-white/10 lg:hidden">
                    <i class="ph-bold ph-list text-xl"></i>
                </button>

                @hasSection('header-left')
                    @yield('header-left')
                @else
                    <!-- Search Input -->
                    <div class="w-56 sm:w-80 md:w-96 relative">
                        <i class="ph ph-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-500"></i>
                        <input type="text"
                               placeholder="Explore Bagirata..."
                               class="w-full bg-[#1b1226] border border-white/10 rounded-xl py-2 pl-10 pr-4 text-xs sm:text-[13px] text-white focus:outline-none focus:border-[#A373D3]/60 focus:ring-1 focus:ring-[#A373D3]/30 transition-all placeholder-gray-500 shadow-inner">
                    </div>
                @endif
            </div>

            <div class="flex items-center gap-2 sm:gap-3">
                @if ($isAuth)
                    <a href="{{ route('nongkrong.create') }}" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white/10 hover:bg-white/15 text-white text-xs font-bold border border-white/10 transition">
                        <i class="ph-bold ph-plus text-xs"></i>
                        <span>Nongkrong</span>
                    </a>
                    <button type="button"
                            onclick="openModal('modal-hutang')"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-gradient-to-r from-[#5D549C] to-[#7E3A63] hover:opacity-90 text-white text-xs font-bold shadow-sm transition">
                        <i class="ph-bold ph-qr-code text-sm"></i>
                        <span class="hidden sm:inline">Scan / Tagih</span>
                    </button>
                @else
                    <a href="{{ route('login') }}" class="px-3.5 py-1.5 rounded-xl bg-white/10 text-white text-xs font-bold hover:bg-white/15 transition">Masuk</a>
                @endif

                <!-- Toggle Right Sidebar Button (Info Channel & Anggota - Persis Discord!) -->
                <button type="button"
                        @click="rightSidebarOpen = !rightSidebarOpen"
                        :class="rightSidebarOpen ? 'text-[#EB9998] bg-white/10 shadow-inner' : 'text-[#6F70A2] hover:text-white hover:bg-white/5'"
                        class="p-2 rounded-xl transition"
                        title="Info Channel & Daftar Anggota">
                    <i class="ph-bold ph-users text-lg"></i>
                </button>
            </div>
        </header>

        <!-- Main Workspace / Content -->
        <div class="flex-1 min-h-0 relative z-10 flex flex-col overflow-hidden">
            <!-- Flash notifications -->
            <div class="px-4 pt-2 sm:px-6 shrink-0">
                @include('partials.flash')
            </div>

            <!-- Page View Content -->
            <div class="flex-1 min-h-0 overflow-y-auto custom-scrollbar flex flex-col">
                @yield('content')
            </div>
        </div>
    </main>

    <!-- 3. RIGHT SIDEBAR CONTAINER (Default: TERSEMBUNYI, muncul saat info channel/anggota diklik!) -->
    <aside x-show="rightSidebarOpen"
           x-cloak
           x-transition:enter="transition ease-out duration-200"
           x-transition:enter-start="translate-x-full opacity-0"
           x-transition:enter-end="translate-x-0 opacity-100"
           x-transition:leave="transition ease-in duration-150"
           x-transition:leave-start="translate-x-0 opacity-100"
           x-transition:leave-end="translate-x-full opacity-0"
           class="fixed inset-y-0 right-0 z-50 w-[300px] sm:w-[320px] h-full bg-[#1f1a2b] flex flex-col pt-5 p-5 shrink-0 overflow-y-auto border-l border-white/5 shadow-[-10px_0_30px_rgba(0,0,0,0.5)] custom-scrollbar">
        
        <!-- Header Panel with Close Button -->
        <div class="flex items-center justify-between pb-3 border-b border-white/5 mb-4">
            <span class="text-xs font-bold uppercase tracking-wider text-[#6F70A2] flex items-center gap-1.5">
                <i class="ph-bold ph-info text-sm"></i>
                <span>Info & Anggota</span>
            </span>
            <button type="button" @click="rightSidebarOpen = false" class="p-1 rounded-lg hover:bg-white/10 text-gray-400 hover:text-white transition">
                <i class="ph-bold ph-x text-base"></i>
            </button>
        </div>

        @hasSection('right-sidebar')
            @yield('right-sidebar')
        @else
            <!-- Default Profile & Circle Panel -->
            <div class="flex flex-col items-center mb-6">
                <div class="glowing-avatar-wrapper mb-3 scale-90">
                    <div class="ring-outer"></div>
                    <div class="ring-middle"></div>
                    <div class="ring-inner"></div>
                    
                    @if ($isAuth && $user->avatar_path)
                        <img src="{{ asset('storage/' . $user->avatar_path) }}" alt="{{ $user->name }}" class="relative z-20 w-[95px] h-[95px] rounded-full object-cover border-4 border-[#1f1a2b]">
                    @else
                        <div class="relative z-20 w-[95px] h-[95px] bg-gradient-to-tr from-[#5D549C] to-[#7E3A63] rounded-full flex items-center justify-center text-3xl font-black text-white border-4 border-[#1f1a2b] shadow-xl">
                            {{ $userInitials }}
                        </div>
                    @endif
                    <div class="absolute bottom-2 right-2 w-4 h-4 bg-emerald-500 rounded-full border-[3px] border-[#1f1a2b] z-30" title="Online"></div>
                </div>
                
                <h2 class="text-[17px] font-bold text-white tracking-wide text-center">
                    {{ $isAuth ? $user->name : 'BagiRata Guest' }}
                </h2>
                <p class="text-[12px] text-[#6F70A2] mt-0.5 font-medium">
                    {{ $userTag }}
                </p>
            </div>

            <!-- Top Circles -->
            <div class="mb-6">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-[11px] font-bold text-[#6F70A2] uppercase tracking-widest">Daftar Circle</h3>
                    <a href="{{ route('groups.index') }}" class="text-[11px] font-medium text-[#A373D3] hover:underline">Semua</a>
                </div>
                
                <div class="space-y-2">
                    @if ($isAuth && $allGroups->isNotEmpty())
                        @foreach ($allGroups->take(5) as $g)
                            <a href="{{ route('groups.show', $g) }}" class="glass-card rounded-xl p-2.5 flex items-center gap-3 cursor-pointer hover:bg-white/5 transition-colors border border-white/5">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-[#5D549C] to-[#7E3A63] flex items-center justify-center font-bold text-white text-xs shrink-0 shadow-sm">
                                    {{ strtoupper(substr($g->name, 0, 2)) }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="text-[12px] font-bold text-white truncate">{{ $g->name }}</div>
                                    <div class="text-[10px] text-[#6F70A2]">{{ $g->members_count ?? $g->members->count() }} Anggota</div>
                                </div>
                            </a>
                        @endforeach
                    @else
                        <p class="text-xs text-[#6F70A2] italic text-center py-4">Belum ada circle.</p>
                    @endif
                </div>
            </div>
        @endif
    </aside>

    <!-- 4. MODAL UTANG-PIUTANG (DISCORD INTERACTIVE MULTI-STEP) -->
    <div id="modal-hutang" class="fixed inset-0 z-50 flex items-center justify-center p-4 modal-overlay bg-black/70 backdrop-blur-md">
        <div class="modal-content w-full max-w-sm bg-[#1a1029]/95 backdrop-blur-2xl rounded-2xl border border-[#A373D3]/30 shadow-[0_0_50px_rgba(163,115,211,0.25)] relative overflow-hidden text-white">
            
            <button type="button" onclick="closeModal('modal-hutang')" class="absolute top-4 right-4 text-gray-400 hover:text-white z-20 w-8 h-8 rounded-full bg-white/5 flex items-center justify-center transition">
                <i class="ph-bold ph-x"></i>
            </button>

            <div class="relative z-10">
                <!-- STEP 1: SCAN -->
                <div id="step-scan" class="p-7">
                    <div class="text-center mb-6">
                        <div class="w-14 h-14 rounded-2xl bg-[#5D549C]/30 border border-[#A373D3]/40 flex items-center justify-center text-[#A373D3] mx-auto mb-3 shadow-inner">
                            <i class="ph-fill ph-qr-code text-3xl"></i>
                        </div>
                        <h2 class="text-lg font-bold text-white">Scan QR Rekan</h2>
                        <p class="text-[12px] text-[#6F70A2] mt-0.5">Arahkan kamera untuk mencatat hutang</p>
                    </div>
                    <div class="relative w-full aspect-square bg-black/40 rounded-2xl border-2 border-dashed border-white/10 flex flex-col items-center justify-center cursor-pointer group hover:border-[#A373D3]/60 transition" onclick="switchStep('input')">
                        <div class="absolute top-0 left-0 w-full h-1 bg-[#EB9998] shadow-[0_0_20px_#EB9998] animate-[scan_2s_ease-in-out_infinite]"></div>
                        <i class="ph ph-scan text-5xl text-gray-600 group-hover:text-[#A373D3] transition-colors"></i>
                        <p class="text-[11px] text-gray-400 mt-3">Klik simulasi scan ke <span class="font-bold text-[#EB9998]">Reza</span></p>
                    </div>
                    <style>@keyframes scan { 0%{top:0;opacity:0} 10%{opacity:1} 90%{opacity:1} 100%{top:100%;opacity:0} }</style>
                </div>

                <!-- STEP 2: INPUT -->
                <div id="step-input" class="p-7 hidden">
                    <div class="text-center mb-6">
                        <div class="w-12 h-12 rounded-full bg-[#5D549C] mx-auto mb-3 flex items-center justify-center font-bold text-white shadow-md">RZ</div>
                        <h2 class="text-lg font-bold text-white">Catat Tagihan</h2>
                        <p class="text-[12px] text-[#6F70A2]">Kepada <span class="text-[#EB9998] font-bold">Rzene</span></p>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <input type="number" value="50000" class="w-full bg-black/40 border border-white/10 rounded-xl py-3.5 px-4 text-xl font-bold text-white focus:outline-none focus:border-[#A373D3] text-center shadow-inner">
                        </div>
                        <button type="button" onclick="switchStep('payment')" class="w-full py-3.5 bg-gradient-to-r from-[#5D549C] via-[#7E3A63] to-[#EB9998] text-white text-[14px] font-bold rounded-xl shadow-[0_4px_15px_rgba(163,115,211,0.3)] hover:opacity-95 transition">
                            Lanjut Pembayaran
                        </button>
                    </div>
                </div>

                <!-- STEP 3: BAYAR -->
                <div id="step-payment" class="p-7 hidden">
                    <div class="bg-black/40 border border-white/5 rounded-xl p-4 mb-4 text-center mt-2">
                        <p class="text-[10px] text-[#6F70A2] font-bold uppercase tracking-wider mb-1">Transfer Rek BCA</p>
                        <p class="text-xl font-mono font-bold text-[#A373D3]">872 123 9988</p>
                        <p class="text-[11px] text-gray-400 mt-0.5">a.n Rzene</p>
                    </div>
                    
                    <div class="w-full h-24 bg-black/30 border border-dashed border-white/20 rounded-xl flex flex-col items-center justify-center text-gray-400 hover:text-[#A373D3] hover:border-[#A373D3] cursor-pointer mb-5 transition" onclick="this.innerHTML='<i class=\'ph-fill ph-check-circle text-emerald-400 text-2xl mb-1\'></i><span class=\'text-[11px] font-bold text-white\'>Bukti diunggah</span>'; this.classList.add('border-emerald-500/40', 'bg-emerald-500/10')">
                        <i class="ph ph-image text-2xl mb-1.5"></i>
                        <span class="text-[11px] font-bold uppercase tracking-wide">Upload Bukti Transfer</span>
                    </div>
                    
                    <button type="button" onclick="simulateWebhook()" class="w-full py-3.5 bg-gradient-to-r from-[#5D549C] to-[#EB9998] text-white text-[14px] font-bold rounded-xl hover:opacity-95 transition flex justify-center items-center gap-2 shadow-lg shadow-[#5D549C]/25">
                        <span id="btn-pay-text">Kirim Pembayaran</span>
                        <i id="btn-pay-icon" class="ph-bold ph-paper-plane-tilt text-lg"></i>
                    </button>
                </div>

                <!-- STEP 4: STRUK -->
                <div id="step-receipt" class="p-7 hidden text-center">
                    <div class="w-14 h-14 bg-emerald-500/20 rounded-full flex items-center justify-center mx-auto mb-4 border border-emerald-500/30">
                        <i class="ph-bold ph-check text-2xl text-emerald-400"></i>
                    </div>
                    <h2 class="text-lg font-bold text-white mb-4">Pembayaran Sukses!</h2>
                    
                    <div class="bg-black/40 border-l-[3px] border-[#EB9998] rounded-r-lg p-4 mb-5 text-left relative">
                        <div class="flex items-center gap-2 mb-2">
                            <i class="ph-fill ph-robot text-[#EB9998] text-base"></i>
                            <span class="text-[11px] font-bold text-gray-300">BagiRata Webhook</span>
                        </div>
                        <h4 class="font-bold text-white text-[13px] mb-1">Receipt Generated</h4>
                        <p class="text-[11px] text-[#6F70A2] mb-3">Menunggu konfirmasi final dari @Rzene.</p>
                        <div class="bg-[#130a1e] p-2.5 rounded-lg border border-white/5 flex justify-between items-center">
                            <span class="text-[11px] text-[#6F70A2] font-bold">Status</span>
                            <span class="font-bold text-amber-400 text-[11px]">Pending Verification</span>
                        </div>
                    </div>
                    
                    <button type="button" onclick="closeModal('modal-hutang')" class="w-full py-3 bg-[#201335] text-white text-[13px] font-bold rounded-xl border border-white/10 hover:bg-white/10 transition-colors">
                        Selesai
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Dynamic Curved SVG on Left Sidebar
        function updateCurve() {
            const path = document.getElementById('sidebar-path');
            const discordLogo = document.getElementById('discord-logo');
            const firstServer = document.getElementById('first-server');
            const plusIcon = document.getElementById('plus-icon');
            const sidebar = document.getElementById('sidebar-container');
            
            if (!discordLogo || !firstServer || !plusIcon || !path || !sidebar) return;

            const sidebarRect = sidebar.getBoundingClientRect();
            const dRect = discordLogo.getBoundingClientRect();
            const fRect = firstServer.getBoundingClientRect();
            const pRect = plusIcon.getBoundingClientRect();
            
            const rightX = 72;
            const leftX = 2;

            const curve1Top = (dRect.bottom - sidebarRect.top) + 2;
            const curve1Bottom = (fRect.top - sidebarRect.top) - 2;
            const distance1 = curve1Bottom - curve1Top;
            const cp1 = distance1 * 0.5; 

            const curve2Top = (pRect.bottom - sidebarRect.top) + 12;
            const curve2Bottom = curve2Top + 60;
            const distance2 = curve2Bottom - curve2Top;
            const cp2 = distance2 * 0.5;

            const d = `
                M ${rightX}, 0
                L ${rightX}, ${curve1Top}
                C ${rightX}, ${curve1Top + cp1} ${leftX}, ${curve1Bottom - cp1} ${leftX}, ${curve1Bottom}
                L ${leftX}, ${curve2Top}
                C ${leftX}, ${curve2Top + cp2} ${rightX}, ${curve2Bottom - cp2} ${rightX}, ${curve2Bottom}
                L ${rightX}, 2000
            `;
            
            path.setAttribute('d', d);
        }

        window.addEventListener('load', updateCurve);
        window.addEventListener('resize', updateCurve);

        // Modal BagiRata Handlers
        const modalEl = document.getElementById('modal-hutang');
        const modalSteps = ['scan', 'input', 'payment', 'receipt'];

        function openModal(id) {
            const target = document.getElementById(id);
            if (!target) return;
            target.classList.add('active');
            switchStep('scan'); 
        }

        function closeModal(id) {
            const target = document.getElementById(id);
            if (target) target.classList.remove('active');
        }

        function switchStep(stepId) {
            modalSteps.forEach(s => {
                const el = document.getElementById('step-' + s);
                if (el) el.classList.add('hidden');
            });
            const activeEl = document.getElementById('step-' + stepId);
            if (activeEl) activeEl.classList.remove('hidden');
        }

        function simulateWebhook() {
            const btnText = document.getElementById('btn-pay-text');
            const btnIcon = document.getElementById('btn-pay-icon');
            if (!btnText || !btnIcon) return;
            
            btnText.innerText = 'Memproses...';
            btnIcon.classList.replace('ph-paper-plane-tilt', 'ph-spinner');
            btnIcon.classList.add('animate-spin');
            
            setTimeout(() => {
                btnText.innerText = 'Kirim Pembayaran';
                btnIcon.classList.replace('ph-spinner', 'ph-paper-plane-tilt');
                btnIcon.classList.remove('animate-spin');
                switchStep('receipt');
            }, 1200);
        }

        if (modalEl) {
            modalEl.addEventListener('click', (e) => {
                if (e.target === modalEl) closeModal('modal-hutang');
            });
        }
    </script>

    @stack('scripts')
</body>
</html>
