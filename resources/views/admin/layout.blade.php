<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AXIOM — @yield('title', 'Admin')</title>
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
            --kpi1:    #3b4f7a;
            --kpi2:    #8b2e2e;
            --kpi3:    #2a3050;
            --success: #27ae60;
            --warning: #e67e22;
            --danger:  #e74c3c;
            --pending: #e67e22;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
        }

        /* ── TOP NAVBAR ── */
        .navbar {
            position: fixed;
            top: 0; left: 0; right: 0;
            height: 64px;
            background: var(--navy);
            display: flex;
            align-items: center;
            padding: 0 24px;
            z-index: 100;
            gap: 16px;
        }

        .nav-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            flex-shrink: 0;
        }

        .nav-brand svg { width: 36px; height: 36px; color: #a8a4e0; }

        .nav-brand-text {
            display: flex;
            flex-direction: column;
            line-height: 1;
        }

        .nav-brand-name {
            font-size: 1rem;
            font-weight: 700;
            color: #c8c5f0;
            letter-spacing: 0.08em;
        }

        .nav-divider {
            color: #4a4a6a;
            font-size: 1.1rem;
            margin: 0 6px;
        }

        .nav-subtitle {
            font-size: 0.65rem;
            font-weight: 400;
            color: #8884a8;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }

        /* Search */
        .nav-search {
            flex: 1;
            max-width: 340px;
            margin: 0 auto;
            position: relative;
        }

        .nav-search input {
            width: 100%;
            background: #f0eeff;
            border: none;
            border-radius: 24px;
            padding: 9px 18px 9px 42px;
            font-family: 'Outfit', sans-serif;
            font-size: 0.88rem;
            color: var(--text);
            outline: none;
        }

        .nav-search input::placeholder { color: var(--muted); }

        .nav-search-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
            pointer-events: none;
        }

        .nav-search-icon svg { width: 16px; height: 16px; }

        /* Nav right */
        .nav-right {
            display: flex;
            align-items: center;
            gap: 18px;
            margin-left: auto;
        }

        .nav-icon-btn {
            background: none;
            border: none;
            cursor: pointer;
            color: #a8a4e0;
            display: flex;
            align-items: center;
            padding: 4px;
            border-radius: 50%;
            transition: background 0.2s;
            position: relative;
        }

        .nav-icon-btn:hover { background: rgba(168,164,224,0.15); }
        .nav-icon-btn svg   { width: 24px; height: 24px; }

        .nav-user {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #c8c5f0;
            font-size: 0.88rem;
            font-weight: 500;
            cursor: pointer;
            position: relative;
        }

        .nav-user svg { width: 32px; height: 32px; color: #a8a4e0; }

        .nav-user-dropdown {
            position: absolute;
            top: calc(100% + 12px);
            right: 0;
            background: var(--navy);
            border: 1px solid #3a3a5a;
            border-radius: 10px;
            padding: 6px 0;
            min-width: 150px;
            display: none;
            z-index: 200;
            box-shadow: 0 8px 24px rgba(0,0,0,0.3);
        }

        .nav-user:hover .nav-user-dropdown,
        .nav-user:focus-within .nav-user-dropdown { display: block; }

        .nav-user-dropdown a,
        .nav-user-dropdown button {
            display: block;
            width: 100%;
            padding: 9px 16px;
            color: #c8c5f0;
            font-family: 'Outfit', sans-serif;
            font-size: 0.85rem;
            text-decoration: none;
            background: none;
            border: none;
            text-align: left;
            cursor: pointer;
            transition: background 0.15s;
        }

        .nav-user-dropdown a:hover,
        .nav-user-dropdown button:hover { background: rgba(168,164,224,0.1); }

        /* ── LAYOUT ── */
        .layout {
            display: flex;
            padding-top: 64px;
            min-height: 100vh;
        }

        /* ── SIDEBAR ── */
        .sidebar {
            width: 280px;
            min-height: calc(100vh - 64px);
            background: var(--sidebar);
            position: fixed;
            top: 64px;
            left: 0;
            bottom: 0;
            display: flex;
            flex-direction: column;
            padding: 16px 0;
            z-index: 90;
        }

        .nav-main { flex: 1; }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 13px 24px;
            color: #a8a4e0;
            text-decoration: none;
            font-size: 0.82rem;
            font-weight: 600;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            transition: background 0.15s, color 0.15s;
            border-radius: 0;
        }

        .nav-item:hover { background: rgba(168,164,224,0.08); color: #c8c5f0; }

        .nav-item.active {
            background: var(--active);
            color: #ffffff;
            border-left: 3px solid var(--accent);
        }

        .nav-item svg { width: 20px; height: 20px; flex-shrink: 0; }

        .nav-bottom {
            border-top: 1px solid #3a3a5a;
            padding-top: 12px;
            margin-top: 12px;
        }

        /* ── MAIN CONTENT ── */
        .main {
            margin-left: 280px;
            flex: 1;
            padding: 28px 28px 40px;
        }

        /* ── CONTENT CARD ── */
        .content-card {
            background: var(--card);
            border-radius: 12px;
            padding: 32px 32px 28px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        }

        /* ── FLASH MESSAGES ── */
        .flash {
            border-radius: 8px;
            padding: 11px 16px;
            font-size: 0.84rem;
            margin-bottom: 20px;
        }

        .flash-success {
            background: rgba(39,174,96,0.12);
            border: 1px solid rgba(39,174,96,0.3);
            color: #1e8449;
        }

        .flash-error {
            background: rgba(231,76,60,0.1);
            border: 1px solid rgba(231,76,60,0.3);
            color: #c0392b;
        }

        /* ── SHARED TABLE STYLES ── */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.84rem;
        }

        .data-table thead tr {
            background: #d8d6e8;
        }

        .data-table th {
            padding: 11px 14px;
            text-align: left;
            font-weight: 600;
            font-size: 0.8rem;
            color: #4a4a6a;
            letter-spacing: 0.03em;
        }

        .data-table td {
            padding: 11px 14px;
            border-bottom: 1px solid #f0eef8;
            color: var(--text);
            vertical-align: middle;
        }

        .data-table tr:last-child td { border-bottom: none; }
        .data-table tbody tr:hover   { background: #faf9ff; }

        /* Status badges */
        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-active   { background: rgba(39,174,96,0.12);  color: #1e8449; }
        .badge-pending  { background: rgba(230,126,34,0.12); color: #b7600a; }
        .badge-inactive { background: rgba(149,149,149,0.15);color: #666; }
        .badge-expired  { background: rgba(231,76,60,0.10);  color: #c0392b; }
        .badge-due-soon { background: rgba(243,156,18,0.12); color: #9a6300; }

        /* Action icon buttons */
        .action-btn {
            background: none;
            border: none;
            cursor: pointer;
            padding: 5px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            transition: background 0.15s;
        }

        .action-btn svg { width: 18px; height: 18px; }
        .action-btn:hover { background: rgba(0,0,0,0.06); }

        .btn-view     { color: #3b6fd4; }
        .btn-edit     { color: #e67e22; }
        .btn-approve  { color: #27ae60; }
        .btn-reject,
        .btn-deactivate { color: #e74c3c; }

        /* Pagination */
        .pagination-row {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-top: 24px;
            font-size: 0.85rem;
            color: var(--muted);
        }

        .pagination-row a {
            display: flex;
            align-items: center;
            gap: 4px;
            color: var(--text);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.15s;
        }

        .pagination-row a:hover { color: var(--accent); }
        .pagination-row a svg   { width: 16px; height: 16px; }

        /* Filter row */
        .filter-row {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .filter-row label {
            font-size: 0.82rem;
            color: var(--muted);
            font-weight: 500;
        }

        .filter-select {
            background: #f0eef8;
            border: 1.5px solid #ddd8f0;
            border-radius: 8px;
            padding: 7px 30px 7px 12px;
            font-family: 'Outfit', sans-serif;
            font-size: 0.82rem;
            color: var(--text);
            outline: none;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%238884a8' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center;
            cursor: pointer;
            transition: border-color 0.2s;
        }

        .filter-select:focus { border-color: var(--accent); }

        .btn-filter {
            background: #3b4f7a;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 7px 18px;
            font-family: 'Outfit', sans-serif;
            font-size: 0.82rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-filter:hover { background: #4a6090; }
    </style>
    @stack('styles')
</head>
<body>

{{-- ── NAVBAR ── --}}
<nav class="navbar">
    <a href="{{ route('admin.dashboard') }}" class="nav-brand">
        <img src="{{ asset('images/axiom-logo-trans.png') }}" 
            alt="AXIOM Logo" 
            class="nav-logo"
            style="height: 42px; width: auto; filter: brightness(1.1);">
        
        <div class="nav-brand-text">
            <span class="nav-brand-name">AXIOM</span>
        </div>
        <span class="nav-divider">|</span>
        <span class="nav-subtitle">Library E-Resource Management</span>
    </a>

    {{-- Search --}}
    <form class="nav-search" method="GET" action="{{ route('admin.users.index') }}" id="admin-search-form">
        <span class="nav-search-icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
            </svg>
        </span>
        <input type="text" name="search"
            placeholder="Search users or books..."
            value="{{ request('search') }}"
            id="admin-search-input">
    </form>

    <script>
    // Redirect to users or books index based on current page
    document.getElementById('admin-search-input').addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            var val = this.value.trim();
            if (!val) return;
            var isBooks = window.location.pathname.includes('/books');
            var form = document.getElementById('admin-search-form');
            form.action = isBooks
                ? '{{ route('admin.books.index') }}'
                : '{{ route('admin.users.index') }}';
            form.submit();
        }
    });
    </script>

    {{-- Right side --}}
    <div class="nav-right">
        <x-help-panel />
        <x-notification-bell />

        {{-- User dropdown --}}
        <div class="nav-user" tabindex="0">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <span>{{ Auth::user()->full_name }}</span>
            <div class="nav-user-dropdown">
                <a href="{{ route('admin.profile') }}">My Profile</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit">Log Out</button>
                </form>
            </div>
        </div>
    </div>
</nav>

{{-- ── LAYOUT ── --}}
<div class="layout">

    {{-- SIDEBAR --}}
    <aside class="sidebar">
        <nav class="nav-main">
            <a href="{{ route('admin.dashboard') }}"
               class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <rect x="3" y="3" width="7" height="7" rx="1" stroke="currentColor" stroke-width="1.8"/>
                    <rect x="14" y="3" width="7" height="7" rx="1" stroke="currentColor" stroke-width="1.8"/>
                    <rect x="3" y="14" width="7" height="7" rx="1" stroke="currentColor" stroke-width="1.8"/>
                    <rect x="14" y="14" width="7" height="7" rx="1" stroke="currentColor" stroke-width="1.8"/>
                </svg>
                Dashboard
            </a>
            <a href="{{ route('admin.users.index') }}"
               class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Manage Users
            </a>
            <a href="{{ route('admin.books.index') }}"
               class="nav-item {{ request()->routeIs('admin.books.*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"/>
                </svg>
                Manage Books
            </a>
            <a href="{{ route('admin.borrows.index') }}"
               class="nav-item {{ request()->routeIs('admin.borrows.*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Borrow Records
            </a>
            <a href="{{ route('admin.departments.index') }}"
               class="nav-item {{ request()->routeIs('admin.departments.*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
                Departments
            </a>
            <a href="{{ route('admin.settings') }}"
               class="nav-item {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                System Settings
            </a>
        </nav>

        <div class="nav-bottom">
            <a href="{{ route('admin.profile') }}"
               class="nav-item {{ request()->routeIs('admin.profile.index') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                My Profile
            </a>
        </div>
    </aside>

    {{-- MAIN --}}
    <main class="main">
        @yield('content')
    </main>

</div>

@stack('scripts')
    <x-toast />
</body>
</html>