<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Denied - Lucky Star Coffee</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --espresso:  #1a0f0a;
            --roast:     #2d1810;
            --mahogany:  #4a2518;
            --caramel:   #c8813a;
            --gold:      #d4a847;
            --cream:     #f5ead8;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Lato', sans-serif;
            background: linear-gradient(135deg, #f0e8d8 0%, #e8d5b7 100%);
            color: var(--roast);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .error-container {
            text-align: center;
            max-width: 500px;
            padding: 2rem;
        }
        .error-icon {
            font-size: 4rem;
            color: var(--caramel);
            margin-bottom: 1rem;
        }
        .error-code {
            font-family: 'Playfair Display', serif;
            font-size: 3rem;
            font-weight: 700;
            color: var(--roast);
            margin-bottom: 0.5rem;
        }
        .error-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--mahogany);
            margin-bottom: 1rem;
        }
        .error-message {
            font-size: 1rem;
            line-height: 1.6;
            color: #666;
            margin-bottom: 2rem;
        }
        .user-info {
            background: white;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .user-role {
            font-weight: 700;
            color: var(--caramel);
        }
        .suggestions {
            background: rgba(255,255,255,0.8);
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }
        .suggestion-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }
        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, var(--caramel), var(--gold));
            color: var(--espresso);
            text-decoration: none;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.9rem;
            transition: all 0.2s;
            box-shadow: 0 2px 8px rgba(200,129,58,0.2);
        }
        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 14px rgba(200,129,58,0.3);
        }
        .btn-secondary {
            background: #f5ede0;
            color: var(--mahogany);
            margin-left: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">🔒</div>
        <div class="error-code">403</div>
        <div class="error-title">Access Denied</div>
        
        <div class="user-info">
            <div>You are logged in as:</div>
            <div class="user-role">{{ auth()->user()->name }} ({{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }})</div>
        </div>

        <div class="error-message">
            You don't have permission to access this page. This area is restricted to specific user roles.
        </div>

        <div class="suggestions">
            <h3 style="margin-bottom: 1rem; color: var(--roast);">📍 Suggested Pages:</h3>
            
            @if(auth()->user()->role === 'manager' || auth()->user()->role === 'admin')
                <div class="suggestion-item">
                    <span>📊</span>
                    <a href="{{ route('dashboard') }}" class="btn">Manager Dashboard</a>
                </div>
                <div class="suggestion-item">
                    <span>📦</span>
                    <a href="{{ route('manager.inventory') }}" class="btn">Inventory Overview</a>
                </div>
            @elseif(auth()->user()->role === 'inventory_clerk')
                <div class="suggestion-item">
                    <span>📋</span>
                    <a href="{{ route('inventory.dashboard') }}" class="btn">Inventory Dashboard</a>
                </div>
                <div class="suggestion-item">
                    <span>📦</span>
                    <a href="{{ route('inventory.stock-in') }}" class="btn">Stock Management</a>
                </div>
            @elseif(auth()->user()->role === 'cashier')
                <div class="suggestion-item">
                    <span>💰</span>
                    <a href="{{ route('pos') }}" class="btn">Point of Sale</a>
                </div>
                <div class="suggestion-item">
                    <span>🧾</span>
                    <a href="{{ route('transactions.index') }}" class="btn">Transactions</a>
                </div>
            @endif
            
            <div class="suggestion-item">
                <span>🏠</span>
                <a href="{{ route('dashboard') }}" class="btn btn-secondary">Go to Dashboard</a>
            </div>
        </div>

        <div style="font-size: 0.8rem; color: #999;">
            If you believe this is an error, please contact your system administrator.
        </div>
    </div>
</body>
</html>
