<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lucky Star — {{ $title ?? (auth()->user()->role === 'admin' ? 'Admin' : 'Manager') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        :root {
            --espresso:  #1a0f0a;
            --roast:     #2d1810;
            --mahogany:  #4a2518;
            --caramel:   #c8813a;
            --gold:      #d4a847;
            --cream:     #f5ead8;
            --latte:     #e8d5b7;
            --steam:     #f2e8d6;
            --chalk:     #fefcf8;
            --sidebar-w: 220px;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Lato', sans-serif;
            background: #f0e8d8;
            color: #2d1810;
            min-height: 100vh;
            display: flex;
        }

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-w);
            background: linear-gradient(180deg, #5a4231 0%, #3a281d 100%); /* ✅ lighter coffee */
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 100;
            box-shadow: 4px 0 18px rgba(0,0,0,0.20);
        }
        .sidebar-brand {
            padding: 1.5rem 1.25rem 1.25rem;
            border-bottom: 1px solid rgba(245,234,216,0.12);
        }
        .brand-name {
            font-family: 'Playfair Display', serif;
            font-size: 1.2rem;
            color: rgba(212,168,71,0.98); /* ✅ brighter */
            display: block;
            letter-spacing: 0.02em;
        }
        .brand-sub {
            font-size: 0.62rem;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: rgba(245,234,216,0.65); /* ✅ more readable */
            margin-top: 0.2rem;
        }

        .sidebar-nav { flex: 1; padding: 1rem 0.75rem; display: flex; flex-direction: column; gap: 0.2rem; }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.65rem 0.85rem;
            border-radius: 8px;
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            color: rgba(245,234,216,0.82); /* ✅ brighter */
            text-decoration: none;
            transition: all 0.15s;
            position: relative;
        }
        .nav-item:hover {
            color: rgba(255,255,255,0.95);
            background: rgba(255,255,255,0.10);
        }

        .nav-item.active {
            color: rgba(255,255,255,0.98);
            background: rgba(255,255,255,0.16);
        }

        .nav-item.active::before {
            content: '';
            position: absolute;
            left: 0; top: 20%; bottom: 20%;
            width: 3px;
            background: rgba(212,168,71,0.95); /* ✅ gold */
            border-radius: 0 2px 2px 0;
        }

        .nav-icon { font-size: 0.9rem; width: 18px; text-align: center; flex-shrink: 0; }

        .nav-section-label {
            font-size: 0.58rem;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: rgba(245,234,216,0.42); /* ✅ brighter label */
            padding: 0.75rem 0.85rem 0.3rem;
            margin-top: 0.25rem;
        }

        .sidebar-footer {
            padding: 1rem 1.25rem;
            border-top: 1px solid rgba(245,234,216,0.10);
        }
        .user-name { font-size: 0.75rem; font-weight: 700; color: rgba(255,255,255,0.85); margin-bottom: 0.2rem; }
        .user-role { font-size: 0.6rem; letter-spacing: 0.08em; text-transform: uppercase; color: rgba(245,234,216,0.60); }

        .logout-btn {
            margin-top: 0.75rem;
            font-size: 0.7rem;
            color: rgba(245,234,216,0.55);
            background: none;
            border: none;
            cursor: pointer;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            transition: color 0.15s;
        }
        .logout-btn:hover { color: rgba(255,120,120,0.85); }

        /* Main content */
        .main-content {
            margin-left: var(--sidebar-w);
            flex: 1;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .page-area {
            padding: 2rem 2.25rem;
            width: 100%;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        /* Page title */
        .page-title {
            font-family: 'Playfair Display', serif;
            font-size: clamp(1.5rem, 2.5vw, 2.25rem);
            font-weight: 700;
            color: var(--roast);
            margin-bottom: 1.5rem;
            letter-spacing: -0.01em;
        }

        /* Cards */
        .card {
            background: white;
            border: 1px solid rgba(74,37,24,0.1);
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(45,24,16,0.07);
        }
        .card-header {
            padding: 0.9rem 1.25rem;
            border-bottom: 1px solid rgba(74,37,24,0.08);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .card-title {
            font-family: 'Playfair Display', serif;
            font-size: clamp(0.85rem, 1.2vw, 1.1rem);
            font-weight: 600;
            color: var(--roast);
        }

        /* KPI cards */
        .kpi-card {
            background: white;
            border: 1px solid rgba(74,37,24,0.1);
            border-radius: 14px;
            padding: 1.25rem 1.5rem;
            box-shadow: 0 2px 12px rgba(45,24,16,0.06);
            position: relative;
            overflow: hidden;
        }
        .kpi-card::after {
            content: '';
            position: absolute;
            bottom: 0; left: 0; right: 0;
            height: 3px;
        }
        .kpi-gold::after  { background: linear-gradient(90deg, var(--caramel), var(--gold)); }
        .kpi-roast::after { background: linear-gradient(90deg, var(--roast), var(--mahogany)); }
        .kpi-moss::after  { background: linear-gradient(90deg, #3d7a3d, #5a9e5a); }
        .kpi-red::after   { background: linear-gradient(90deg, #c0392b, #e05252); }
        .kpi-label { font-size: clamp(0.6rem, 0.8vw, 0.7rem); letter-spacing: 0.1em; text-transform: uppercase; color: #9a7a68; margin-bottom: 0.5rem; }
        .kpi-value { font-family: 'Playfair Display', serif; font-size: clamp(1.5rem, 2.2vw, 2.2rem); font-weight: 700; color: var(--roast); line-height: 1; }
        .kpi-sub { font-size: clamp(0.6rem, 0.9vw, 0.75rem); color: #a08070; margin-top: 0.35rem; }

        /* Tables */
        .data-table { width: 100%; border-collapse: collapse; font-size: clamp(0.75rem, 1vw, 0.9rem); }
        .data-table th {
            padding: 0.65rem 1rem;
            text-align: left;
            font-size: clamp(0.6rem, 0.8vw, 0.7rem);
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #9a7a68;
            background: #faf5ee;
            border-bottom: 1px solid rgba(74,37,24,0.1);
        }
        .data-table td {
            padding: clamp(0.6rem, 0.8vw, 0.7rem) 1rem;
            border-bottom: 1px solid rgba(74,37,24,0.06);
            color: #3d2415;
            vertical-align: middle;
        }
        .data-table tr:hover td { background: #fdf8f2; }
        .data-table tr:last-child td { border-bottom: none; }

        /* Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.2rem 0.55rem;
            border-radius: 20px;
            font-size: clamp(0.6rem, 0.8vw, 0.7rem);
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }
        .badge-active   { background: #eef7ee; color: #2d6a2d; border: 1px solid #b8d8b8; }
        .badge-inactive { background: #f5f0eb; color: #9a7a68; border: 1px solid #d4c4b4; }
        .badge-manager  { background: #fdf0e0; color: #8a5a20; border: 1px solid #e8c890; }
        .badge-cashier  { background: #f0ebe5; color: #6a4a35; border: 1px solid #c8b0a0; }
        .badge-in       { background: #eef7ee; color: #2d6a2d; border: 1px solid #b8d8b8; }
        .badge-out      { background: #fdf0f0; color: #8a2020; border: 1px solid #e8b8b8; }
        .badge-low      { background: #fff8e8; color: #8a5a10; border: 1px solid #e8d090; }

        /* Buttons */
        .btn-primary {
            background: linear-gradient(135deg, var(--caramel), var(--gold));
            color: var(--espresso);
            border: none;
            padding: 0.55rem 1.25rem;
            border-radius: 8px;
            font-weight: 700;
            font-size: clamp(0.7rem, 0.9vw, 0.85rem);
            letter-spacing: 0.06em;
            text-transform: uppercase;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 2px 8px rgba(200,129,58,0.2);
        }
        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 4px 14px rgba(200,129,58,0.3); }
        .btn-secondary {
            background: #f5ede0;
            color: var(--mahogany);
            border: 1px solid rgba(74,37,24,0.15);
            padding: 0.55rem 1.1rem;
            border-radius: 8px;
            font-weight: 700;
            font-size: clamp(0.7rem, 0.9vw, 0.85rem);
            cursor: pointer;
            transition: all 0.15s;
        }
        .btn-secondary:hover { background: #ede0cc; }
        .btn-danger {
            background: #fdf0f0;
            color: #c0392b;
            border: 1px solid rgba(192,57,43,0.2);
            padding: 0.55rem 1.1rem;
            border-radius: 8px;
            font-weight: 700;
            font-size: clamp(0.7rem, 0.9vw, 0.85rem);
            cursor: pointer;
            transition: all 0.15s;
        }
        .btn-danger:hover { background: #fde8e8; }
        .btn-link { background: none; border: none; color: var(--caramel); font-size: clamp(0.7rem, 0.9vw, 0.85rem); font-weight: 700; cursor: pointer; letter-spacing: 0.04em; }
        .btn-link:hover { color: var(--mahogany); text-decoration: underline; }

        /* Form inputs */
        .form-input {
            width: 100%;
            border: 1px solid rgba(74,37,24,0.2);
            border-radius: 8px;
            padding: 0.55rem 0.85rem;
            font-size: clamp(0.75rem, 1vw, 0.9rem);
            font-family: 'Lato', sans-serif;
            color: var(--roast);
            background: white;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .form-input:focus {
            border-color: var(--caramel);
            box-shadow: 0 0 0 3px rgba(200,129,58,0.1);
        }
        .form-label { display: block; font-size: clamp(0.65rem, 0.85vw, 0.75rem); font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; color: #7a5c44; margin-bottom: 0.35rem; }
        .form-error { font-size: clamp(0.6rem, 0.8vw, 0.7rem); color: #c0392b; margin-top: 0.25rem; }

        /* Modal */
        .modal-bg { background: rgba(20,10,5,0.7); backdrop-filter: blur(3px); position: fixed; inset: 0; z-index: 200; display: flex; align-items: center; justify-content: center; padding: 1rem; }
        .modal-box {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            width: 100%;
            max-width: 480px;
            box-shadow: 0 30px 80px rgba(0,0,0,0.35);
        }
        .modal-header {
            background: linear-gradient(135deg, var(--roast), var(--espresso));
            padding: 1.1rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .modal-title { font-family: 'Playfair Display', serif; font-size: 1.05rem; color: var(--caramel); }
        .modal-close { color: rgba(245,234,216,0.4); background: none; border: none; font-size: 1.1rem; cursor: pointer; transition: color 0.15s; }
        .modal-close:hover { color: var(--cream); }
        .modal-body { padding: 1.5rem; }
        .modal-footer { padding: 1rem 1.5rem; background: #faf5ee; border-top: 1px solid rgba(74,37,24,0.08); display: flex; justify-content: flex-end; gap: 0.75rem; }

        /* Alerts */
        .alert-success { background: #f0faf0; border: 1px solid #b8d8b8; color: #2d5a2d; border-radius: 10px; padding: 0.75rem 1rem; font-size: 0.82rem; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; }
        .alert-error   { background: #fdf0f0; border: 1px solid #e8b8b8; color: #8a2020; border-radius: 10px; padding: 0.75rem 1rem; font-size: 0.82rem; margin-bottom: 1rem; }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: #f0e8d8; }
        ::-webkit-scrollbar-thumb { background: rgba(74,37,24,0.2); border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(74,37,24,0.35); }

        a { color: inherit; text-decoration: none; }
    </style>
</head>
<body>

<!-- Sidebar -->
<aside class="sidebar">
    <div class="sidebar-brand">
        <span class="brand-name">✦ Lucky Star</span>
        <span class="brand-sub">{{ auth()->user()->role === 'admin' ? 'Admin Portal' : 'Manager Portal' }}</span>
    </div>

    <nav class="sidebar-nav">
        <span class="nav-section-label">Overview</span>
        <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <span class="nav-icon">◈</span> Dashboard
        </a>

        <span class="nav-section-label">Catalog</span>
        <a href="{{ route('manager.products') }}" class="nav-item {{ request()->routeIs('manager.products') ? 'active' : '' }}">
            <span class="nav-icon">☕</span> Products
        </a>

        <span class="nav-section-label">Inventory</span>
        <a href="{{ route('manager.inventory') }}" class="nav-item {{ request()->routeIs('manager.inventory') ? 'active' : '' }}">
            <span class="nav-icon">◈</span> Inventory Overview
        </a>
        <a href="{{ route('manager.stock-in') }}" class="nav-item {{ request()->routeIs('manager.stock-in') ? 'active' : '' }}">
            <span class="nav-icon">↕</span> Stock In / Out
        </a>
        <a href="{{ route('manager.movements') }}" class="nav-item {{ request()->routeIs('manager.movements') ? 'active' : '' }}">
            <span class="nav-icon">≡</span> Movement Log
        </a>

        <span class="nav-section-label">Reports</span>
        <a href="{{ route('reports.sales') }}" class="nav-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
            <span class="nav-icon">↗</span> Sales Report
        </a>
        <a href="{{ route('transactions.index') }}" class="nav-item {{ request()->routeIs('transactions.*') ? 'active' : '' }}">
            <span class="nav-icon">🧾</span> Transactions
        </a>

        <span class="nav-section-label">System</span>
        <a href="{{ route('manager.users') }}" class="nav-item {{ request()->routeIs('manager.users') ? 'active' : '' }}">
            <span class="nav-icon">◎</span> Users
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="user-name">{{ auth()->user()->name }}</div>
        <div class="user-role">{{ ucfirst(auth()->user()->role) }}</div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-btn">Sign out →</button>
        </form>
    </div>
</aside>

<!-- Main -->
<div class="main-content">
    <div class="page-area">
        @if (session('success'))
            <div class="alert-success">✓ {{ session('success') }}</div>
        @endif
        {{ $slot }}
    </div>
</div>

@livewireScripts
@stack('scripts')
</body>
</html>
