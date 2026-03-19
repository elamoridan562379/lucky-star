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
use App\Http\Livewire\Admin\AdminDashboard;
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

    // admin -> admin dashboard
    if ($role === 'admin') {
        return redirect()->route('admin.dashboard');
    }

    // manager -> manager dashboard
    if ($role === 'manager') {
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

// — Manager routes with inventory oversight (Admin also has access)
Route::middleware(['auth', 'role:manager,admin'])
    ->prefix('manager')
    ->group(function (): void {

        Route::get('/dashboard', DashboardOverview::class)->name('dashboard');
        Route::get('/inventory', InventoryDashboard::class)->name('manager.inventory');
        Route::get('/products', ProductManager::class)->name('manager.products');
        Route::get('/products/create', ProductManager::class)->name('manager.products.create');
        Route::get('/inventory/stock-in', ManagerInventoryStockIn::class)->name('manager.stock-in');
        Route::get('/inventory/stock-in/create', [ManagerInventoryStockIn::class, 'create'])->name('manager.stock-in.create');
        Route::get('/inventory/movements', StockMovementsTable::class)->name('manager.movements');
        Route::get('/reports/sales', SalesReport::class)->name('reports.sales');
        Route::get('/reports/sales/create', [SalesReport::class, 'create'])->name('reports.sales.create');
        Route::get('/users', UserManager::class)->name('manager.users');
    });

// — Admin routes with full system access
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->group(function (): void {
        Route::get('/dashboard', AdminDashboard::class)->name('admin.dashboard');
        Route::get('/users', UserManager::class)->name('users.index');
        Route::get('/products', ProductManager::class)->name('admin.products');
        Route::get('/products/create', ProductManager::class)->name('admin.products.create');
        Route::get('/inventory', InventoryDashboard::class)->name('admin.inventory');
        Route::get('/inventory/stock-in', ManagerInventoryStockIn::class)->name('admin.stock-in');
        Route::get('/inventory/stock-in/create', ManagerInventoryStockIn::class)->name('admin.stock-in.create');
        Route::get('/inventory/movements', StockMovementsTable::class)->name('admin.movements');
        Route::get('/reports/sales', SalesReport::class)->name('admin.reports.sales');
        Route::get('/reports/sales/create', SalesReport::class)->name('admin.reports.sales.create');
        Route::get('/transactions', TransactionHistory::class)->name('admin.transactions');
    });

// — Shared inventory routes (All roles with inventory access)
Route::middleware(['auth', 'role:inventory_clerk,manager,admin'])
    ->prefix('inventory')
    ->group(function (): void {
        Route::get('/shared/movements', InventoryMovements::class)->name('inventory.shared.movements');
        Route::get('/shared/products', InventoryProducts::class)->name('inventory.shared.products');
    });
