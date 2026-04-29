<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AXIOM — @yield('title', 'Portal')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --navy:    #17171f;
            --sidebar: #0c0c0f;
            --active:  #3d3f5e;
            --accent:  #a8a4e0;
            --bg:      #e8e8ec;
            --card:    #ffffff;
            --text:    #1a1a2e;
            --muted:   #8884a8;
        }

        body { font-family: 'Outfit', sans-serif; background: var(--bg); color: var(--text); min-height: 100vh; }

        /* ── NAVBAR ── */
        .navbar {
            position: fixed; top: 0; left: 0; right: 0; height: 64px;
            background: var(--navy); display: flex; align-items: center;
            padding: 0 24px; z-index: 100; gap: 16px;
        }
        .nav-brand { display: flex; align-items: center; gap: 10px; text-decoration: none; flex-shrink: 0; }
        .nav-brand-name { font-size: 1rem; font-weight: 700; color: #c8c5f0; letter-spacing: .08em; }
        .nav-divider    { color: #4a4a6a; font-size: 1.1rem; margin: 0 6px; }
        .nav-subtitle   { font-size: .65rem; font-weight: 400; color: #8884a8; letter-spacing: .12em; text-transform: uppercase; }

        .nav-search { flex: 1; max-width: 340px; margin: 0 auto; position: relative; }
        .nav-search input {
            width: 100%; background: #f0eeff; border: none; border-radius: 24px;
            padding: 9px 18px 9px 42px; font-family: 'Outfit', sans-serif;
            font-size: .88rem; color: var(--text); outline: none;
        }
        .nav-search input::placeholder { color: var(--muted); }
        .nav-search-icon {
            position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
            color: var(--muted); pointer-events: none;
        }
        .nav-search-icon svg { width: 16px; height: 16px; }

        .nav-right { display: flex; align-items: center; gap: 18px; margin-left: auto; }
        .nav-icon-btn {
            background: none; border: none; cursor: pointer; color: #a8a4e0;
            display: flex; align-items: center; padding: 4px; border-radius: 50%;
            transition: background .2s;
        }
        .nav-icon-btn:hover { background: rgba(168,164,224,.15); }
        .nav-icon-btn svg { width: 24px; height: 24px; }

        .nav-user {
            display: flex; align-items: center; gap: 10px; color: #c8c5f0;
            font-size: .88rem; font-weight: 500; cursor: pointer; position: relative;
        }
        .nav-user svg { width: 32px; height: 32px; color: #a8a4e0; }
        .nav-user-dropdown {
            position: absolute; top: calc(100% + 12px); right: 0;
            background: var(--navy); border: 1px solid #3a3a5a; border-radius: 10px;
            padding: 6px 0; min-width: 150px; display: none; z-index: 200;
            box-shadow: 0 8px 24px rgba(0,0,0,.3);
        }
        .nav-user:hover .nav-user-dropdown,
        .nav-user:focus-within .nav-user-dropdown { display: block; }
        .nav-user-dropdown a, .nav-user-dropdown button {
            display: block; width: 100%; padding: 9px 16px; color: #c8c5f0;
            font-family: 'Outfit', sans-serif; font-size: .85rem; text-decoration: none;
            background: none; border: none; text-align: left; cursor: pointer;
            transition: background .15s;
        }
        .nav-user-dropdown a:hover, .nav-user-dropdown button:hover { background: rgba(168,164,224,.1); }

        /* ── HAMBURGER (mobile only) ── */
        .nav-hamburger {
            display: none; background: none; border: none;
            cursor: pointer; color: #a8a4e0; padding: 4px;
            flex-shrink: 0; align-items: center; justify-content: center;
        }
        .nav-hamburger svg { width: 24px; height: 24px; }

        /* ── SIDEBAR OVERLAY (mobile) ── */
        .sidebar-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,.5); z-index: 89;
        }
        .sidebar-overlay.open { display: block; }

        /* ── LAYOUT ── */
        .layout { display: flex; padding-top: 64px; min-height: 100vh; }

        /* ── SIDEBAR ── */
        .sidebar {
            width: 280px; min-height: calc(100vh - 64px); background: var(--sidebar);
            position: fixed; top: 64px; left: 0; bottom: 0;
            display: flex; flex-direction: column; padding: 16px 0; z-index: 90;
            transition: transform .25s ease;
        }
        .nav-main { flex: 1; }
        .nav-item {
            display: flex; align-items: center; gap: 14px; padding: 13px 24px;
            color: #a8a4e0; text-decoration: none; font-size: .82rem; font-weight: 600;
            letter-spacing: .06em; text-transform: uppercase;
            transition: background .15s, color .15s;
        }
        .nav-item:hover { background: rgba(168,164,224,.08); color: #c8c5f0; }
        .nav-item.active { background: var(--active); color: #ffffff; border-left: 3px solid var(--accent); }
        .nav-item svg { width: 20px; height: 20px; flex-shrink: 0; }
        .nav-bottom { border-top: 1px solid #3a3a5a; padding-top: 12px; margin-top: 12px; }

        /* ── MAIN ── */
        .main { margin-left: 280px; flex: 1; padding: 28px 28px 40px; }
        .content-card {
            background: var(--card); border-radius: 12px;
            padding: 32px 32px 28px; box-shadow: 0 2px 12px rgba(0,0,0,.06);
        }

        /* ── FLASH ── */
        .flash { border-radius: 8px; padding: 11px 16px; font-size: .84rem; margin-bottom: 20px; }
        .flash-success { background: rgba(39,174,96,.12);  border: 1px solid rgba(39,174,96,.3);  color: #1e8449; }
        .flash-error   { background: rgba(231,76,60,.10);  border: 1px solid rgba(231,76,60,.3);  color: #c0392b; }

        /* ── TABLE ── */
        .data-table { width: 100%; border-collapse: collapse; font-size: .84rem; }
        .data-table thead tr { background: #d8d6e8; }
        .data-table th { padding: 11px 14px; text-align: left; font-weight: 600; font-size: .8rem; color: #4a4a6a; letter-spacing: .03em; }
        .data-table td { padding: 11px 14px; border-bottom: 1px solid #f0eef8; color: var(--text); vertical-align: middle; }
        .data-table tr:last-child td { border-bottom: none; }
        .data-table tbody tr:hover   { background: #faf9ff; }

        /* ── BADGES ── */
        .badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: .75rem; font-weight: 600; }
        .badge-active    { background: rgba(39,174,96,.12);  color: #1e8449; }
        .badge-pending   { background: rgba(230,126,34,.12); color: #b7600a; }
        .badge-inactive  { background: rgba(149,149,149,.15);color: #666; }
        .badge-expired   { background: rgba(231,76,60,.10);  color: #c0392b; }
        .badge-due-soon  { background: rgba(243,156,18,.12); color: #9a6300; }
        .badge-cancelled { background: rgba(149,149,149,.15);color: #666; }

        /* ── ACTION BUTTONS ── */
        .action-btn { background: none; border: none; cursor: pointer; padding: 5px; border-radius: 6px; display: inline-flex; align-items: center; transition: background .15s; }
        .action-btn svg { width: 18px; height: 18px; }
        .action-btn:hover { background: rgba(0,0,0,.06); }
        .btn-view { color: #2a3050; }

        /* ── PAGINATION ── */
        .pagination-row { display: flex; align-items: center; gap: 12px; margin-top: 24px; font-size: .85rem; color: var(--muted); }
        .pagination-row a { display: flex; align-items: center; gap: 4px; color: var(--text); text-decoration: none; font-weight: 500; transition: color .15s; }
        .pagination-row a:hover { color: var(--accent); }
        .pagination-row a svg { width: 16px; height: 16px; }

        /* ── FILTERS ── */
        .filter-row { display: flex; align-items: center; gap: 12px; margin-bottom: 20px; flex-wrap: wrap; }
        .filter-row label { font-size: .82rem; color: var(--muted); font-weight: 500; }
        .filter-select {
            background: #f0eef8; border: 1.5px solid #ddd8f0; border-radius: 8px;
            padding: 7px 30px 7px 12px; font-family: 'Outfit', sans-serif; font-size: .82rem;
            color: var(--text); outline: none; appearance: none; cursor: pointer;
            transition: border-color .2s;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%238884a8' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
            background-repeat: no-repeat; background-position: right 10px center;
        }
        .filter-select:focus { border-color: var(--accent); }
        .btn-filter {
            background: #3b4f7a; color: white; border: none; border-radius: 8px;
            padding: 7px 18px; font-family: 'Outfit', sans-serif;
            font-size: .82rem; font-weight: 600; cursor: pointer; transition: background .2s;
        }
        .btn-filter:hover { background: #4a6090; }

        /* ── BORROW BUTTON ── */
        .btn-borrow {
            background: #2a3050; color: white; border: none; border-radius: 8px;
            padding: 6px 18px; font-family: 'Outfit', sans-serif;
            font-size: .82rem; font-weight: 600; cursor: pointer; transition: background .2s;
        }
        .btn-borrow:hover:not(:disabled) { background: #3b4f7a; }
        .btn-borrow:disabled { background: #c8c5e0; color: #9997b8; cursor: not-allowed; }

        /* ── MODAL ── */
        .modal-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(10,10,20,.5); z-index: 300;
            align-items: center; justify-content: center;
        }
        .modal-overlay.open { display: flex; }
        .modal-box {
            background: white; border-radius: 12px; padding: 32px 36px 28px;
            max-width: 480px; width: 90%;
            box-shadow: 0 20px 50px rgba(0,0,0,.25); animation: popIn .2s ease;
        }
        @keyframes popIn { from{transform:scale(.92);opacity:0} to{transform:scale(1);opacity:1} }
        .modal-title {
            font-size: 1.05rem; font-weight: 700; color: #1a1a2e; text-align: center;
            margin-bottom: 20px; padding-bottom: 14px; border-bottom: 1px solid #f0eef8;
            letter-spacing: .06em; text-transform: uppercase;
        }
        .detail-row {
            display: flex; justify-content: space-between; align-items: center;
            padding: 10px 0; border-bottom: 1px solid #f5f4fc; font-size: .88rem;
        }
        .detail-row:last-of-type { border-bottom: none; }
        .detail-row .d-label { color: #8884a8; }
        .detail-row .d-value { font-weight: 600; color: #1a1a2e; text-align: right; max-width: 60%; font-size: .84rem; }
        .detail-notice { font-size: .78rem; color: #c0392b; margin-top: 14px; text-align: center; line-height: 1.5; }
        .modal-actions { display: flex; gap: 10px; justify-content: center; margin-top: 22px; }
        .btn-modal-cancel {
            background: white; color: #3a3a5a; border: 2px solid #e0def0; border-radius: 8px;
            padding: 10px 32px; font-family: 'Outfit', sans-serif;
            font-size: .88rem; font-weight: 600; cursor: pointer; transition: background .15s;
        }
        .btn-modal-cancel:hover { background: #f5f4fc; }
        .btn-modal-confirm {
            background: #2a3050; color: white; border: none; border-radius: 8px;
            padding: 10px 32px; font-family: 'Outfit', sans-serif;
            font-size: .88rem; font-weight: 600; cursor: pointer; transition: background .15s;
        }
        .btn-modal-confirm:hover { background: #3b4f7a; }

        /* ── MISC ── */
        .search-input {
            background: #f0eef8; border: 1.5px solid #ddd8f0; border-radius: 8px;
            padding: 7px 12px; font-family: 'Outfit', sans-serif; font-size: .82rem;
            color: var(--text); outline: none; transition: border-color .2s; width: 200px;
        }
        .search-input:focus { border-color: var(--accent); }
        .empty-state { text-align: center; padding: 32px; color: var(--muted); font-size: .88rem; }

        /* ── PROFILE ── */
        .profile-title { font-size: 1.5rem; font-weight: 700; color: #1a1a2e; margin-bottom: 28px; }
        .profile-body  { display: grid; grid-template-columns: 220px 1fr; gap: 40px; align-items: start; }
        .avatar-col    { display: flex; flex-direction: column; align-items: center; gap: 10px; }
        .avatar-circle {
            width: 120px; height: 120px; border-radius: 50%;
            background: #d8d6e8; overflow: hidden;
            display: flex; align-items: center; justify-content: center;
        }
        .avatar-circle img { width: 100%; height: 100%; object-fit: cover; }
        .avatar-initials { font-size: 2.2rem; font-weight: 700; color: #8884a8; }
        .btn-photo, .btn-password {
            width: 100%; background: #2a3050; color: white; border: none;
            border-radius: 8px; padding: 9px 16px; font-family: 'Outfit', sans-serif;
            font-size: .82rem; font-weight: 600; cursor: pointer; transition: background .2s;
        }
        .btn-photo:hover, .btn-password:hover { background: #3b4f7a; }
        .info-section { margin-bottom: 28px; }
        .info-section h3 { font-size: 1.05rem; font-weight: 700; color: #1a1a2e; margin-bottom: 14px; }
        .info-table { width: 100%; border-collapse: collapse; }
        .info-table tr { border-bottom: 1px solid #f0eef8; }
        .info-table tr:last-child { border-bottom: none; }
        .info-table td { padding: 10px 0; font-size: .9rem; }
        .info-table td:first-child { color: #5a5a7a; width: 160px; }
        .info-table td:last-child  { font-weight: 700; color: #1a1a2e; text-align: right; }

        /* ── KPI ── */
        .kpi-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 32px; }
        .kpi-card {
            border-radius: 10px; padding: 24px 24px 20px;
            display: flex; flex-direction: column; justify-content: space-between;
            min-height: 140px; text-decoration: none;
            transition: transform .18s, box-shadow .18s;
            cursor: pointer; position: relative; overflow: hidden;
        }
        .kpi-card:hover { transform: translateY(-3px); box-shadow: 0 10px 28px rgba(0,0,0,.18); }
        .kpi-card-1 { background: #3b4f7a; }
        .kpi-card-2 { background: #8b2e2e; }
        .kpi-card-3 { background: #2a3050; }
        .kpi-number { font-size: 2.8rem; font-weight: 700; color: white; line-height: 1; }
        .kpi-label  { font-size: 1rem; font-weight: 400; color: rgba(255,255,255,.88); margin-top: 6px; line-height: 1.3; }
        .kpi-arrow  { position: absolute; bottom: 18px; right: 20px; color: rgba(255,255,255,.7); }
        .kpi-arrow svg { width: 22px; height: 22px; }
        .section-title { font-size: 1rem; font-weight: 600; color: #3a3a5a; margin-bottom: 14px; }

        /* ── PASSWORD MODAL ── */
        .field { display: flex; flex-direction: column; gap: 5px; margin-bottom: 14px; }
        .field label { font-size: .76rem; font-weight: 600; color: #5a5a7a; text-transform: uppercase; letter-spacing: .04em; }
        .field input {
            background: #f0eef8; border: 1.5px solid transparent; border-radius: 8px;
            padding: 10px 12px; font-family: 'Outfit', sans-serif;
            font-size: .88rem; color: #1a1a2e; outline: none; transition: border-color .2s;
        }
        .field input:focus { border-color: #a8a4e0; background: #faf8ff; }
        .btn-cancel-modal {
            background: #f0eef8; color: #5a5a7a; border: none; border-radius: 8px;
            padding: 9px 20px; font-family: 'Outfit', sans-serif;
            font-size: .84rem; font-weight: 600; cursor: pointer;
        }
        .btn-save-pw {
            background: #2a3050; color: white; border: none; border-radius: 8px;
            padding: 9px 20px; font-family: 'Outfit', sans-serif;
            font-size: .84rem; font-weight: 600; cursor: pointer;
        }

        /* ── TABLE SCROLL WRAPPER ── */
        .table-scroll { overflow-x: auto; -webkit-overflow-scrolling: touch; }

        /* ════════════════════════════════════════
           RESPONSIVE — 768px (tablet / mobile)
        ════════════════════════════════════════ */
        @media (max-width: 768px) {
            /* Navbar */
            .nav-subtitle { display: none; }
            .nav-divider  { display: none; }
            .nav-search   { display: none; }
            .nav-user span { display: none; }
            .nav-hamburger { display: flex; }
            .navbar { padding: 0 16px; gap: 12px; }

            /* Sidebar slides off-screen */
            .sidebar {
                transform: translateX(-100%);
                z-index: 95;
            }
            .sidebar.open { transform: translateX(0); }

            /* Main fills full width */
            .main { margin-left: 0; padding: 16px 16px 32px; }
            .content-card { padding: 20px 16px; }

            /* KPI grid: 2 columns */
            .kpi-grid { grid-template-columns: repeat(2, 1fr); }

            /* Profile: stack vertically */
            .profile-body { grid-template-columns: 1fr; gap: 24px; }
            .avatar-col {
                flex-direction: row; flex-wrap: wrap;
                gap: 12px; align-items: center;
            }
            .avatar-circle { width: 80px; height: 80px; flex-shrink: 0; }
            .avatar-initials { font-size: 1.6rem; }
            .btn-photo, .btn-password { width: auto; flex: 1; min-width: 120px; }
            .info-table td:first-child { width: 120px; }

            /* Modal full-width */
            .modal-box { padding: 20px 16px; width: 95%; }

            /* Filters: stack */
            .filter-row { flex-direction: column; align-items: stretch; gap: 8px; }
            .filter-select { width: 100%; }
            .search-input  { width: 100%; }
            .btn-filter    { width: 100%; }
            .filter-row label { margin-bottom: -4px; }
        }

        /* ════════════════════════════════════════
           RESPONSIVE — 540px (small phones)
        ════════════════════════════════════════ */
        @media (max-width: 540px) {
            .kpi-grid { grid-template-columns: 1fr; }
            .kpi-card { min-height: 100px; }
            .kpi-number { font-size: 2.2rem; }
            .content-card { padding: 16px 12px; }
        }
    </style>
    @stack('styles')
</head>
<body>

@php
    $rp = Auth::user()->isFaculty() ? 'faculty' : 'student';
@endphp

{{-- ── SIDEBAR OVERLAY (mobile tap-outside to close) ── --}}
<div class="sidebar-overlay" id="sidebarOverlay"></div>

{{-- ── TOP NAVBAR ── --}}
<nav class="navbar">
    {{-- Hamburger: only visible on mobile --}}
    <button class="nav-hamburger" id="sidebarToggle" aria-label="Toggle menu">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
             stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
    </button>

    <a href="{{ route($rp . '.dashboard') }}" class="nav-brand">
        <img src="{{ asset('images/axiom-logo-trans.png') }}" alt="AXIOM Logo"
             style="height:42px;width:auto;filter:brightness(1.1);">
        <div style="display:flex;flex-direction:column;line-height:1;">
            <span class="nav-brand-name">AXIOM</span>
        </div>
        <span class="nav-divider">|</span>
        <span class="nav-subtitle">Library E-Resource Management</span>
    </a>

    {{-- Global search → redirects to Browse Books --}}
    <form class="nav-search" method="GET" action="{{ route($rp . '.books.index') }}">
        <span class="nav-search-icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
            </svg>
        </span>
        <input type="text" name="search"
               placeholder="Search by title, author, or ISBN"
               value="{{ request('search') }}">
    </form>

    <div class="nav-right">
        <x-help-panel />
        <x-notification-bell />

        <div class="nav-user" tabindex="0">
            @if(Auth::user()->profile_photo)
                <img src="{{ Auth::user()->profile_photo }}"
                    style="width:32px;height:32px;border-radius:50%;object-fit:cover;"
                    alt="Avatar">
            @else
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655
                             6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            @endif
            <span>{{ Auth::user()->full_name }}</span>

            <div class="nav-user-dropdown">
                <a href="{{ route($rp . '.profile') }}">My Profile</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit">Log Out</button>
                </form>
            </div>
        </div>
    </div>
</nav>

{{-- ── BODY LAYOUT ── --}}
<div class="layout">
    <aside class="sidebar" id="mainSidebar">
        <nav class="nav-main">

            <a href="{{ route($rp . '.dashboard') }}"
               class="nav-item {{ request()->routeIs($rp . '.dashboard') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor" stroke-width="1.8">
                    <rect x="3"  y="3"  width="7" height="7" rx="1" stroke="currentColor" stroke-width="1.8"/>
                    <rect x="14" y="3"  width="7" height="7" rx="1" stroke="currentColor" stroke-width="1.8"/>
                    <rect x="3"  y="14" width="7" height="7" rx="1" stroke="currentColor" stroke-width="1.8"/>
                    <rect x="14" y="14" width="7" height="7" rx="1" stroke="currentColor" stroke-width="1.8"/>
                </svg>
                Dashboard
            </a>

            <a href="{{ route($rp . '.books.index') }}"
               class="nav-item {{ request()->routeIs($rp . '.books.*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                </svg>
                Browse Books
            </a>

            <a href="{{ route($rp . '.my-books.index') }}"
               class="nav-item {{ request()->routeIs($rp . '.my-books.*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477
                             3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5
                             1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477
                             4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746
                             0-3.332.477-4.5 1.253"/>
                </svg>
                My Books
            </a>

        </nav>

        <div class="nav-bottom">
            <a href="{{ route($rp . '.profile') }}"
               class="nav-item {{ request()->routeIs($rp . '.profile') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655
                             6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                My Profile
            </a>
        </div>
    </aside>

    <main class="main">
        @yield('content')
    </main>
</div>

<script>
    const sidebar = document.getElementById('mainSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const toggle  = document.getElementById('sidebarToggle');

    toggle.addEventListener('click', () => {
        sidebar.classList.toggle('open');
        overlay.classList.toggle('open');
    });
    overlay.addEventListener('click', () => {
        sidebar.classList.remove('open');
        overlay.classList.remove('open');
    });

    // Close sidebar on nav-item click (mobile UX)
    sidebar.querySelectorAll('.nav-item').forEach(item => {
        item.addEventListener('click', () => {
            sidebar.classList.remove('open');
            overlay.classList.remove('open');
        });
    });
</script>

@stack('scripts')
<x-toast />
</body>
</html>