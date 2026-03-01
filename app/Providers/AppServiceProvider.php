<?php

namespace App\Providers;

use App\Http\Livewire\Cashier\PosTerminal;
use App\Http\Livewire\Cashier\TransactionHistory;
use App\Http\Livewire\Cashier\ReceiptView;
use App\Http\Livewire\Manager\DashboardOverview;
use App\Http\Livewire\Manager\ProductManager;
use App\Http\Livewire\Manager\InventoryStockIn;
use App\Http\Livewire\Manager\StockMovementsTable;
use App\Http\Livewire\Manager\SalesReport;
use App\Http\Livewire\Manager\UserManager;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Register all Livewire components
        Livewire::component('cashier.pos-terminal',       PosTerminal::class);
        Livewire::component('cashier.transaction-history', TransactionHistory::class);
        Livewire::component('cashier.receipt-view',       ReceiptView::class);
        Livewire::component('manager.dashboard-overview', DashboardOverview::class);
        Livewire::component('manager.product-manager',    ProductManager::class);
        Livewire::component('manager.inventory-stock-in', InventoryStockIn::class);
        Livewire::component('manager.stock-movements-table', StockMovementsTable::class);
        Livewire::component('manager.sales-report',       SalesReport::class);
        Livewire::component('manager.user-manager',       UserManager::class);
    }
}
