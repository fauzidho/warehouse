<!DOCTYPE html>
<html lang="id" class="h-full bg-zinc-950 text-zinc-100 antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - Gudang Half Kopi</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>
<body class="h-full bg-zinc-950 text-zinc-100 selection:bg-amber-600 selection:text-white">
    <div class="min-h-full flex flex-col lg:flex-row">
        <!-- Sidebar Desktop -->
        <aside class="w-full lg:w-72 bg-zinc-900/90 border-r border-zinc-800/80 backdrop-blur-xl flex flex-col shrink-0">
            <!-- Brand Header -->
            <div class="p-5 border-b border-zinc-800/80 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-500 via-amber-600 to-amber-700 flex items-center justify-center shadow-lg shadow-amber-900/40 border border-amber-400/30">
                        <svg class="w-6 h-6 text-zinc-950" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 8h1a4 4 0 0 1 0 8h-1M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8zM6 1v3M10 1v3M14 1v3" />
                        </svg>
                    </div>
                    <div>
                        <div class="font-extrabold text-base tracking-tight text-white flex items-center gap-1.5">
                            HALF KOPI <span class="text-xs px-1.5 py-0.5 rounded bg-amber-500/10 text-amber-400 font-semibold border border-amber-500/20">GUDANG</span>
                        </div>
                        <div class="text-[11px] text-zinc-400 font-medium">Sistem Inventori Kedai Kopi</div>
                    </div>
                </div>
            </div>

            <!-- User Status Card -->
            <div class="p-4 mx-4 my-3 rounded-xl bg-zinc-800/50 border border-zinc-700/40">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2.5 truncate">
                        <div class="w-8 h-8 rounded-lg bg-zinc-700 flex items-center justify-center font-bold text-xs text-amber-400 border border-zinc-600">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <div class="truncate">
                            <div class="text-xs font-semibold text-zinc-200 truncate">{{ auth()->user()->name }}</div>
                            <div class="text-[11px] text-zinc-400 flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                {{ auth()->user()->isAdmin() ? 'Administrator' : 'Staff / Barista' }}
                            </div>
                        </div>
                    </div>
                    <div>
                        @if(auth()->user()->isAdmin())
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/30">
                                ADMIN
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-cyan-500/10 text-cyan-400 border border-cyan-500/30">
                                PEGAWAI
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-3 py-2 space-y-6 overflow-y-auto">
                <!-- Utama -->
                <div class="space-y-1">
                    <div class="px-3 text-[10px] font-bold uppercase tracking-wider text-zinc-500">Menu Utama</div>
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('dashboard') ? 'bg-amber-600 text-white shadow-md shadow-amber-900/30 font-semibold' : 'text-zinc-400 hover:text-zinc-200 hover:bg-zinc-800/60' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('items.index') }}" class="flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('items.*') ? 'bg-amber-600 text-white shadow-md shadow-amber-900/30 font-semibold' : 'text-zinc-400 hover:text-zinc-200 hover:bg-zinc-800/60' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            <span>Bahan Baku (Katalog)</span>
                        </div>
                        @if(($globalLowStockCount ?? 0) > 0)
                            <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/20 text-rose-400 border border-rose-500/40">
                                {{ $globalLowStockCount }}
                            </span>
                        @endif
                    </a>
                </div>

                <!-- Operasional Transaksi -->
                <div class="space-y-1">
                    <div class="px-3 text-[10px] font-bold uppercase tracking-wider text-zinc-500">Operasional Stok</div>
                    <a href="{{ route('stock-in.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('stock-in.*') ? 'bg-amber-600 text-white shadow-md shadow-amber-900/30 font-semibold' : 'text-zinc-400 hover:text-zinc-200 hover:bg-zinc-800/60' }}">
                        <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                        </svg>
                        <span>Stok Masuk (Penerimaan)</span>
                    </a>
                    <a href="{{ route('stock-out.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('stock-out.*') ? 'bg-amber-600 text-white shadow-md shadow-amber-900/30 font-semibold' : 'text-zinc-400 hover:text-zinc-200 hover:bg-zinc-800/60' }}">
                        <svg class="w-4 h-4 text-amber-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                        </svg>
                        <span>Stok Keluar (Pemakaian Bar)</span>
                    </a>
                    <a href="{{ route('adjustments.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('adjustments.*') ? 'bg-amber-600 text-white shadow-md shadow-amber-900/30 font-semibold' : 'text-zinc-400 hover:text-zinc-200 hover:bg-zinc-800/60' }}">
                        <svg class="w-4 h-4 text-cyan-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                        <span>Stock Opname (Fisik)</span>
                    </a>
                </div>

                <!-- Laporan -->
                <div class="space-y-1">
                    <div class="px-3 text-[10px] font-bold uppercase tracking-wider text-zinc-500">Laporan & Audit</div>
                    <a href="{{ route('reports.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('reports.*') ? 'bg-amber-600 text-white shadow-md shadow-amber-900/30 font-semibold' : 'text-zinc-400 hover:text-zinc-200 hover:bg-zinc-800/60' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span>Laporan Mutasi & Nilai Stok</span>
                    </a>
                </div>

                <!-- Master Data (Admin Only) -->
                @if(auth()->user()->isAdmin())
                <div class="space-y-1 pt-2 border-t border-zinc-800/60">
                    <div class="px-3 text-[10px] font-bold uppercase tracking-wider text-amber-500/80 flex items-center justify-between">
                        <span>Akses Admin</span>
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                    </div>
                    <a href="{{ route('categories.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('categories.*') ? 'bg-amber-600 text-white shadow-md shadow-amber-900/30 font-semibold' : 'text-zinc-400 hover:text-zinc-200 hover:bg-zinc-800/60' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                        <span>Kategori Bahan</span>
                    </a>
                    <a href="{{ route('suppliers.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('suppliers.*') ? 'bg-amber-600 text-white shadow-md shadow-amber-900/30 font-semibold' : 'text-zinc-400 hover:text-zinc-200 hover:bg-zinc-800/60' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span>Supplier & Vendor</span>
                    </a>
                    <a href="{{ route('users.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('users.*') ? 'bg-amber-600 text-white shadow-md shadow-amber-900/30 font-semibold' : 'text-zinc-400 hover:text-zinc-200 hover:bg-zinc-800/60' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <span>Manajemen Pengguna</span>
                    </a>
                </div>
                @endif
            </nav>

            <!-- Bottom Logout -->
            <div class="p-4 border-t border-zinc-800/80">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded-lg text-xs font-semibold text-zinc-400 hover:text-rose-400 hover:bg-rose-500/10 border border-transparent hover:border-rose-500/20 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Keluar (Logout)
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 bg-zinc-950">
            <!-- Top Navbar -->
            <header class="no-print h-16 bg-zinc-900/60 border-b border-zinc-800/80 backdrop-blur-xl px-6 flex items-center justify-between sticky top-0 z-30">
                <div class="flex items-center gap-3">
                    <h1 class="text-base font-bold text-white tracking-tight">@yield('page-title', 'Dashboard')</h1>
                    <span class="text-zinc-600 hidden sm:inline">•</span>
                    <span class="text-xs text-zinc-400 hidden sm:inline">@yield('page-subtitle', 'Sistem Manajemen Gudang & Stok Bahan')</span>
                </div>

                <div class="flex items-center gap-3">
                    <!-- Shortcut Actions -->
                    <a href="{{ route('stock-in.create') }}" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500/20 border border-emerald-500/20 transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                        Stok Masuk
                    </a>
                    <a href="{{ route('stock-out.create') }}" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-amber-500/10 text-amber-400 hover:bg-amber-500/20 border border-amber-500/20 transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/>
                        </svg>
                        Pemakaian Bar
                    </a>
                    <a href="{{ route('adjustments.create') }}" class="hidden md:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-cyan-500/10 text-cyan-400 hover:bg-cyan-500/20 border border-cyan-500/20 transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Stock Opname
                    </a>

                    <!-- Status Indicator -->
                    <div class="px-3 py-1 rounded-full bg-zinc-800 text-zinc-300 text-[11px] font-medium border border-zinc-700/60 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                        <span>{{ now()->translatedFormat('d M Y') }}</span>
                    </div>
                </div>
            </header>

            <!-- Main Body Container -->
            <main class="flex-1 p-6 lg:p-8 max-w-7xl w-full mx-auto">
                <!-- Alerts / Flash Messages -->
                @if(session('success'))
                    <div class="mb-6 p-4 rounded-xl bg-emerald-950/40 border border-emerald-500/30 text-emerald-200 flex items-center justify-between shadow-lg shadow-emerald-950/20">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-emerald-500/20 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <span class="text-sm font-medium">{{ session('success') }}</span>
                        </div>
                        <button onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-emerald-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 p-4 rounded-xl bg-rose-950/40 border border-rose-500/30 text-rose-200 flex items-center justify-between shadow-lg shadow-rose-950/20">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-rose-500/20 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-rose-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                            <span class="text-sm font-medium">{{ session('error') }}</span>
                        </div>
                        <button onclick="this.parentElement.remove()" class="text-rose-400 hover:text-rose-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="mb-6 p-4 rounded-xl bg-amber-950/40 border border-amber-500/30 text-amber-200 flex items-center justify-between shadow-lg shadow-amber-950/20">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-amber-500/20 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <span class="text-sm font-medium">{{ session('warning') }}</span>
                        </div>
                        <button onclick="this.parentElement.remove()" class="text-amber-400 hover:text-amber-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                @endif

                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="no-print border-t border-zinc-800/80 py-5 px-8 text-center text-xs text-zinc-500">
                &copy; {{ date('Y') }} <strong class="text-zinc-300">Gudang Half Kopi</strong>. Sistem Inventori Bahan & Supply Chain Kedai Kopi.
            </footer>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
