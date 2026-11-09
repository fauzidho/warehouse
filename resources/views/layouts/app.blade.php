<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') &mdash; Gudang Half Kopi</title>
    <meta name="description" content="Sistem Manajemen Gudang & Inventori Half Kopi">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- SheetJS (Excel Parser & Exporter) -->
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        html, body {
            height: 100%;
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #0c0a09;
            color: #f4f4f5;
        }

        /* ─── ROOT LAYOUT ─── */
        .layout {
            display: flex;
            height: 100vh;
            height: 100dvh;
            overflow: hidden;
        }

        /* ─── SIDEBAR ─── */
        #sidebar {
            width: 264px;
            min-width: 264px;
            height: 100vh;
            background: #151210;
            border-right: 1px solid rgba(82,68,54,0.55);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            flex-shrink: 0;
            transition: transform 0.28s cubic-bezier(0.16,1,0.3,1);
        }

        /* MOBILE: sidebar jadi fixed overlay */
        @media (max-width: 1023px) {
            #sidebar {
                position: fixed;
                top: 0; left: 0; bottom: 0;
                z-index: 200;
                transform: translateX(-100%);
            }
            #sidebar.open { transform: translateX(0); }
            .hamburger { display: flex !important; }
        }
        @media (min-width: 1024px) {
            .hamburger { display: none !important; }
        }

        /* ─── SIDEBAR INNER ─── */
        .sidebar-brand {
            padding: 1rem;
            border-bottom: 1px solid rgba(63,63,70,0.6);
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
        }
        .sidebar-user {
            padding: 0.625rem 0.875rem;
            flex-shrink: 0;
        }
        .sidebar-nav {
            flex: 1;
            min-height: 0;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 0.5rem 0.75rem;
        }
        .sidebar-nav::-webkit-scrollbar { width: 3px; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: #3f3f46; border-radius: 9999px; }
        .sidebar-footer {
            padding: 0.75rem;
            border-top: 1px solid rgba(63,63,70,0.6);
            flex-shrink: 0;
        }

        /* ─── NAV LINKS ─── */
        .nav-section-label {
            font-size: 9.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.09em;
            color: #52525b;
            padding: 0.875rem 0.5rem 0.3rem;
        }
        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            padding: 0.5rem 0.75rem;
            border-radius: 8px;
            font-size: 12.5px;
            font-weight: 500;
            color: #a1a1aa;
            text-decoration: none;
            position: relative;
            margin-bottom: 3px;
            transition: all 0.18s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .nav-link:hover { 
            background: rgba(39, 39, 42, 0.75); 
            color: #ffffff;
            transform: translateX(2px);
        }
        .nav-link.active,
        .nav-link.active-green,
        .nav-link.active-amber,
        .nav-link.active-cyan,
        .nav-link.active-violet { 
            background: rgba(217, 119, 6, 0.14); 
            color: #fbbf24; 
            font-weight: 600; 
            box-shadow: inset 0 0 12px rgba(245, 158, 11, 0.05);
        }
        .nav-link.active::before,
        .nav-link.active-green::before,
        .nav-link.active-amber::before,
        .nav-link.active-cyan::before,
        .nav-link.active-violet::before {
            content: '';
            position: absolute;
            left: 0; top: 20%;
            bottom: 20%;
            width: 3px;
            background: #f59e0b;
            border-radius: 0 4px 4px 0;
            box-shadow: 0 0 8px rgba(245, 158, 11, 0.6);
        }
        .nav-link svg { flex-shrink: 0; }
        .nav-link .sub { margin-left: auto; font-size: 10px; color: #71717a; }

        /* ─── MAIN AREA ─── */
        .main-area {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            height: 100vh;
            height: 100dvh;
            overflow: hidden;
        }

        /* ─── TOPBAR ─── */
        .topbar {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0 1.75rem;
            height: auto;
            min-height: 64px;
            padding-block: 0.6rem;
            background: rgba(21,18,16,0.92);
            border-bottom: 1px solid rgba(82,68,54,0.55);
            backdrop-filter: blur(12px);
            flex-shrink: 0;
        }

        /* ─── CONTENT SCROLL ─── */
        .content-scroll {
            flex: 1;
            min-height: 0;
            overflow-y: auto;
            overflow-x: auto;
        }
        .content-scroll::-webkit-scrollbar { width: 4px; height: 4px; }
        .content-scroll::-webkit-scrollbar-thumb { background: #3f3f46; border-radius: 9999px; }

        /* ─── TEXT OVERFLOW GLOBAL FIXES ─── */
        table { min-width: 0; }
        td, th {
            overflow-wrap: break-word;
            word-break: break-word;
        }
        /* Kolom tabel dengan teks panjang */
        .td-truncate {
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        /* Semua teks yang melebihi container */
        .text-clamp-1 {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .text-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        /* Dashboard KPI &mdash; nilai Rupiah agar tidak melebar */
        .dashboard-kpi .text-lg,
        .dashboard-kpi .text-2xl {
            word-break: break-all;
            overflow-wrap: anywhere;
        }

        /* ─── OVERLAY (mobile) ─── */
        #overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.6);
            backdrop-filter: blur(3px);
            z-index: 199;
        }

        /* ─── PAGE ANIMATION ─── */
        .page-body { animation: fadeUp 0.18s ease-out; }        @keyframes fadeUp {
            from { opacity:0; transform:translateY(5px); }
            to   { opacity:1; transform:translateY(0); }
        }

        /* ─── PRINT ─── */
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; color: black !important; }
            .layout { display: block; height: auto; overflow: visible; }
            #sidebar { display: none !important; }
            .main-area { height: auto; overflow: visible; }
            .content-scroll { overflow: visible; }
        }

        @media (max-width: 767px) {
            .topbar { padding: 0.6rem 1rem; height: auto; min-height: 58px; }
            .sidebar-brand { padding: 0.875rem; }
            .page-content { padding: 1rem !important; }
            .page-footer { padding: 0.75rem 1rem !important; flex-direction: column; align-items: flex-start !important; gap: 0.25rem !important; }
            .flash-messages { padding: 0.75rem 1rem 0 !important; }
            .hidden-xs { display: none !important; }
            /* Tabel responsive di mobile */
            .overflow-x-auto { -webkit-overflow-scrolling: touch; }
        }
    </style>
</head>
<body>

{{-- Mobile Overlay --}}
<div id="overlay" onclick="closeSidebar()"></div>

<div class="layout">

    {{-- ═══════════════════════════
         SIDEBAR (ONE, not doubled)
         ═══════════════════════════ --}}
    <aside id="sidebar">

        {{-- Brand --}}
        <div class="sidebar-brand">
            <div style="display:flex;align-items:center;gap:0.75rem;">
                <div style="width:38px;height:38px;border-radius:10px;background:linear-gradient(135deg,#f59e0b,#d97706);display:flex;align-items:center;justify-content:center;box-shadow:0 4px 16px rgba(217,119,6,0.3);flex-shrink:0;">
                    <svg style="width:20px;height:20px;" fill="none" stroke="#09090b" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 8h1a4 4 0 0 1 0 8h-1M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8zM6 1v3M10 1v3M14 1v3"/>
                    </svg>
                </div>
                <div>
                    <div style="font-weight:800;font-size:13px;color:#fff;letter-spacing:-0.02em;display:flex;align-items:center;gap:5px;">
                        HALF KOPI
                        <span style="font-size:9px;padding:1px 5px;border-radius:4px;background:rgba(245,158,11,0.12);color:#f59e0b;border:1px solid rgba(245,158,11,0.2);font-weight:700;">WMS</span>
                    </div>
                    <div style="font-size:10px;color:#71717a;margin-top:1px;">Sistem Manajemen Gudang</div>
                </div>
            </div>
            <button class="hamburger" onclick="closeSidebar()"
                    style="display:none;padding:5px;border-radius:7px;background:none;border:none;color:#71717a;cursor:pointer;line-height:0;">
                <svg style="width:17px;height:17px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- User Card --}}
        <div class="sidebar-user">
            <div style="display:flex;align-items:center;gap:0.6rem;padding:0.5rem 0.65rem;border-radius:9px;background:rgba(39,39,42,0.45);border:1px solid rgba(63,63,70,0.3);">
                <div style="width:30px;height:30px;border-radius:7px;background:rgba(245,158,11,0.14);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:12px;color:#f59e0b;flex-shrink:0;">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div style="flex:1;min-width:0;">
                    <div style="font-size:11px;font-weight:600;color:#e4e4e7;overflow-wrap:anywhere;">{{ auth()->user()->name }}</div>
                    <div style="font-size:10px;color:#71717a;display:flex;align-items:center;gap:3px;margin-top:1px;">
                        <span style="width:5px;height:5px;border-radius:50%;background:#34d399;display:inline-block;flex-shrink:0;"></span>
                        {{ auth()->user()->isAdmin() ? 'Administrator' : 'Staf Pegawai' }}
                    </div>
                </div>
                @if(auth()->user()->isAdmin())
                    <span style="font-size:9px;padding:2px 5px;border-radius:4px;background:rgba(245,158,11,0.1);color:#f59e0b;border:1px solid rgba(245,158,11,0.2);font-weight:700;flex-shrink:0;">ADMIN</span>
                @else
                    <span style="font-size:9px;padding:2px 5px;border-radius:4px;background:rgba(52,211,153,0.1);color:#34d399;border:1px solid rgba(52,211,153,0.2);font-weight:700;flex-shrink:0;">STAF</span>
                @endif
            </div>
        </div>

        {{-- Navigation (SCROLLABLE) --}}
        <nav class="sidebar-nav">

            <div class="nav-section-label">Menu Utama</div>

            <a href="{{ route('dashboard') }}" onclick="if(window.innerWidth<1024)closeSidebar()"
               class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Dasbor
            </a>

            <a href="{{ route('items.index') }}" onclick="if(window.innerWidth<1024)closeSidebar()"
               class="nav-link {{ request()->routeIs('items.*') ? 'active' : '' }}"
               style="justify-content:space-between;">
                <span style="display:flex;align-items:center;gap:0.6rem;">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    Katalog Bahan Baku
                </span>
                @if(($globalLowStockCount ?? 0) > 0)
                    <span style="font-size:9px;font-weight:700;padding:1px 6px;border-radius:9999px;background:rgba(244,63,94,0.15);color:#fb7185;border:1px solid rgba(244,63,94,0.3);">{{ $globalLowStockCount }} Restock</span>
                @endif
            </a>

            <div class="nav-section-label">Operasional Gudang</div>

            <a href="{{ route('stock-in.index') }}" onclick="if(window.innerWidth<1024)closeSidebar()"
               class="nav-link {{ request()->routeIs('stock-in.*') ? 'active' : '' }}" style="justify-content:space-between;">
                <span style="display:flex;align-items:center;gap:0.6rem;">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                    Stok Masuk
                </span>
                <span class="sub">Penerimaan</span>
            </a>

            <a href="{{ route('stock-out.index') }}" onclick="if(window.innerWidth<1024)closeSidebar()"
               class="nav-link {{ request()->routeIs('stock-out.*') ? 'active' : '' }}" style="justify-content:space-between;">
                <span style="display:flex;align-items:center;gap:0.6rem;">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                    Stok Keluar
                </span>
                <span class="sub">Pemakaian</span>
            </a>

            <a href="{{ route('adjustments.index') }}" onclick="if(window.innerWidth<1024)closeSidebar()"
               class="nav-link {{ request()->routeIs('adjustments.*') ? 'active' : '' }}" style="justify-content:space-between;">
                <span style="display:flex;align-items:center;gap:0.6rem;">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    Opname Stok
                </span>
                <span class="sub">Hitung Fisik</span>
            </a>

            <div class="nav-section-label">Laporan & Rekap</div>

            <a href="{{ route('reports.index') }}" onclick="if(window.innerWidth<1024)closeSidebar()"
               class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Laporan Mutasi Stok
            </a>

            @if(auth()->user()->isAdmin())
            <div class="nav-section-label" style="color:rgba(217,119,6,0.65);border-top:1px solid rgba(63,63,70,0.4);padding-top:0.875rem;margin-top:0.5rem;">Pengaturan Admin</div>

            <a href="{{ route('categories.index') }}" onclick="if(window.innerWidth<1024)closeSidebar()"
               class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                Kategori Bahan
            </a>

            <a href="{{ route('suppliers.index') }}" onclick="if(window.innerWidth<1024)closeSidebar()"
               class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Pemasok (Supplier)
            </a>

            <a href="{{ route('users.index') }}" onclick="if(window.innerWidth<1024)closeSidebar()"
               class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                Kelola Pengguna
            </a>
            @endif

        </nav>

        {{-- Logout --}}
        <div class="sidebar-footer">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                        style="width:100%;display:flex;align-items:center;justify-content:center;gap:0.5rem;padding:0.5rem;border-radius:8px;font-size:12px;font-weight:600;color:#71717a;background:none;border:1px solid transparent;cursor:pointer;transition:all 0.15s;"
                        onmouseover="this.style.color='#f87171';this.style.background='rgba(239,68,68,0.07)';this.style.borderColor='rgba(239,68,68,0.15)';"
                        onmouseout="this.style.color='#71717a';this.style.background='none';this.style.borderColor='transparent';">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Keluar dari Sistem
                </button>
            </form>
        </div>
    </aside>

    {{-- ═══════════════
         MAIN AREA
         ═══════════════ --}}
    <div class="main-area">

        {{-- Topbar --}}
        <header class="topbar no-print">
            <button class="hamburger" onclick="openSidebar()"
                    style="display:none;padding:7px;border-radius:8px;background:none;border:none;color:#a1a1aa;cursor:pointer;line-height:0;flex-shrink:0;"
                    onmouseover="this.style.background='rgba(39,39,42,0.8)';this.style.color='#e4e4e7';"
                    onmouseout="this.style.background='none';this.style.color='#a1a1aa';">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            <div style="flex:1;min-width:0;">
                <div style="font-size:13px;font-weight:700;color:#fff;overflow-wrap:anywhere;">
                    @yield('page-title', 'Dashboard')
                </div>
                <div style="font-size:10px;color:#71717a;overflow-wrap:anywhere;" class="hidden-xs">
                    @yield('page-subtitle', 'Sistem Manajemen Gudang')
                </div>
            </div>

            <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;">
                <div style="display:flex;align-items:center;gap:7px;padding:6px 14px;border-radius:9px;background:rgba(24,24,27,0.7);color:#e4e4e7;font-size:11.5px;font-weight:600;border:1px solid rgba(255,255,255,0.08);letter-spacing:0.01em;" class="shadow-sm">
                    <svg width="14" height="14" fill="none" stroke="#f59e0b" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span id="realtimeClock" style="font-variant-numeric: tabular-nums;">-- : -- : --</span>
                </div>
            </div>
        </header>

        {{-- Flash Messages --}}
        @if(session('success') || session('error') || session('warning'))
        <div style="padding:10px 20px 0;" class="no-print flash-messages">
            @if(session('success'))
                <div id="flash-ok" style="display:flex;align-items:center;justify-content:space-between;padding:10px 14px;border-radius:10px;background:rgba(5,46,22,0.6);border:1px solid rgba(16,185,129,0.2);color:#6ee7b7;font-size:12px;font-weight:500;">
                    <div style="display:flex;align-items:center;gap:8px;">
                        <svg width="14" height="14" fill="none" stroke="#34d399" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        {{ session('success') }}
                    </div>
                    <button onclick="this.parentElement.remove()" style="background:none;border:none;color:#34d399;cursor:pointer;opacity:0.6;font-size:16px;line-height:1;padding:0 2px;">&times;</button>
                </div>
            @endif
            @if(session('error'))
                <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 14px;border-radius:10px;background:rgba(76,5,25,0.6);border:1px solid rgba(244,63,94,0.2);color:#fca5a5;font-size:12px;font-weight:500;">
                    <div style="display:flex;align-items:center;gap:8px;">
                        <svg width="14" height="14" fill="none" stroke="#f87171" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        {{ session('error') }}
                    </div>
                    <button onclick="this.parentElement.remove()" style="background:none;border:none;color:#f87171;cursor:pointer;opacity:0.6;font-size:16px;line-height:1;padding:0 2px;">&times;</button>
                </div>
            @endif
            @if(session('warning'))
                <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 14px;border-radius:10px;background:rgba(69,26,3,0.6);border:1px solid rgba(245,158,11,0.2);color:#fde68a;font-size:12px;font-weight:500;">
                    <div style="display:flex;align-items:center;gap:8px;">
                        <svg width="14" height="14" fill="none" stroke="#f59e0b" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        {{ session('warning') }}
                    </div>
                    <button onclick="this.parentElement.remove()" style="background:none;border:none;color:#f59e0b;cursor:pointer;opacity:0.6;font-size:16px;line-height:1;padding:0 2px;">&times;</button>
                </div>
            @endif
        </div>
        @endif

        {{-- Page Content --}}
        <div class="content-scroll">
            <main class="page-body page-content" style="padding:1.75rem 2rem;max-width:1440px;width:100%;margin:0 auto;">
                @yield('content')
            </main>
            <footer class="no-print page-footer" style="padding:0.75rem 2rem;border-top:1px solid rgba(82,68,54,0.45);font-size:11px;color:#71717a;display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap;">
                <div style="display:flex;align-items:center;gap:8px;">
                    <span style="font-weight:700;color:#71717a;">HALF KOPI WMS</span>
                    <span>&bull;</span>
                    <span>Sistem Manajemen Inventori Gudang</span>
                </div>
                <div style="display:flex;align-items:center;gap:8px;">
                    <span style="display:flex;align-items:center;gap:4px;"><span style="width:6px;height:6px;border-radius:50%;background:#34d399;display:inline-block;"></span>Sistem Aktif</span>
                    <span>&bull;</span>
                    <span>&copy; {{ date('Y') }} Half Kopi Enterprise</span>
                </div>
            </footer>
        </div>

    </div>{{-- end .main-area --}}

</div>{{-- end .layout --}}

<style>
    @media (max-width: 767px) {
        .btn-desktop { display: none !important; }
    }
</style>

<script>
    function openSidebar() {
        document.getElementById('sidebar').classList.add('open');
        document.getElementById('overlay').style.display = 'block';
        document.body.style.overflow = 'hidden';
    }
    function closeSidebar() {
        document.getElementById('sidebar').classList.remove('open');
        document.getElementById('overlay').style.display = 'none';
        document.body.style.overflow = '';
    }

    // Auto-dismiss success flash after 5s
    setTimeout(function() {
        var el = document.getElementById('flash-ok');
        if (el) { el.style.transition='opacity 0.4s'; el.style.opacity='0'; setTimeout(function(){el.remove();},400); }
    }, 5000);

    // Realtime Clock
    function updateRealtimeClock() {
        const el = document.getElementById('realtimeClock');
        if (!el) return;
        const now = new Date();
        const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        const day = days[now.getDay()];
        const date = String(now.getDate()).padStart(2, '0');
        const month = months[now.getMonth()];
        const year = now.getFullYear();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        el.textContent = `${day}, ${date} ${month} ${year} • ${hours}:${minutes}:${seconds} WIB`;
    }
    updateRealtimeClock();
    setInterval(updateRealtimeClock, 1000);
</script>

@stack('scripts')
</body>
</html>
