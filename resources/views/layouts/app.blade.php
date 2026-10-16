<!DOCTYPE html>
<html lang="id" class="dark scroll-smooth">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'BagiRata') · Solusi Patungan & Split Bill Tanpa Drama</title>
  <link rel="icon" type="image/png" href="{{ asset('image/logo.png') }}">

  <!-- Theme Pre-Initialization to prevent flicker -->
  <script>
    (function() {
      const savedTheme = localStorage.getItem('theme');
      if (savedTheme === 'light') {
        document.documentElement.classList.remove('dark');
        document.documentElement.classList.add('light');
      } else {
        document.documentElement.classList.add('dark');
        document.documentElement.classList.remove('light');
      }
    })();
  </script>

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
@php
  $activeRoute = request()->route()?->getName() ?? '';
  $isLanding = request()->routeIs('landing') || request()->is('/');
  $isAuthPage = request()->routeIs('login', 'register') || request()->is('login') || request()->is('register');
  $noSidebar = $isLanding || $isAuthPage;
  $isAuth = auth()->check();
  $user = auth()->user();
  $userInitials = $isAuth ? strtoupper(substr($user->name, 0, 2)) : 'BR';
  $userTag = $isAuth ? ($user->username ? '@' . $user->username : '#' . str_pad($user->id, 4, '0', STR_PAD_LEFT)) : '@tamu';
  $allGroups = $isAuth ? $user->groups : collect([]);
@endphp
<body class="{{ $noSidebar ? 'min-h-screen w-full bg-theme-root text-theme-main selection:bg-[#FF2A85]/30 selection:text-white overflow-x-hidden flex flex-col' : 'overflow-hidden flex h-screen w-screen bg-theme-root text-theme-main selection:bg-[#FF2A85]/30 selection:text-white' }}"
   x-data="{ mobileNav: false, rightSidebarOpen: false }">

  @if ($noSidebar)
    <!-- Top Header (Full-Width, Tanpa Sidebar) -->
    <header class="w-full h-16 sm:h-20 bg-theme-sidebar2/90 backdrop-blur-md border-b border-theme-border sticky top-0 z-50">
      <div class="max-w-7xl mx-auto h-full px-4 sm:px-6 lg:px-8 flex items-center justify-between">
        <a href="{{ url('/') }}" class="flex items-center gap-3">
          <img src="{{ asset('image/logo.png') }}" alt="BagiRata" class="w-8 h-8 sm:w-9 sm:h-9 object-contain">
          <span class="text-lg sm:text-xl font-black tracking-tight text-theme-main">BagiRata</span>
        </a>

        @if ($isLanding)
          <nav class="hidden md:flex items-center gap-7 text-xs sm:text-sm font-bold text-theme-muted">
            <a href="#fitur" class="hover:text-theme-main transition">Fitur</a>
            <a href="#kas-grup" class="hover:text-theme-main transition">Kas Grup</a>
            <a href="#itemized" class="hover:text-theme-main transition">Hitung Pajak</a>
            <a href="#debt-simplification" class="hover:text-theme-main transition">Pangkas Utang</a>
            <a href="{{ route('calculator.index') }}" class="hover:text-[#FF2A85] transition flex items-center gap-1.5">
              <i class="ph-bold ph-calculator text-base text-[#FF2A85]"></i>
              <span>Kalkulator</span>
            </a>
            <a href="#faq" class="hover:text-theme-main transition">FAQ</a>
          </nav>
        @endif

        <div class="flex items-center gap-3">
          <!-- Theme Toggle Switcher -->
          <button type="button"
              onclick="toggleTheme()"
              class="p-2 rounded-xl bg-theme-elevated text-theme-muted hover:text-theme-main border border-theme-border transition shadow-sm"
              title="Ganti Mode Terang / Gelap">
            <i class="ph-bold ph-sun text-base dark:hidden text-amber-500"></i>
            <i class="ph-bold ph-moon text-base hidden dark:inline text-[#A373D3]"></i>
          </button>

          @auth
            <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-xl bg-[#5D549C] hover:bg-[#4e4587] text-white text-xs sm:text-sm font-bold transition shadow-md">
              Buka Dashboard
            </a>
          @else
            @if (request()->routeIs('login') || request()->is('login'))
              <a href="{{ route('register', ['redirect' => request('redirect')]) }}" class="px-4 py-1.5 sm:px-5 sm:py-2 rounded-xl bg-[#5D549C] hover:bg-[#4e4587] text-white text-xs sm:text-sm font-bold transition shadow-md">
                Daftar Akun
              </a>
            @elseif (request()->routeIs('register') || request()->is('register'))
              <a href="{{ route('login', ['redirect' => request('redirect')]) }}" class="px-3.5 py-1.5 sm:px-4 sm:py-2 rounded-xl bg-theme-elevated hover:bg-theme-elevated/80 text-theme-main text-xs sm:text-sm font-bold transition border border-theme-border">
                Masuk
              </a>
            @else
              <a href="{{ route('login') }}" class="px-3.5 py-1.5 sm:px-4 sm:py-2 rounded-xl bg-theme-elevated hover:bg-theme-elevated/80 text-theme-main text-xs sm:text-sm font-bold transition border border-theme-border">
                Masuk
              </a>
              <a href="{{ route('register') }}" class="px-4 py-1.5 sm:px-5 sm:py-2 rounded-xl bg-[#5D549C] hover:bg-[#4e4587] text-white text-xs sm:text-sm font-bold transition shadow-md">
                Daftar
              </a>
            @endif
          @endauth
        </div>
      </div>
    </header>

    <!-- Full-Width Content Container -->
    <main class="w-full flex-1">
      @yield('content')
    </main>
  @else
    <!-- Mobile Nav Backdrop -->
    <div x-show="mobileNav"
       x-cloak
       @click="mobileNav = false"
       class="fixed inset-0 z-40 bg-black/70 backdrop-blur-sm lg:hidden">
    </div>

    <!-- 1. LEFT SIDEBAR CONTAINER -->
    <aside id="sidebar-container"
        :class="mobileNav ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
        class="fixed inset-y-0 left-0 z-50 flex h-full w-[310px] sm:w-[340px] max-w-[88vw] bg-theme-sidebar2 border-r border-theme-border transition-transform duration-300 ease-in-out lg:relative lg:translate-x-0 shrink-0">

    <!-- Dynamic SVG Background with Smooth Inward Curve (Gambar 1, Tanpa Neon, Bersih & Mulus) -->
    <svg id="svg-bg" class="absolute top-0 left-0 w-full h-full z-0 pointer-events-none overflow-visible">
      <path id="sidebar-bg-path" d="" fill="var(--theme-bg-sidebar1)" />
      <path id="sidebar-path" d="" stroke="var(--theme-border-curve)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none" />
    </svg>

    <!-- 1A. Leftmost Server Column (76px) -->
    <div class="w-[76px] h-full flex flex-col items-center relative z-10 shrink-0 bg-transparent">
      
      <!-- BagiRata Home Logo Button (Pinned at top, tidak ikut scroll) -->
      @php 
        $isHomeActive = request()->routeIs('dashboard', 'landing') || request()->is('/');
      @endphp
      <div class="pt-5 pb-10 shrink-0 flex justify-center w-full">
        <a href="{{ $isAuth ? route('dashboard') : url('/') }}"
          id="discord-logo"
          title="Dashboard Akun & Explore BagiRata"
          class="server-icon {{ $isHomeActive && !request()->is('groups*') ? 'active' : '' }} w-12 h-12 rounded-2xl bg-[#5D549C] flex items-center justify-center text-white shadow-md hover:bg-[#4e4587] transition-all shrink-0">
          <img src="{{ asset('image/logo.png') }}" alt="BagiRata" class="w-8 h-8 object-contain brightness-0 invert">
        </a>
      </div>

      <!-- DAFTAR SEMUA GROUP USER (Inner Scrollable Server List ala Discord) -->
      <div id="server-scroll-container" class="flex-1 w-full overflow-y-auto space-y-3 flex flex-col items-center no-scrollbar pt-3 pb-4">
        @if ($isAuth && $allGroups->isNotEmpty())
          @foreach ($allGroups as $index => $grp)
            @php 
              $isGrpActive = request()->is('groups/' . $grp->id) || request()->is('groups/' . $grp->id . '/*');
            @endphp
            <a href="{{ route('groups.show', $grp) }}"
              @if ($index === 0) id="first-server" @endif
              title="{{ $grp->name }}"
              class="server-icon {{ $isGrpActive ? 'active' : '' }} w-12 h-12 rounded-2xl {{ $isGrpActive ? 'bg-gradient-to-tr from-[#5D549C] to-[#7E3A63] text-white shadow-md border-transparent' : 'bg-theme-elevated text-theme-main dark:text-theme-muted border-theme-border hover:border-[#A373D3]/50' }} flex items-center justify-center cursor-pointer border shrink-0">
              @if ($grp->avatar_path)
                <img src="{{ asset('storage/' . $grp->avatar_path) }}" alt="{{ $grp->name }}" class="w-full h-full object-cover rounded-2xl">
              @else
                <span class="font-black text-sm {{ $isGrpActive ? 'text-white' : 'text-theme-main dark:text-theme-muted' }}">
                  {{ strtoupper(substr($grp->name, 0, 2)) }}
                </span>
              @endif
            </a>
          @endforeach
        @else
          <a href="{{ route('groups.create') }}"
            id="first-server"
            title="Buat Circle Pertama"
            class="server-icon w-12 h-12 rounded-2xl bg-gradient-to-tr from-[#5D549C] to-[#7E3A63] flex items-center justify-center cursor-pointer shrink-0 shadow-md">
            <i class="ph-bold ph-users-three text-white text-xl"></i>
          </a>
        @endif

        <!-- Plus Icon (Buat Group Baru) -->
        <a href="{{ route('groups.create') }}"
          id="plus-icon"
          title="Buat Circle / Group Baru"
          class="server-icon w-12 h-12 rounded-2xl bg-theme-elevated flex items-center justify-center cursor-pointer text-[#EB9998] hover:bg-[#5D549C] hover:text-white transition-all border border-dashed border-theme-border hover:border-transparent shrink-0">
          <i class="fa-solid fa-plus text-base"></i>
        </a>
      </div>

      <!-- Bottom Pinned Spacer matching user profile bar -->
      <div class="h-[64px] shrink-0 w-full"></div>
    </div>

    <!-- 1B. Main Sidebar (Channels / Explore Menu) -->
    <div class="flex-1 h-full flex flex-col relative z-10 overflow-hidden bg-theme-sidebar2">
      
      {{-- Dynamic Content: jika di dalam group tampilkan channels/menu group, jika umum tampilkan explore menu --}}
      @hasSection('sidebar')
        <div class="flex-1 overflow-y-auto px-3.5 pt-5 pb-4 space-y-3 custom-scrollbar">
          @yield('sidebar')
        </div>
      @elseif (isset($group) && (request()->is('groups*') || request()->routeIs('groups.*')))
        <div class="flex-1 overflow-y-auto px-3.5 pt-2 pb-4 space-y-3 custom-scrollbar">
          @include('groups._sidebar', ['group' => $group])
        </div>
      @else
        <!-- Default Explore Navigation (Balanced top spacing, never cramped) -->
        <div class="px-5 pt-6 pb-4 relative z-10 flex items-center justify-between">
          <div>
            <h1 class="text-lg font-black text-theme-main tracking-tight flex items-center gap-2">
              <span>Explore</span>
              <span class="h-2 w-2 rounded-full bg-[#EB9998]"></span>
            </h1>
            <span class="text-[10px] uppercase font-bold tracking-wider text-theme-muted">BagiRata Workspace</span>
          </div>
          <button @click="mobileNav = false" class="lg:hidden text-theme-muted hover:text-theme-main p-1">
            <i class="ph-bold ph-x text-lg"></i>
          </button>
        </div>

        <div class="flex-1 overflow-y-auto px-3.5 pt-4 pb-4 space-y-2 custom-scrollbar">
          <!-- Home / Dashboard -->
          <a href="{{ $isAuth ? route('dashboard') : url('/') }}"
            class="nav-link flex items-center gap-3 px-3.5 py-2.5 rounded-2xl transition-all {{ ($activeRoute === 'dashboard' || request()->is('/')) ? 'nav-link-active' : '' }}">
            <i class="ph-fill ph-compass text-lg w-5 text-center text-[#A373D3]"></i>
            <span class="font-bold text-[13px]">Home</span>
          </a>

          <div class="pt-4 pb-1.5 px-3.5 text-[10px] font-black text-theme-subtle tracking-wider uppercase">Menu Circle</div>
          
          <!-- Groups -->
          <a href="{{ route('groups.index') }}"
            class="nav-link flex items-center gap-3 px-3.5 py-2.5 rounded-2xl transition-all {{ str_starts_with($activeRoute, 'groups') ? 'nav-link-active' : '' }}">
            <i class="ph ph-users-three text-lg w-5 text-center text-[#5D549C]"></i>
            <span class="font-bold text-[13px]">Circle Tongkrongan</span>
          </a>
          
          <!-- Nongkrong -->
          <a href="{{ route('nongkrong.index') }}"
            class="nav-link flex items-center gap-3 px-3.5 py-2.5 rounded-2xl transition-all {{ str_starts_with($activeRoute, 'nongkrong') ? 'nav-link-active' : '' }}">
            <i class="ph ph-coffee text-lg w-5 text-center text-[#EB9998]"></i>
            <span class="font-bold text-[13px]">Sesi Nongki</span>
          </a>
          
          <!-- Kalkulator -->
          <a href="{{ route('calculator.index') }}"
            class="nav-link flex items-center gap-3 px-3.5 py-2.5 rounded-2xl transition-all {{ str_starts_with($activeRoute, 'calculator') ? 'nav-link-active' : '' }}">
            <i class="ph ph-calculator text-lg w-5 text-center text-[#A373D3]"></i>
            <span class="font-bold text-[13px]">Kalkulator Cepat</span>
          </a>
          
          <!-- Budgeting -->
          <a href="{{ route('budgets.index') }}"
            class="nav-link flex items-center gap-3 px-3.5 py-2.5 rounded-2xl transition-all {{ str_starts_with($activeRoute, 'budgets') ? 'nav-link-active' : '' }}">
            <i class="ph ph-chart-pie-slice text-lg w-5 text-center text-[#5D549C]"></i>
            <span class="font-bold text-[13px]">Budgeting Anti Boncos</span>
          </a>

          <!-- Utang-Piutang -->
          <div class="nav-link flex items-center justify-between px-3.5 py-2.5 rounded-2xl cursor-pointer transition-all {{ str_starts_with($activeRoute, 'debts') ? 'nav-link-active' : '' }}">
            <a href="{{ route('debts.overview') }}" class="flex items-center gap-3 flex-1">
              <i class="ph-fill ph-hand-coins text-lg text-[#EB9998] w-5 text-center"></i>
              <span class="font-bold text-[13px]">Buku Utang</span>
            </a>
            <button type="button"
                onclick="openModal('modal-hutang')"
                title="Buka Scanner / Form Pembayaran"
                class="p-1 rounded-lg hover:bg-white/10 text-[#EB9998]">
              <div class="w-2.5 h-2.5 rounded-full bg-[#EB9998]"></div>
            </button>
          </div>

          <!-- Teman -->
          <a href="{{ route('friends.index') }}"
            class="nav-link flex items-center gap-3 px-3.5 py-2.5 rounded-2xl transition-all {{ str_starts_with($activeRoute, 'friends') ? 'nav-link-active' : '' }}">
            <i class="ph ph-user-list text-lg w-5 text-center text-[#A373D3]"></i>
            <span class="font-bold text-[13px]">Circle Teman</span>
          </a>
        </div>
      @endif

      <!-- Bottom Voice & User Section -->
      <div class="mt-auto px-4 pb-3 pt-2.5 relative bg-theme-sidebar2 border-t border-theme-border shrink-0">
        
        <!-- Audio Frequency Equalizer -->
        <div class="audio-bars mb-2.5 opacity-80" title="Audio Activity Status">
          <div class="bar"></div>
          <div class="bar"></div>
          <div class="bar"></div>
          <div class="bar"></div>
          <div class="bar"></div>
          <div class="bar"></div>
          <div class="bar"></div>
        </div>

        <!-- User Profile & Controls -->
        <div class="flex items-center justify-between relative z-10 border-t border-theme-border/40 pt-2.5">
          @if ($isAuth)
            <a href="{{ route('profile.show') }}" class="flex items-center gap-2.5 cursor-pointer group hover:bg-theme-elevated p-1 -ml-1 rounded-xl transition-colors flex-1 min-w-0 mr-1" title="Buka Profil">
              <div class="relative shrink-0">
                @if ($user->avatar_path)
                  <img src="{{ asset('storage/' . $user->avatar_path) }}" alt="{{ $user->name }}" class="w-8 h-8 rounded-full object-cover ring-2 ring-[#5D549C]/30">
                @else
                  <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-[#5D549C] to-[#7E3A63] flex items-center justify-center text-xs font-bold text-white shadow-sm">
                    {{ $userInitials }}
                  </div>
                @endif
                <div class="absolute bottom-0 right-0 w-2 h-2 bg-emerald-500 rounded-full border border-theme-sidebar2"></div>
              </div>
              <div class="flex flex-col min-w-0">
                <span class="text-xs font-bold text-theme-main leading-tight truncate group-hover:text-[#A373D3] transition-colors">{{ $user->name }}</span>
                <span class="text-[10px] text-theme-muted truncate">{{ $userTag }}</span>
              </div>
            </a>

            <div class="flex items-center gap-0.5 text-theme-muted shrink-0">
              <button type="button" class="w-7 h-7 flex items-center justify-center rounded-lg hover:text-theme-main hover:bg-theme-elevated transition" title="Mikrofon"><i class="fa-solid fa-microphone text-xs"></i></button>
              <button type="button" class="w-7 h-7 flex items-center justify-center rounded-lg hover:text-theme-main hover:bg-theme-elevated transition" title="Headset"><i class="fa-solid fa-headphones text-xs"></i></button>
              
              <!-- User Settings Button (Menggantikan tombol logout langsung) -->
              <a href="{{ route('profile.edit') }}" class="w-7 h-7 flex items-center justify-center rounded-lg hover:text-[#A373D3] hover:bg-theme-elevated transition" title="User Settings / Setelan Pengguna">
                <i class="fa-solid fa-gear text-xs"></i>
              </a>

              <!-- Theme Toggle in User Control Bar -->
              <button type="button" onclick="toggleTheme()" class="w-7 h-7 flex items-center justify-center rounded-lg hover:text-theme-main hover:bg-theme-elevated transition" title="Ganti Mode Terang / Gelap">
                <i class="ph-bold ph-sun text-xs dark:hidden text-amber-500"></i>
                <i class="ph-bold ph-moon text-xs hidden dark:inline text-[#A373D3]"></i>
              </button>
            </div>
          @else
            <div class="flex items-center justify-between w-full gap-2">
              <a href="{{ route('login') }}" class="flex-1 text-center py-2 text-xs font-bold rounded-xl bg-theme-elevated hover:bg-theme-elevated/80 text-theme-main transition border border-theme-border">Masuk</a>
              <a href="{{ route('register') }}" class="flex-1 text-center py-2 text-xs font-bold rounded-xl bg-[#5D549C] hover:bg-[#4e4587] text-white transition shadow-sm">Daftar</a>
              <button type="button" onclick="toggleTheme()" class="w-8 h-8 flex items-center justify-center rounded-xl bg-theme-elevated hover:bg-theme-card transition border border-theme-border text-theme-muted hover:text-theme-main shrink-0" title="Ganti Mode Terang / Gelap">
                <i class="ph-bold ph-sun text-xs dark:hidden text-amber-500"></i>
                <i class="ph-bold ph-moon text-xs hidden dark:inline text-[#A373D3]"></i>
              </button>
            </div>
          @endif
        </div>
      </div>
    </div>
  </aside>

  <!-- 2. CENTER MAIN CONTENT AREA -->
  <main class="flex-1 bg-theme-root h-full flex flex-col relative overflow-hidden z-0">
    
    <!-- Ambient Blur Blobs with GTA 6 Twilight Colors (Hanya di background!) -->
    <div class="absolute -top-[10%] -left-[10%] w-[45%] h-[45%] bg-[#FF2A85]/8 blur-[160px] rounded-full pointer-events-none"></div>
    <div class="absolute top-[30%] -right-[15%] w-[40%] h-[40%] bg-[#8B5CF6]/8 blur-[160px] rounded-full pointer-events-none"></div>
    <div class="absolute -bottom-[10%] left-[25%] w-[40%] h-[40%] bg-[#FF7A00]/6 blur-[160px] rounded-full pointer-events-none"></div>

    <!-- Header Bar -->
    <header class="h-[64px] flex items-center justify-between px-3 sm:px-6 border-b border-theme-border bg-theme-sidebar2/80 shrink-0 relative z-20 backdrop-blur-md">
      <div class="flex items-center gap-2 sm:gap-3 min-w-0">
        <!-- Mobile Menu Button -->
        <button type="button"
            @click="mobileNav = !mobileNav"
            class="p-2 rounded-xl text-theme-muted hover:text-theme-main hover:bg-theme-elevated lg:hidden shrink-0">
          <i class="ph-bold ph-list text-xl"></i>
        </button>

        @hasSection('header-left')
          @yield('header-left')
        @else
          <!-- Search Input -->
          <div class="w-36 xs:w-52 sm:w-80 md:w-96 relative">
            <i class="ph ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-theme-subtle text-xs sm:text-sm"></i>
            <input type="text"
                placeholder="Cari circle, bill..."
                class="w-full bg-theme-elevated border border-theme-border rounded-2xl py-1.5 sm:py-2 pl-8 sm:pl-10 pr-3 sm:pr-4 text-xs sm:text-[13px] text-theme-main focus:outline-none focus:border-[#5D549C] focus:ring-1 focus:ring-[#5D549C]/30 transition-all placeholder:text-theme-subtle shadow-inner">
          </div>
        @endif
      </div>

      <div class="flex items-center gap-2 sm:gap-3">
        @if ($isAuth)
          <a href="{{ route('nongkrong.create') }}" class="hidden sm:inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-theme-elevated hover:bg-theme-elevated/80 text-theme-main text-xs font-bold border border-theme-border transition">
            <i class="ph-bold ph-plus text-xs text-[#EB9998]"></i>
            <span>Nongki</span>
          </a>
          <button type="button"
              onclick="openModal('modal-hutang')"
              class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-[#5D549C] hover:bg-[#4e4587] text-white text-xs font-bold shadow-sm transition">
            <i class="ph-bold ph-qr-code text-sm"></i>
            <span class="hidden sm:inline">Scan / Tagih</span>
          </button>
        @else
          <a href="{{ route('login') }}" class="px-3.5 py-1.5 rounded-xl bg-[#5D549C] hover:bg-[#4e4587] text-white text-xs font-bold shadow-sm transition">
            Masuk
          </a>
        @endif

        <!-- Toggle Right Sidebar Button (Info Channel & Anggota - Persis Discord!) -->
        <button type="button"
            @click="rightSidebarOpen = !rightSidebarOpen"
            :class="rightSidebarOpen ? 'text-[#EB9998] bg-theme-elevated shadow-inner' : 'text-theme-muted hover:text-theme-main hover:bg-theme-elevated'"
            class="p-2 rounded-xl transition"
            title="Info Channel & Anggota Tongkrongan">
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

  <!-- Mobile Right Sidebar Backdrop -->
  <div x-show="rightSidebarOpen"
     x-cloak
     @click="rightSidebarOpen = false"
     class="fixed inset-0 z-40 bg-black/60 backdrop-blur-sm lg:hidden">
  </div>

  <!-- 3. RIGHT SIDEBAR CONTAINER (Default: TERSEMBUNYI, muncul saat info channel/anggota diklik!) -->
  <aside x-show="rightSidebarOpen"
      x-cloak
      x-transition:enter="transition ease-out duration-200"
      x-transition:enter-start="translate-x-full opacity-0"
      x-transition:enter-end="translate-x-0 opacity-100"
      x-transition:leave="transition ease-in duration-150"
      x-transition:leave-start="translate-x-0 opacity-100"
      x-transition:leave-end="translate-x-full opacity-0"
      class="fixed inset-y-0 right-0 z-50 w-[290px] sm:w-[320px] max-w-[85vw] h-full bg-theme-sidebar2 flex flex-col pt-5 p-5 shrink-0 overflow-y-auto border-l border-theme-border shadow-[-10px_0_30px_rgba(0,0,0,0.15)] custom-scrollbar">
    
    <!-- Header Panel with Close Button -->
    <div class="flex items-center justify-between pb-3 border-b border-theme-border mb-4">
      <span class="text-xs font-bold uppercase tracking-wider text-theme-subtle flex items-center gap-1.5">
        <i class="ph-bold ph-info text-sm text-[#EB9998]"></i>
        <span>Info & Anggota</span>
      </span>
      <button type="button" @click="rightSidebarOpen = false" class="p-1 rounded-lg hover:bg-theme-elevated text-theme-muted hover:text-theme-main transition">
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
            <img src="{{ asset('storage/' . $user->avatar_path) }}" alt="{{ $user->name }}" class="relative z-20 w-[95px] h-[95px] rounded-full object-cover border-4 border-theme-sidebar2">
          @else
            <div class="relative z-20 w-[95px] h-[95px] bg-gradient-to-tr from-[#5D549C] to-[#7E3A63] rounded-full flex items-center justify-center text-3xl font-black text-white border-4 border-theme-sidebar2 shadow-xl">
              {{ $userInitials }}
            </div>
          @endif
          <div class="absolute bottom-2 right-2 w-4 h-4 bg-emerald-500 rounded-full border-[3px] border-theme-sidebar2 z-30" title="Online"></div>
        </div>
        
        <h2 class="text-[17px] font-bold text-theme-main tracking-wide text-center">
          {{ $isAuth ? $user->name : 'BagiRata Guest' }}
        </h2>
        <p class="text-[12px] text-theme-muted mt-0.5 font-medium">
          {{ $userTag }}
        </p>
      </div>

      <!-- Top Circles -->
      <div class="mb-6">
        <div class="flex items-center justify-between mb-3">
          <h3 class="text-[11px] font-bold text-theme-subtle uppercase tracking-widest">Daftar Circle</h3>
          <a href="{{ route('groups.index') }}" class="text-[11px] font-medium text-[#A373D3] hover:underline">Semua</a>
        </div>
        
        <div class="space-y-2">
          @if ($isAuth && $allGroups->isNotEmpty())
            @foreach ($allGroups->take(5) as $g)
              <a href="{{ route('groups.show', $g) }}" class="rounded-xl p-2.5 flex items-center gap-3 cursor-pointer bg-theme-elevated hover:bg-theme-card transition-colors border border-theme-border">
                <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-[#5D549C] to-[#7E3A63] flex items-center justify-center font-bold text-white text-xs shrink-0 shadow-sm">
                  {{ strtoupper(substr($g->name, 0, 2)) }}
                </div>
                <div class="min-w-0 flex-1">
                  <div class="text-[12px] font-bold text-theme-main truncate">{{ $g->name }}</div>
                  <div class="text-[10px] text-theme-muted">{{ $g->members_count ?? $g->members->count() }} Anggota</div>
                </div>
              </a>
            @endforeach
          @else
            <p class="text-xs text-theme-muted italic text-center py-4">Belum ada circle.</p>
          @endif
        </div>
      </div>
    @endif
  </aside>
  @endif

  <!-- 4. MODAL UTANG-PIUTANG (DISCORD INTERACTIVE MULTI-STEP) -->
  <div id="modal-hutang" class="fixed inset-0 z-50 flex items-center justify-center p-4 modal-overlay bg-black/60 backdrop-blur-md">
    <div class="modal-content w-full max-w-sm bg-theme-card backdrop-blur-2xl rounded-2xl border border-theme-border-strong shadow-2xl relative overflow-hidden text-theme-main">
      
      <button type="button" onclick="closeModal('modal-hutang')" class="absolute top-4 right-4 text-theme-muted hover:text-theme-main z-20 w-8 h-8 rounded-full bg-theme-elevated flex items-center justify-center transition">
        <i class="ph-bold ph-x"></i>
      </button>

      <div class="relative z-10">
        <!-- STEP 1: SCAN -->
        <div id="step-scan" class="p-7">
          <div class="text-center mb-6">
            <div class="w-14 h-14 rounded-2xl bg-[#5D549C]/20 border border-[#A373D3]/40 flex items-center justify-center text-[#A373D3] mx-auto mb-3 shadow-inner">
              <i class="ph-fill ph-qr-code text-3xl"></i>
            </div>
            <h2 class="text-lg font-bold text-theme-main">Scan QR Rekan</h2>
            <p class="text-[12px] text-theme-muted mt-0.5">Arahkan kamera untuk mencatat hutang</p>
          </div>
          <div class="relative w-full aspect-square bg-theme-elevated rounded-2xl border-2 border-dashed border-theme-border flex flex-col items-center justify-center cursor-pointer group hover:border-[#5D549C] transition" onclick="switchStep('input')">
            <div class="absolute top-0 left-0 w-full h-1 bg-[#EB9998] shadow-[0_0_20px_#EB9998] animate-[scan_2s_ease-in-out_infinite]"></div>
            <i class="ph ph-scan text-5xl text-theme-subtle group-hover:text-[#5D549C] transition-colors"></i>
            <p class="text-[11px] text-theme-muted mt-3">Klik simulasi scan ke <span class="font-bold text-[#EB9998]">Reza</span></p>
          </div>
          <style>@keyframes scan { 0%{top:0;opacity:0} 10%{opacity:1} 90%{opacity:1} 100%{top:100%;opacity:0} }</style>
        </div>

        <!-- STEP 2: INPUT -->
        <div id="step-input" class="p-7 hidden">
          <div class="text-center mb-6">
            <div class="w-12 h-12 rounded-full bg-[#5D549C] mx-auto mb-3 flex items-center justify-center font-bold text-white shadow-md">RZ</div>
            <h2 class="text-lg font-bold text-theme-main">Catat Tagihan</h2>
            <p class="text-[12px] text-theme-muted">Kepada <span class="text-[#EB9998] font-bold">Rzene</span></p>
          </div>
          <div class="space-y-4">
            <div>
              <input type="number" value="50000" class="w-full bg-theme-elevated border border-theme-border rounded-xl py-3.5 px-4 text-xl font-bold text-theme-main focus:outline-none focus:border-[#5D549C] text-center shadow-inner">
            </div>
            <button type="button" onclick="switchStep('payment')" class="pushable w-full">
              <span class="shadow"></span>
              <span class="edge"></span>
              <span class="front w-full py-3.5">
                Lanjut Pembayaran
              </span>
            </button>
          </div>
        </div>

        <!-- STEP 3: BAYAR -->
        <div id="step-payment" class="p-7 hidden">
          <div class="bg-theme-elevated border border-theme-border rounded-xl p-4 mb-4 text-center mt-2">
            <p class="text-[10px] text-theme-subtle font-bold uppercase tracking-wider mb-1">Transfer Rek BCA</p>
            <p class="text-xl font-mono font-bold text-[#5D549C] dark:text-[#A373D3]">872 123 9988</p>
            <p class="text-[11px] text-theme-muted mt-0.5">a.n Rzene</p>
          </div>
          
          <div class="w-full h-24 bg-theme-elevated/70 border border-dashed border-theme-border rounded-xl flex flex-col items-center justify-center text-theme-muted hover:text-[#5D549C] hover:border-[#5D549C] cursor-pointer mb-5 transition" onclick="this.innerHTML='<i class=\'ph-fill ph-check-circle text-emerald-500 text-2xl mb-1\'></i><span class=\'text-[11px] font-bold text-emerald-500\'>Bukti diunggah</span>'; this.classList.add('border-emerald-500/40', 'bg-emerald-500/10')">
            <i class="ph ph-image text-2xl mb-1.5"></i>
            <span class="text-[11px] font-bold uppercase tracking-wide">Upload Bukti Transfer</span>
          </div>
          
          <button type="button" onclick="simulateWebhook()" class="pushable w-full">
            <span class="shadow"></span>
            <span class="edge"></span>
            <span class="front w-full py-3.5">
              <span id="btn-pay-text">Kirim Pembayaran</span>
              <i id="btn-pay-icon" class="ph-bold ph-paper-plane-tilt text-lg"></i>
            </span>
          </button>
        </div>

        <!-- STEP 4: STRUK -->
        <div id="step-receipt" class="p-7 hidden text-center">
          <div class="w-14 h-14 bg-emerald-500/20 rounded-full flex items-center justify-center mx-auto mb-4 border border-emerald-500/30">
            <i class="ph-bold ph-check text-2xl text-emerald-500"></i>
          </div>
          <h2 class="text-lg font-bold text-theme-main mb-4">Pembayaran Sukses!</h2>
          
          <div class="bg-theme-elevated border-l-[3px] border-[#EB9998] rounded-r-lg p-4 mb-5 text-left relative">
            <div class="flex items-center gap-2 mb-2">
              <i class="ph-fill ph-robot text-[#EB9998] text-base"></i>
              <span class="text-[11px] font-bold text-theme-muted">BagiRata Webhook</span>
            </div>
            <h4 class="font-bold text-theme-main text-[13px] mb-1">Receipt Generated</h4>
            <p class="text-[11px] text-theme-muted mb-3">Menunggu konfirmasi final dari @Rzene.</p>
            <div class="bg-theme-card p-2.5 rounded-lg border border-theme-border flex justify-between items-center">
              <span class="text-[11px] text-theme-muted font-bold">Status</span>
              <span class="font-bold text-amber-500 text-[11px]">Pending Verification</span>
            </div>
          </div>
          
          <button type="button" onclick="closeModal('modal-hutang')" class="w-full py-3 bg-theme-elevated text-theme-main text-[13px] font-bold rounded-xl border border-theme-border hover:bg-theme-card transition-colors">
            Selesai
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Scripts -->
  <script>
    // Theme Switcher Function (Mode Terang & Gelap di Setiap Halaman)
    window.toggleTheme = function() {
      const html = document.documentElement;
      const isDark = html.classList.contains('dark');
      if (isDark) {
        html.classList.remove('dark');
        html.classList.add('light');
        localStorage.setItem('theme', 'light');
      } else {
        html.classList.remove('light');
        html.classList.add('dark');
        localStorage.setItem('theme', 'dark');
      }
      if (typeof updateCurve === 'function') {
        setTimeout(updateCurve, 50);
      }
    };

    // Dynamic Organic Inward-Curved Line on Left Sidebar (Persis Gambar 1, Mulus & Bebas Tabrakan)
    function updateCurve() {
      const path = document.getElementById('sidebar-path');
      const discordLogo = document.getElementById('discord-logo');
      const plusIcon = document.getElementById('plus-icon');
      const sidebar = document.getElementById('sidebar-container');
      const scrollContainer = document.getElementById('server-scroll-container');
      
      if (!path || !sidebar) return;

      const sidebarRect = sidebar.getBoundingClientRect();
      const h = sidebar.clientHeight || window.innerHeight;
      
      const rightX = 76; // Border alami kolom server (76px)
      const leftX = 2;   // Lengkungan ke dalam di sisi kiri (12px di kiri icon server)

      let logoBottom = 64;
      if (discordLogo) {
        logoBottom = discordLogo.getBoundingClientRect().bottom - sidebarRect.top;
      }
      
      // Posisi container server bersifat statis terhadap sidebar shell (tidak bergoyang saat scroll)
      let containerTop = logoBottom + 32;
      if (scrollContainer) {
        containerTop = scrollContainer.getBoundingClientRect().top - sidebarRect.top;
      }

      // 1. Lengkungan ke dalam (Inward Curve: dari rightX=76 ke leftX=2)
      // Diberikan jarak aman agar garis tidak terlalu dekat dengan logo (curve1Top dimulai di bawah logo)
      // Dan selesai di containerTop sebelum tombol server pertama dimulai.
      const curve1Top = Math.max(0, logoBottom + 4);
      const curve1Bottom = Math.max(curve1Top + 34, containerTop);
      const dist1 = curve1Bottom - curve1Top;
      const cp1 = dist1 * 0.46;

      // 2. Outward curve: HANYA jika server sedikit dan plusIcon berada aman di atas area profil bawah
      // Jika server banyak (scrollContainer bisa di-scroll atau server panjang),
      // garis tetap lurus di leftX=2 sampai bawah agar TIDAK PERNAH memotong atau menabrak icon manapun!
      const isScrollable = scrollContainer ? (scrollContainer.scrollHeight > scrollContainer.clientHeight + 10) : false;

      let pBottom = containerTop + 140;
      if (plusIcon) {
        pBottom = plusIcon.getBoundingClientRect().bottom - sidebarRect.top;
      }

      const canCurveOut = !isScrollable && ((pBottom + 16 + 56) < (h - 75));

      let d;
      let bgD;

      if (canCurveOut) {
        const curve2Top = pBottom + 16;
        const dist2 = 56;
        const curve2Bottom = curve2Top + dist2;
        const cp2 = dist2 * 0.46;

        d = `
          M ${rightX}, 0
          L ${rightX}, ${curve1Top}
          C ${rightX}, ${curve1Top + cp1} ${leftX}, ${curve1Bottom - cp1} ${leftX}, ${curve1Bottom}
          L ${leftX}, ${curve2Top}
          C ${leftX}, ${curve2Top + cp2} ${rightX}, ${curve2Bottom - cp2} ${rightX}, ${curve2Bottom}
          L ${rightX}, ${h + 200}
        `;
        bgD = `
          M 0, 0
          L ${rightX}, 0
          L ${rightX}, ${curve1Top}
          C ${rightX}, ${curve1Top + cp1} ${leftX}, ${curve1Bottom - cp1} ${leftX}, ${curve1Bottom}
          L ${leftX}, ${curve2Top}
          C ${leftX}, ${curve2Top + cp2} ${rightX}, ${curve2Bottom - cp2} ${rightX}, ${curve2Bottom}
          L ${rightX}, ${h + 200}
          L 0, ${h + 200}
          Z
        `;
      } else {
        d = `
          M ${rightX}, 0
          L ${rightX}, ${curve1Top}
          C ${rightX}, ${curve1Top + cp1} ${leftX}, ${curve1Bottom - cp1} ${leftX}, ${curve1Bottom}
          L ${leftX}, ${h + 200}
        `;
        bgD = `
          M 0, 0
          L ${rightX}, 0
          L ${rightX}, ${curve1Top}
          C ${rightX}, ${curve1Top + cp1} ${leftX}, ${curve1Bottom - cp1} ${leftX}, ${curve1Bottom}
          L ${leftX}, ${h + 200}
          L 0, ${h + 200}
          Z
        `;
      }

      path.setAttribute('d', d);

      const bgPath = document.getElementById('sidebar-bg-path');
      if (bgPath) {
        bgPath.setAttribute('d', bgD);
      }
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
