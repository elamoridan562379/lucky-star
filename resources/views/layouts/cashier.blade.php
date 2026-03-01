<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lucky Star — {{ $title ?? 'POS' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        :root {
            --espresso:   #1a0f0a;
            --roast:      #2d1810;
            --mahogany:   #4a2518;
            --caramel:    #c8813a;
            --cream:      #f5ead8;
            --latte:      #e8d5b7;
            --steam:      #f9f3ea;
            --chalk:      #fefcf8;
            --gold:       #d4a847;
            --moss:       #3d4a2e;
        }
        * { box-sizing: border-box; }
        body {
            font-family: 'Lato', sans-serif;
            background: var(--espresso);
            color: var(--cream);
            min-height: 100vh;
        }
        .font-display { font-family: 'Playfair Display', serif; }

        /* Grain texture overlay */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.04'/%3E%3C/svg%3E");
            pointer-events: none;
            z-index: 9999;
            opacity: 0.35;
        }

        .nav-bar {
            background: linear-gradient(135deg, var(--roast) 0%, var(--espresso) 100%);
            border-bottom: 1px solid rgba(200,129,58,0.2);
            box-shadow: 0 2px 20px rgba(0,0,0,0.5);
        }
        .nav-logo {
            font-family: 'Playfair Display', serif;
            font-size: 1.25rem;
            letter-spacing: 0.02em;
            color: var(--caramel);
        }
        .nav-link {
            font-size: 0.78rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: rgba(245,234,216,0.6);
            transition: color 0.2s;
            text-decoration: none;
        }
        .nav-link:hover, .nav-link.active { color: var(--caramel); }

        /* Category tabs */
        .cat-tab {
            font-family: 'Lato', sans-serif;
            font-weight: 700;
            font-size: 0.72rem;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            padding: 0.6rem 1.4rem;
            border-radius: 2rem;
            border: 1px solid rgba(200,129,58,0.25);
            transition: all 0.2s;
            cursor: pointer;
            color: rgba(245,234,216,0.6);
            background: transparent;
        }
        .cat-tab:hover { border-color: var(--caramel); color: var(--caramel); }
        .cat-tab.active {
            background: var(--caramel);
            color: var(--espresso);
            border-color: var(--caramel);
            box-shadow: 0 4px 15px rgba(200,129,58,0.35);
        }

        /* Product cards */
        .product-card {
            background: linear-gradient(145deg, var(--roast), var(--mahogany));
            border: 1px solid rgba(200,129,58,0.15);
            border-radius: 14px;
            padding: 1.1rem;
            cursor: pointer;
            transition: all 0.2s ease;
            text-align: left;
            position: relative;
            overflow: hidden;
        }
        .product-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, rgba(200,129,58,0.4), transparent);
        }
        .product-card:hover {
            border-color: rgba(200,129,58,0.5);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.4);
        }
        .product-card:active { transform: translateY(0) scale(0.98); }
        .product-card.out-of-stock {
            opacity: 0.4;
            cursor: not-allowed;
            filter: grayscale(0.5);
        }
        .product-card.out-of-stock:hover { transform: none; }
        .product-name {
            font-family: 'Playfair Display', serif;
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--cream);
            line-height: 1.3;
            margin-bottom: 0.3rem;
        }
        .product-price {
            font-weight: 700;
            font-size: 1.05rem;
            color: var(--caramel);
            letter-spacing: 0.02em;
        }
        .product-stock {
            font-size: 0.65rem;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: rgba(245,234,216,0.4);
            margin-top: 0.4rem;
        }
        .stock-low { color: #e07a3a; }
        .stock-out {
            font-size: 0.6rem;
            background: rgba(200,60,60,0.2);
            color: #e05252;
            padding: 0.15rem 0.4rem;
            border-radius: 3px;
            border: 1px solid rgba(200,60,60,0.3);
        }

        /* Cart */
        .cart-panel {
            background: linear-gradient(180deg, var(--mahogany) 0%, var(--roast) 100%);
            border-left: 1px solid rgba(200,129,58,0.2);
            box-shadow: -4px 0 30px rgba(0,0,0,0.3);
        }
        .cart-header {
            background: rgba(0,0,0,0.25);
            border-bottom: 1px solid rgba(200,129,58,0.15);
            padding: 1rem 1.25rem;
        }
        .cart-title {
            font-family: 'Playfair Display', serif;
            font-size: 1rem;
            color: var(--caramel);
            letter-spacing: 0.03em;
        }
        .cart-item-name {
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--latte);
        }
        .cart-item-price { font-size: 0.72rem; color: var(--caramel); }
        .qty-btn {
            width: 26px; height: 26px;
            border-radius: 50%;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(200,129,58,0.2);
            color: var(--latte);
            font-size: 0.9rem;
            font-weight: 700;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            transition: all 0.15s;
        }
        .qty-btn:hover { background: var(--caramel); color: var(--espresso); border-color: var(--caramel); }

        /* Cash input */
        .cash-input {
            width: 100%;
            background: rgba(0,0,0,0.3);
            border: 1px solid rgba(200,129,58,0.3);
            border-radius: 8px;
            padding: 0.6rem 0.9rem;
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--cream);
            letter-spacing: 0.04em;
            outline: none;
            transition: border-color 0.2s;
        }
        .cash-input:focus { border-color: var(--caramel); box-shadow: 0 0 0 3px rgba(200,129,58,0.1); }
        .cash-input::placeholder { color: rgba(245,234,216,0.25); }

        /* Confirm button */
        .btn-confirm {
            width: 100%;
            padding: 0.85rem;
            border-radius: 10px;
            font-family: 'Lato', sans-serif;
            font-weight: 700;
            font-size: 0.85rem;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            background: linear-gradient(135deg, #c8813a, #d4a847);
            color: var(--espresso);
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 4px 15px rgba(200,129,58,0.3);
        }
        .btn-confirm:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(200,129,58,0.45); }
        .btn-confirm:active { transform: translateY(0); }
        .btn-confirm:disabled {
            background: rgba(255,255,255,0.06);
            color: rgba(245,234,216,0.25);
            cursor: not-allowed;
            box-shadow: none;
            transform: none;
        }

        /* Dividers */
        .cart-divider { border: none; border-top: 1px dashed rgba(200,129,58,0.2); }

        /* Search */
        .search-input {
            background: rgba(0,0,0,0.3);
            border: 1px solid rgba(200,129,58,0.2);
            border-radius: 20px;
            padding: 0.45rem 0.9rem 0.45rem 2.1rem;
            font-size: 0.78rem;
            color: var(--cream);
            outline: none;
            transition: border-color 0.2s;
            width: 200px;
        }
        .search-input:focus { border-color: rgba(200,129,58,0.5); }
        .search-input::placeholder { color: rgba(245,234,216,0.3); }

        /* Receipt modal */
        .receipt-modal-bg { background: rgba(10,5,3,0.85); backdrop-filter: blur(4px); }
        .receipt-card {
            background: var(--chalk);
            color: #2d1810;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 25px 60px rgba(0,0,0,0.6);
            max-width: 360px;
            width: 100%;
        }
        .receipt-header {
            background: linear-gradient(135deg, var(--espresso), var(--mahogany));
            padding: 1.5rem;
            text-align: center;
        }
        .receipt-logo {
            font-family: 'Playfair Display', serif;
            font-size: 1.4rem;
            color: var(--caramel);
            display: block;
            margin-bottom: 0.25rem;
        }
        .receipt-sub { font-size: 0.68rem; letter-spacing: 0.12em; text-transform: uppercase; color: rgba(200,129,58,0.6); }
        .receipt-body { padding: 1.25rem 1.5rem; font-family: 'Courier New', monospace; font-size: 0.78rem; }
        .receipt-row { display: flex; justify-content: space-between; margin-bottom: 0.3rem; }
        .receipt-label { color: #7a5c44; }
        .receipt-dashed { border: none; border-top: 1px dashed #d4b896; margin: 0.75rem 0; }
        .receipt-total { font-size: 0.95rem; font-weight: 700; color: var(--mahogany); }
        .receipt-change { color: #2d6a2d; font-weight: 700; }
        .btn-print {
            flex: 1; padding: 0.7rem;
            background: var(--mahogany); color: var(--cream);
            border: none; border-radius: 8px;
            font-weight: 700; font-size: 0.78rem;
            letter-spacing: 0.06em; text-transform: uppercase;
            cursor: pointer; transition: background 0.2s;
        }
        .btn-print:hover { background: var(--espresso); }
        .btn-new-sale {
            flex: 1; padding: 0.7rem;
            background: linear-gradient(135deg, #c8813a, #d4a847);
            color: var(--espresso); border: none; border-radius: 8px;
            font-weight: 700; font-size: 0.78rem;
            letter-spacing: 0.06em; text-transform: uppercase;
            cursor: pointer; transition: all 0.2s;
        }
        .btn-new-sale:hover { opacity: 0.9; }

        /* Error bar */
        .error-bar {
            background: rgba(180,40,40,0.15);
            border-bottom: 1px solid rgba(200,60,60,0.25);
            color: #e07070;
            font-size: 0.78rem;
            padding: 0.6rem 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: rgba(0,0,0,0.2); }
        ::-webkit-scrollbar-thumb { background: rgba(200,129,58,0.3); border-radius: 2px; }

        @media print {
            .no-print { display: none !important; }
            body { background: white; color: black; }
            body::before { display: none; }
        }
    </style>
</head>
<body>

<!-- Nav -->
<nav class="nav-bar no-print">
    <div class="px-5 py-3 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <div class="nav-logo">✦ Lucky Star</div>
            <span style="color:rgba(200,129,58,0.3); font-size:0.7rem">|</span>
            <span style="font-size:0.68rem; letter-spacing:0.1em; text-transform:uppercase; color:rgba(245,234,216,0.35)">Coffee Shop</span>
        </div>
        <div class="flex items-center gap-5">
            <a href="{{ route('pos') }}" class="nav-link {{ request()->routeIs('pos') ? 'active' : '' }}">POS Terminal</a>
            <a href="{{ route('transactions.index') }}" class="nav-link {{ request()->routeIs('transactions.*') ? 'active' : '' }}">History</a>
            <span style="color:rgba(200,129,58,0.2)">|</span>
            <span style="font-size:0.75rem; color:rgba(245,234,216,0.4)">{{ auth()->user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}" style="display:inline">
                @csrf
                <button type="submit" class="nav-link" style="background:none; border:none; cursor:pointer;">Sign out</button>
            </form>
        </div>
    </div>
</nav>

<main>{{ $slot }}</main>

@livewireScripts
@stack('scripts')
</body>
</html>
