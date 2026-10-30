<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Agenda Surat Masuk</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Alpine.js dan Tailwind dimuat dari aset build lokal. -->
    
    <!-- Turbo Drive (Hotwire) untuk Navigasi SPA Tanpa Refresh -->
    <script type="module">
        import * as Turbo from 'https://cdn.jsdelivr.net/npm/@hotwired/turbo@8.0.4/dist/turbo.es2017-esm.js';
    </script>
    <script>
        window.SipasConfig = {
            assetBase: @json(rtrim(asset(''), '/')),
        };
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        /* Custom Scrollbar for Sidebar */
        .sidebar-scroll::-webkit-scrollbar { width: 4px; }
        .sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
        .sidebar-scroll:hover::-webkit-scrollbar-thumb { background: #475569; }
        
        /* Custom Scrollbar for Horizontal Table */
        .table-scroll::-webkit-scrollbar { height: 12px; width: 12px; }
        .table-scroll::-webkit-scrollbar-track { background: #f8fafc; border-radius: 8px; }
        .table-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 8px; border: 3px solid #f8fafc; }
        .table-scroll::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up {
            animation: fadeInUp 0.4s ease-out forwards;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen selection:bg-indigo-100 selection:text-indigo-900" x-data="{ sidebarOpen: false }">
    <!-- Overlay for mobile -->
    <div x-show="sidebarOpen" 
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false"
         class="fixed inset-0 z-30 bg-slate-900/60 backdrop-blur-sm lg:hidden" style="display: none;"></div>

    <!-- Sidebar -->
    <aside :class="{ 'sidebar-open': sidebarOpen }" class="sipas-sidebar fixed inset-y-0 left-0 z-40 flex w-60 flex-col bg-[#0B1120] shadow-2xl transition-transform duration-300 ease-in-out border-r border-slate-800/50">
        <!-- Logo Area -->
        <div class="h-20 flex items-center justify-between px-6 border-b border-slate-800/50">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-cyan-500 shadow-lg shadow-indigo-500/30">
                    <svg class="h-6 w-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                    </svg>
                </div>
                <div>
                    <span class="text-xl font-bold tracking-wide bg-clip-text text-transparent bg-gradient-to-r from-indigo-400 to-cyan-400">SIPAS</span>
                    <p class="text-[11px] text-slate-400 font-medium uppercase tracking-wider">Agenda Digital</p>
                </div>
            </div>
            <button type="button" @click="sidebarOpen = false" class="rounded-lg p-2 text-slate-400 hover:bg-slate-800 hover:text-white transition-colors lg:hidden" aria-label="Tutup menu">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 pt-6 px-4 space-y-1 overflow-y-auto sidebar-scroll pb-4">
            <p class="px-2 text-xs font-semibold text-slate-500 uppercase tracking-widest mb-2 mt-2">Menu Utama</p>
            
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'bg-indigo-500/10 text-indigo-400 border-indigo-500' : 'text-slate-400 hover:bg-slate-800/50 hover:text-slate-200 border-transparent' }} group flex items-center gap-3 px-3 py-3 text-sm font-medium rounded-xl border-l-2 transition-all duration-200 relative overflow-hidden">
                <svg class="h-5 w-5 {{ request()->routeIs('dashboard') ? 'text-indigo-400' : 'text-slate-500 group-hover:text-slate-300' }} transition-colors" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.5 12 4l9 9.5M5 11.5V20h14v-8.5" />
                </svg>
                Dashboard
            </a>

            <a href="{{ route('surat-masuk.index') }}" class="{{ request()->routeIs('surat-masuk.*') ? 'bg-indigo-500/10 text-indigo-400 border-indigo-500' : 'text-slate-400 hover:bg-slate-800/50 hover:text-slate-200 border-transparent' }} group flex items-center gap-3 px-3 py-3 text-sm font-medium rounded-xl border-l-2 transition-all duration-200">
                <svg class="h-5 w-5 {{ request()->routeIs('surat-masuk.*') ? 'text-indigo-400' : 'text-slate-500 group-hover:text-slate-300' }} transition-colors" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25v9A2.25 2.25 0 0 1 18.75 19.5H5.25A2.25 2.25 0 0 1 3 17.25v-9m18 0A2.25 2.25 0 0 0 18.75 6H5.25A2.25 2.25 0 0 0 3 8.25m18 0-9 5.25L3 8.25" />
                </svg>
                Surat Masuk
            </a>

            <a href="{{ route('surat-keluar.index') }}" class="{{ request()->routeIs('surat-keluar.*') ? 'bg-indigo-500/10 text-indigo-400 border-indigo-500' : 'text-slate-400 hover:bg-slate-800/50 hover:text-slate-200 border-transparent' }} group flex items-center gap-3 px-3 py-3 text-sm font-medium rounded-xl border-l-2 transition-all duration-200">
                <svg class="h-5 w-5 {{ request()->routeIs('surat-keluar.*') ? 'text-indigo-400' : 'text-slate-500 group-hover:text-slate-300' }} transition-colors" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5h18M7 11.5h10M7 15.5h6M5 20h14a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2Z" />
                </svg>
                Surat Keluar
            </a>

            <p class="px-2 text-xs font-semibold text-slate-500 uppercase tracking-widest mb-2 mt-6">Akun</p>

            <a href="{{ route('profile') }}" class="{{ request()->routeIs('profile') ? 'bg-indigo-500/10 text-indigo-400 border-indigo-500' : 'text-slate-400 hover:bg-slate-800/50 hover:text-slate-200 border-transparent' }} group flex items-center gap-3 px-3 py-3 text-sm font-medium rounded-xl border-l-2 transition-all duration-200">
                <svg class="h-5 w-5 {{ request()->routeIs('profile') ? 'text-indigo-400' : 'text-slate-500 group-hover:text-slate-300' }} transition-colors" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                </svg>
                Profil Akun
            </a>
        </nav>

        <!-- Logout Area -->
        <div class="p-4 border-t border-slate-800/50 bg-[#0B1120]/50 backdrop-blur-sm">
            <!-- Menambahkan data-turbo="false" agar form submit diproses normal -->
            <form action="{{ route('logout') ?? '#' }}" method="POST" data-turbo="false">
                @csrf
                <button type="submit" class="group flex w-full items-center justify-center gap-2 rounded-xl bg-slate-800/80 px-4 py-3 text-sm font-semibold text-slate-300 hover:bg-rose-500/10 hover:text-rose-400 transition-all duration-300 border border-slate-700/50 hover:border-rose-500/30">
                    <svg class="h-5 w-5 transition-transform group-hover:-translate-x-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                    </svg>
                    Keluar Sistem
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Wrapper -->
    <div class="lg:pl-60 flex flex-col min-h-screen">
        <!-- Glassmorphism Navbar -->
        <header class="sticky top-0 z-20 bg-white/70 backdrop-blur-lg shadow-sm border-b border-slate-200/60 h-20 flex items-center px-4 sm:px-6 lg:px-8 justify-between transition-all duration-300">
            <div class="flex items-center gap-4">
                <button type="button" @click="sidebarOpen = true" class="rounded-xl p-2.5 text-slate-500 hover:bg-slate-100/80 hover:text-slate-800 transition-colors lg:hidden ring-1 ring-slate-200" aria-label="Buka menu">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <div>
                    <h1 class="text-lg sm:text-xl font-bold text-slate-800 tracking-tight">Manajemen Dokumen & Arsip</h1>
                    <p class="text-xs sm:text-sm text-slate-500 font-medium hidden sm:block">Kelola surat masuk dengan mudah dan rapi</p>
                </div>
            </div>
            
            <div class="flex items-center gap-4">
                <!-- User Profile Dropdown -->
                <div class="flex items-center gap-3">
                    <div class="hidden sm:flex flex-col items-end">
                        <span class="text-sm font-bold text-slate-800">{{ auth()->user()->name ?? 'Administrator' }}</span>
                        <span class="text-xs font-medium text-slate-500">Admin Kepegawaian</span>
                    </div>
                    <a href="{{ route('profile') }}" title="Profil Akun">
                        <img class="h-10 w-10 rounded-full object-cover ring-2 ring-slate-100 shadow-sm hover:ring-indigo-400 transition-all" src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'Admin') }}&background=6366f1&color=fff&bold=true" alt="User avatar">
                    </a>
                </div>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="flex-1 px-4 py-8 sm:px-6 lg:px-8 w-full max-w-7xl mx-auto">
            <!-- Alerts with modern design -->
            @if(session('success'))
                <div class="mb-8 rounded-xl bg-emerald-50 border border-emerald-200 p-4 shadow-sm flex items-start gap-3 transform transition-all hover:scale-[1.01]">
                    <div class="text-emerald-500 mt-0.5">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-emerald-800">Berhasil!</h3>
                        <p class="text-sm text-emerald-700 mt-1">{{ session('success') }}</p>
                    </div>
                </div>
            @endif
            
            @if(session('warning'))
                <div class="mb-8 rounded-xl bg-amber-50 border border-amber-200 p-4 shadow-sm flex items-start gap-3 transform transition-all hover:scale-[1.01]">
                    <div class="text-amber-500 mt-0.5">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-amber-800">Perhatian</h3>
                        <p class="text-sm text-amber-700 mt-1">{{ session('warning') }}</p>
                    </div>
                </div>
            @endif
            
            @if($errors->any())
                <div class="mb-8 rounded-xl bg-rose-50 border border-rose-200 p-4 shadow-sm flex items-start gap-3">
                    <div class="text-rose-500 mt-0.5">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-rose-800">Terdapat Kesalahan</h3>
                        <ul class="mt-2 list-disc pl-5 text-sm text-rose-700 space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <div class="animate-fade-in-up">
                @yield('content')
            </div>
        </main>
    </div>
    @stack('scripts')
</body>
</html>
