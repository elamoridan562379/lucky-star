<?php

use App\Http\Livewire\Cashier\PosTerminal;
use App\Http\Livewire\Cashier\TransactionHistory;
use App\Http\Livewire\Cashier\ReceiptView;
use App\Http\Livewire\Manager\DashboardOverview;
use App\Http\Livewire\Manager\ProductManager;
use App\Http\Livewire\Manager\InventoryStockIn as ManagerInventoryStockIn;
use App\Http\Livewire\Manager\StockMovementsTable;
use App\Http\Livewire\Manager\SalesReport;
use App\Http\Livewire\Manager\UserManager;
use App\Http\Livewire\InventoryDashboard;
use App\Http\Livewire\InventoryStockIn;
use App\Http\Livewire\InventoryMovements;
use App\Http\Livewire\InventoryProducts;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\RedirectResponse;

Route::get('/whoami', function () {
    return auth()->check()
        ? ['email' => auth()->user()->email, 'role' => auth()->user()->role]
        : 'NOT LOGGED IN';
});

Route::get('/', function (): RedirectResponse {
    if (!auth()->check()) {
        return redirect()->route('login');
    }

    $role = auth()->user()->role ?? 'cashier';

    // inventory clerk -> inventory dashboard
    if ($role === 'inventory_clerk') {
        return redirect()->route('inventory.dashboard');
    }

    // manager/admin -> manager dashboard
    if (in_array($role, ['manager', 'admin'], true)) {
        return redirect()->route('dashboard');
    }

    // cashier -> POS
    return redirect()->route('pos');
});

// ── Auth routes (Laravel Breeze) ──────────────────────
require __DIR__ . '/auth.php';

// — Cashier routes
Route::middleware(['auth', 'role:cashier,manager,admin'])->group(function (): void {
    Route::get('/pos', PosTerminal::class)->name('pos');
    Route::get('/transactions', TransactionHistory::class)->name('transactions.index');
    Route::get('/transactions/{id}', ReceiptView::class)->name('transactions.show');
});

// — Inventory Clerk routes (Admin & Clerk)
Route::middleware(['auth', 'role:inventory_clerk,admin'])
    ->prefix('inventory')
    ->group(function (): void {
        Route::get('/dashboard', InventoryDashboard::class)->name('inventory.dashboard');
        Route::get('/stock-in', InventoryStockIn::class)->name('inventory.stock-in');
        Route::get('/movements', InventoryMovements::class)->name('inventory.movements');
        Route::get('/products', InventoryProducts::class)->name('inventory.products');
    });

// — Manager routes with inventory oversight
Route::middleware(['auth', 'role:manager,admin'])
    ->prefix('manager')
    ->group(function (): void {

        Route::get('/dashboard', DashboardOverview::class)->name('dashboard');
        Route::get('/inventory', InventoryDashboard::class)->name('manager.inventory');
        Route::get('/products', ProductManager::class)->name('manager.products');
        Route::get('/inventory/stock-in', ManagerInventoryStockIn::class)->name('manager.stock-in');
        Route::get('/inventory/movements', StockMovementsTable::class)->name('manager.movements');
        Route::get('/reports/sales', SalesReport::class)->name('reports.sales');
        Route::get('/users', UserManager::class)->name('users.index');
    });

// — Shared inventory routes (All roles with inventory access)
Route::middleware(['auth', 'role:inventory_clerk,manager,admin'])
    ->prefix('inventory')
    ->group(function (): void {
        Route::get('/shared/movements', InventoryMovements::class)->name('inventory.shared.movements');
        Route::get('/shared/products', InventoryProducts::class)->name('inventory.shared.products');
    });
