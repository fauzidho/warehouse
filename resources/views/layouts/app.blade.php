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
            background: #f5ede4 !important;
            color: #26150b !important;
        }

        /* ─── ROOT LAYOUT ─── */
        .layout {
            display: flex;
            height: 100vh;
            height: 100dvh;
            overflow: hidden;
            width: 100%;
            position: relative;
            background: #f5ede4 !important;
        }

        /* ─── SIDEBAR (RICH ESPRESSO COFFEE) ─── */
        #sidebar {
            width: 264px;
            min-width: 264px;
            height: 100vh;
            background: #2a180d !important;
            border-right: 1px solid rgba(140, 100, 72, 0.35);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            flex-shrink: 0;
            transition: margin-left 0.25s ease;
        }

        /* DESKTOP: Collapsed mode (Sidebar menutup halus tanpa menarik/memotong konten) */
        @media (min-width: 1024px) {
            body.sidebar-collapsed #sidebar {
                margin-left: -264px !important;
                border-right-color: transparent !important;
                pointer-events: none;
            }
        }

        /* ─── TOMBOL TOGGLE DI TENGAH-TENGAH PERBATASAN SIDEBAR & LAMAN UTAMA ─── */
        .sidebar-edge-toggle {
            position: fixed;
            top: 50% !important;
            left: 250px; /* tepat di garis perbatasan 264px */
            transform: translateY(-50%) !important;
            width: 28px;
            height: 52px;
            border-radius: 8px;
            background: #ffffff !important;
            border: 1px solid #c8a88a !important;
            color: #b45309 !important;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 300;
            box-shadow: 0 4px 16px rgba(60, 35, 18, 0.15) !important;
            user-select: none;
            -webkit-user-select: none;
            touch-action: manipulation;
            transition: left 0.25s ease, background 0.15s, color 0.15s, border-color 0.15s;
        }

        /* Area klik diperluas agar selalu kena saat diklik */
        .sidebar-edge-toggle::before {
            content: '';
            position: absolute;
            inset: -8px -10px;
            cursor: pointer;
        }

        .sidebar-edge-toggle svg {
            pointer-events: none;
            flex-shrink: 0;
        }

        .sidebar-edge-toggle:hover {
            background: #b45309 !important;
            color: #ffffff !important;
            border-color: #b45309 !important;
            box-shadow: 0 0 20px rgba(180, 83, 9, 0.4) !important;
        }

        .sidebar-edge-toggle:active {
            background: #92400e !important;
            color: #ffffff !important;
        }

        /* KETIKA SIDEBAR MENUTUP (COLLAPSED) */
        body.sidebar-collapsed .sidebar-edge-toggle {
            left: 0px !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            width: 26px !important;
            height: 52px !important;
            border-radius: 0 10px 10px 0 !important;
            border-left: none !important;
            background: #ffffff !important;
            color: #b45309 !important;
            border: 1px solid #c8a88a !important;
            border-left: none !important;
            box-shadow: 4px 0 16px rgba(60, 35, 18, 0.15) !important;
        }

        body.sidebar-collapsed .sidebar-edge-toggle:hover {
            background: #b45309 !important;
            color: #ffffff !important;
        }

        body.sidebar-collapsed .sidebar-edge-toggle:active {
            background: #92400e !important;
            color: #ffffff !important;
        }

        @media (max-width: 1023px) {
            .sidebar-edge-toggle { display: none !important; }
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
        }

        /* ─── SIDEBAR INNER ─── */
        .sidebar-brand {
            height: 64px;
            min-height: 64px;
            max-height: 64px;
            padding: 0 1rem;
            border-bottom: 1px solid rgba(140, 100, 72, 0.35);
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            box-sizing: border-box;
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
        .sidebar-nav::-webkit-scrollbar-thumb { background: #6f4e38; border-radius: 9999px; }
        .sidebar-footer {
            padding: 0.75rem;
            border-top: 1px solid rgba(140, 100, 72, 0.35);
            flex-shrink: 0;
        }

        /* ─── NAV LINKS ─── */
        .nav-section-label {
            font-size: 9.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.09em;
            color: #b59b83;
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
            color: #e5d4c0;
            text-decoration: none;
            position: relative;
            margin-bottom: 3px;
            transition: all 0.18s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .nav-link:hover { 
            background: rgba(67, 46, 32, 0.65); 
            color: #ffffff;
            transform: translateX(2px);
        }
        .nav-link.active,
        .nav-link.active-green,
        .nav-link.active-amber,
        .nav-link.active-cyan,
        .nav-link.active-violet { 
            background: rgba(224, 164, 99, 0.16); 
            color: #f6b26b; 
            font-weight: 600; 
            box-shadow: inset 0 0 12px rgba(224, 164, 99, 0.08);
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
            background: #e0a463;
            border-radius: 0 4px 4px 0;
            box-shadow: 0 0 8px rgba(224, 164, 99, 0.6);
        }
        .nav-link svg { flex-shrink: 0; }
        .nav-link .sub { margin-left: auto; font-size: 10px; color: #b59b83; }

        /* ─── MAIN AREA (WARM SOFT BEIGE) ─── */
        .main-area {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            height: 100vh;
            height: 100dvh;
            overflow: hidden;
            background: #f5ede4 !important;
        }

        /* ─── TOPBAR (WARM CREAM BEIGE) ─── */
        .topbar {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0 1.75rem;
            height: 64px;
            min-height: 64px;
            max-height: 64px;
            background: #faf4ed !important;
            border-bottom: 1px solid #e2d3c3 !important;
            backdrop-filter: blur(12px);
            flex-shrink: 0;
            box-sizing: border-box;
        }

        /* ─── CONTENT SCROLL ─── */
        .content-scroll {
            flex: 1;
            min-height: 0;
            overflow-y: auto;
            overflow-x: auto;
            background: #f5ede4 !important;
        }
        .content-scroll::-webkit-scrollbar { width: 5px; height: 5px; }
        .content-scroll::-webkit-scrollbar-thumb { background: #c8b29d; border-radius: 9999px; }
        .content-scroll::-webkit-scrollbar-thumb:hover { background: #b09780; }

        /* ─── GLOBAL PALET COKELAT & BEIGE (TIDAK TERLALU GELAP) ─── */
        /* Kartu & Container */
        .layout main :is(
            [class~="bg-zinc-900"],
            [class~="bg-zinc-900\/70"],
            [class~="bg-zinc-900\/80"],
            [class~="bg-zinc-900\/90"],
            [class~="bg-zinc-900\/95"],
            [class~="bg-zinc-950"],
            [class~="bg-zinc-950\/40"],
            [class~="bg-zinc-950\/60"],
            .dashboard-kpi,
            .dashboard-panel,
            .dashboard-recent,
            .dashboard-guide
        ) {
            background: #ffffff !important;
            background-color: #ffffff !important;
            border-color: #e2d3c3 !important;
            box-shadow: 0 4px 18px rgba(60, 35, 18, 0.05) !important;
        }

        .layout main :is(
            [class~="border-zinc-800"],
            [class~="border-zinc-800\/40"],
            [class~="border-zinc-800\/60"],
            [class~="border-zinc-800\/80"],
            [class~="border-zinc-700"],
            [class~="border-zinc-700\/60"],
            [class~="border-zinc-700\/70"],
            [class~="border-zinc-700\/80"]
        ) {
            border-color: #e2d3c3 !important;
        }

        /* Tipografi: Deep Espresso Brown */
        .layout main :is(
            h1, h2, h3, h4, h5, h6,
            [class~="text-white"],
            td[class~="text-white"],
            th[class~="text-white"]
        ):not(.dashboard-welcome *):not(.btn *):not(button *):not(a[class*="from-"] *):not(button[class*="from-"] *):not([class*="bg-emerald-"] *):not([class*="bg-rose-"] *):not([class*="bg-amber-"] *) {
            color: #23150b !important;
        }

        .layout main :is([class~="text-zinc-100"], [class~="text-zinc-200"]):not(.dashboard-welcome *) {
            color: #2e1a0e !important;
        }
        .layout main :is([class~="text-zinc-300"], [class~="text-zinc-400"]):not(.dashboard-welcome *) {
            color: #6f523e !important;
        }
        .layout main :is([class~="text-zinc-500"]):not(.dashboard-welcome *) {
            color: #8d6d56 !important;
        }
        .layout main :is([class~="text-zinc-600"]):not(.dashboard-welcome *) {
            color: #a88972 !important;
        }

        /* Form Input & Select */
        .layout main :is(input:not([type="checkbox"]):not([type="radio"]):not([type="submit"]):not([type="button"]):not([type="reset"]):not([type="file"]), select, textarea) {
            background-color: #ffffff !important;
            border-color: #d4c2b0 !important;
            color: #23150b !important;
            box-shadow: 0 1px 3px rgba(60, 35, 18, 0.04) !important;
        }
        .layout main :is(input, select, textarea)::placeholder {
            color: #9a806d !important;
        }
        .layout main :is(input, select, textarea):focus {
            border-color: #d97706 !important;
            background-color: #fffdfa !important;
            box-shadow: 0 0 0 3px rgba(217, 119, 6, 0.2) !important;
        }

        /* Tabel */
        .layout main table {
            background-color: #ffffff !important;
        }
        .layout main table thead,
        .layout main table thead tr {
            background-color: #f7ede2 !important;
        }
        .layout main table thead th,
        .layout main table th {
            background-color: #f7ede2 !important;
            color: #6a4d37 !important;
            border-bottom: 1px solid #e2d3c3 !important;
            font-weight: 700 !important;
        }
        .layout main table tbody tr {
            background-color: #ffffff !important;
            border-bottom: 1px solid #ede3d7 !important;
        }
        .layout main table tbody tr:hover {
            background-color: #faf4ed !important;
        }
        .layout main table tbody td {
            color: #23150b !important;
            border-bottom: 1px solid #ede3d7 !important;
        }

        /* Dashboard Welcome Banner (Warm Mocha Coffee) */
        .dashboard-welcome {
            border: 1px solid rgba(217, 119, 6, 0.45) !important;
            background: linear-gradient(135deg, #3d2516 0%, #2b180d 60%, #4a2d1a 100%) !important;
            box-shadow: 0 12px 28px -6px rgba(45, 24, 14, 0.25), 0 0 20px -4px rgba(217, 119, 6, 0.12) !important;
        }
        .dashboard-welcome h2 {
            color: #ffffff !important;
        }
        .dashboard-welcome p {
            color: #eedccb !important;
        }
        .dashboard-welcome strong {
            color: #ffffff !important;
        }
        .dashboard-recent > div:first-child {
            background: #ffffff !important;
            border-bottom: 1px solid #e2d3c3 !important;
        }

        /* Tombol Sekunder / Abu-abu */
        .layout main a:where([class*="bg-zinc-800"], [class*="bg-zinc-900"]):not([class*="from-"]):not([class*="bg-emerald-"]):not([class*="bg-amber-"]):not([class*="bg-rose-"]),
        .layout main button:where([class*="bg-zinc-800"], [class*="bg-zinc-900"]):not([class*="from-"]):not([class*="bg-emerald-"]):not([class*="bg-amber-"]):not([class*="bg-rose-"]) {
            background-color: #f7ede2 !important;
            border-color: #d4c2b0 !important;
            color: #3d2617 !important;
        }
        .layout main a:where([class*="bg-zinc-800"], [class*="bg-zinc-900"]):hover,
        .layout main button:where([class*="bg-zinc-800"], [class*="bg-zinc-900"]):hover {
            background-color: #eee0d1 !important;
            color: #23150b !important;
        }

        /* Paginator */
        nav[role="navigation"] a {
            background: #ffffff !important;
            color: #4a2e1b !important;
            border: 1px solid #d4c2b0 !important;
        }
        nav[role="navigation"] a:hover {
            background: #f7ede2 !important;
            color: #23150b !important;
            border-color: #8c684d !important;
        }
        nav[role="navigation"] span[aria-current="page"] > span {
            background: #d97706 !important;
            color: #ffffff !important;
            border-color: #b45309 !important;
        }
        nav[role="navigation"] > div:first-child {
            color: #6f523e !important;
        }

        /* ─── TEXT OVERFLOW GLOBAL FIXES ─── */
        table { min-width: 0; }
        td, th {
            overflow-wrap: break-word;
            word-break: break-word;
        }

        /* ─── NATIVE DATE INPUT & CALENDAR ICON ─── */
        input[type="date"] {
            color-scheme: light !important;
        }
        input[type="date"]::-webkit-calendar-picker-indicator {
            cursor: pointer;
            filter: none !important;
            opacity: 0.75 !important;
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
            background: rgba(25, 17, 12, 0.75);
            backdrop-filter: blur(4px);
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
            .topbar { padding: 0 1rem; height: 58px; min-height: 58px; max-height: 58px; }
            .sidebar-brand { padding: 0 0.875rem; height: 58px; min-height: 58px; max-height: 58px; }
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
                    <svg style="width:20px;height:20px;" fill="none" stroke="#ffffff" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 8h1a4 4 0 0 1 0 8h-1M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8zM6 1v3M10 1v3M14 1v3"/>
                    </svg>
                </div>
                <div>
                    <div style="font-weight:800;font-size:13px;color:#fdfaf6;letter-spacing:-0.02em;display:flex;align-items:center;gap:5px;">
                        HALF KOPI
                        <span style="font-size:9px;padding:1px 5px;border-radius:4px;background:rgba(224,164,99,0.18);color:#f6b26b;border:1px solid rgba(224,164,99,0.3);font-weight:700;">WMS</span>
                    </div>
                    <div style="font-size:10px;color:#d2bca7;margin-top:1px;">Sistem Manajemen Gudang</div>
                </div>
            </div>
        </div>

        {{-- User Card --}}
        <div class="sidebar-user">
            <div style="display:flex;align-items:center;gap:0.6rem;padding:0.5rem 0.65rem;border-radius:9px;background:rgba(51,35,25,0.7);border:1px solid rgba(111,78,56,0.45);">
                <div style="width:30px;height:30px;border-radius:7px;background:rgba(224,164,99,0.18);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:12px;color:#f6b26b;flex-shrink:0;">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div style="flex:1;min-width:0;">
                    <div style="font-size:11px;font-weight:600;color:#fbf5ed;overflow-wrap:anywhere;">{{ auth()->user()->name }}</div>
                    <div style="font-size:10px;color:#d2bca7;display:flex;align-items:center;gap:3px;margin-top:1px;">
                        <span style="width:5px;height:5px;border-radius:50%;background:#34d399;display:inline-block;flex-shrink:0;"></span>
                        {{ auth()->user()->isAdmin() ? 'Administrator' : 'Staf Pegawai' }}
                    </div>
                </div>
                @if(auth()->user()->isAdmin())
                    <span style="font-size:9px;padding:2px 5px;border-radius:4px;background:rgba(224,164,99,0.15);color:#f6b26b;border:1px solid rgba(224,164,99,0.3);font-weight:700;flex-shrink:0;">ADMIN</span>
                @else
                    <span style="font-size:9px;padding:2px 5px;border-radius:4px;background:rgba(52,211,153,0.15);color:#34d399;border:1px solid rgba(52,211,153,0.3);font-weight:700;flex-shrink:0;">STAF</span>
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

    {{-- ═════════════════════════════════════════════════════════════════════
         TOMBOL TOGGLE BERSIMBOL DI TENGAH PERBATASAN SIDEBAR & LAMAN UTAMA
         ═════════════════════════════════════════════════════════════════════ --}}
    <button id="sidebarEdgeBtn" type="button" onclick="toggleSidebar(event)"
            class="sidebar-edge-toggle no-print"
            title="Tutup Sidebar"
            aria-label="Tutup / Buka Sidebar">
        <svg id="sidebarEdgeIcon" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.6" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
        </svg>
    </button>

    {{-- ═══════════════
         MAIN AREA
         ═══════════════ --}}
    <div class="main-area">

        {{-- Topbar --}}
        <header class="topbar no-print">
            {{-- Tombol Mobile Only --}}
            <button class="hamburger" onclick="openSidebar()"
                    style="display:none;padding:7px;border-radius:8px;background:none;border:none;color:#3d2617;cursor:pointer;line-height:0;flex-shrink:0;"
                    onmouseover="this.style.background='#eee0d1';this.style.color='#23150b';"
                    onmouseout="this.style.background='none';this.style.color='#3d2617';">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            <div style="flex:1;min-width:0;">
                <div style="font-size:13px;font-weight:700;color:#23150b;overflow-wrap:anywhere;">
                    @yield('page-title', 'Dashboard')
                </div>
                <div style="font-size:10px;color:#6f523e;overflow-wrap:anywhere;" class="hidden-xs">
                    @yield('page-subtitle', 'Sistem Manajemen Gudang')
                </div>
            </div>

            <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;">
                <div style="display:flex;align-items:center;gap:7px;padding:6px 14px;border-radius:9px;background:#2a180d;color:#ffffff;font-size:11.5px;font-weight:600;border:1px solid #5a3822;letter-spacing:0.01em;" class="shadow-sm">
                    <svg width="14" height="14" fill="none" stroke="#ffffff" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span id="realtimeClock" style="font-variant-numeric: tabular-nums;color:#ffffff;">-- : -- : --</span>
                </div>
            </div>
        </header>

        {{-- Flash Messages --}}
        @if(session('success') || session('error') || session('warning'))
        <div style="padding:10px 20px 0;" class="no-print flash-messages">
            @if(session('success'))
                <div id="flash-ok" style="display:flex;align-items:center;justify-content:space-between;padding:10px 14px;border-radius:10px;background:rgba(20,53,35,0.7);border:1px solid rgba(16,185,129,0.3);color:#a7f3d0;font-size:12px;font-weight:500;">
                    <div style="display:flex;align-items:center;gap:8px;">
                        <svg width="14" height="14" fill="none" stroke="#34d399" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        {{ session('success') }}
                    </div>
                    <button onclick="this.parentElement.remove()" style="background:none;border:none;color:#34d399;cursor:pointer;opacity:0.6;font-size:16px;line-height:1;padding:0 2px;">&times;</button>
                </div>
            @endif
            @if(session('error'))
                <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 14px;border-radius:10px;background:rgba(65,22,22,0.7);border:1px solid rgba(244,63,94,0.3);color:#fecdd3;font-size:12px;font-weight:500;">
                    <div style="display:flex;align-items:center;gap:8px;">
                        <svg width="14" height="14" fill="none" stroke="#f87171" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        {{ session('error') }}
                    </div>
                    <button onclick="this.parentElement.remove()" style="background:none;border:none;color:#f87171;cursor:pointer;opacity:0.6;font-size:16px;line-height:1;padding:0 2px;">&times;</button>
                </div>
            @endif
            @if(session('warning'))
                <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 14px;border-radius:10px;background:rgba(68,43,18,0.7);border:1px solid rgba(245,158,11,0.3);color:#fef3c7;font-size:12px;font-weight:500;">
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
            <footer class="no-print page-footer" style="padding:0.75rem 2rem;border-top:1px solid #e2d3c3;font-size:11px;color:#7a5c45;display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap;background:#f5ede4;">
                <div style="display:flex;align-items:center;gap:8px;">
                    <span style="font-weight:700;color:#2e1a0e;">HALF KOPI WMS</span>
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

    // Toggle Buka / Tutup Sidebar (Desktop & Mobile)
    function toggleSidebar(e) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }
        const isDesktop = window.innerWidth >= 1024;
        if (isDesktop) {
            document.body.classList.toggle('sidebar-collapsed');
            const isCollapsed = document.body.classList.contains('sidebar-collapsed');
            try { localStorage.setItem('halfkopi_sidebar_collapsed', isCollapsed ? '1' : '0'); } catch(err){}
            updateSidebarEdgeUI(isCollapsed);
        } else {
            const sidebar = document.getElementById('sidebar');
            if (sidebar.classList.contains('open')) {
                closeSidebar();
            } else {
                openSidebar();
            }
        }
    }

    function updateSidebarEdgeUI(isCollapsed) {
        const icon = document.getElementById('sidebarEdgeIcon');
        const btn = document.getElementById('sidebarEdgeBtn');
        if (!icon) return;

        if (isCollapsed) {
            // Ketika sidebar tertutup: tombol menampilkan simbol panah ke kanan (buka »)
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>';
            if (btn) btn.setAttribute('title', 'Buka Sidebar');
        } else {
            // Ketika sidebar terbuka: tombol menampilkan simbol panah ke kiri (tutup «)
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>';
            if (btn) btn.setAttribute('title', 'Tutup Sidebar');
        }
    }

    // Default: Pastikan sidebar terbuka dan pulih
    try {
        if (!sessionStorage.getItem('sidebar_restored_default')) {
            localStorage.removeItem('halfkopi_sidebar_collapsed');
            sessionStorage.setItem('sidebar_restored_default', '1');
            document.body.classList.remove('sidebar-collapsed');
        } else if (window.innerWidth >= 1024 && localStorage.getItem('halfkopi_sidebar_collapsed') === '1') {
            document.body.classList.add('sidebar-collapsed');
            document.addEventListener('DOMContentLoaded', function() {
                updateSidebarEdgeUI(true);
            });
        }
    } catch(e) {}

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
