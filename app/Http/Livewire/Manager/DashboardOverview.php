<?php

namespace App\Http\Livewire\Manager;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Livewire\Component;

class DashboardOverview extends Component
{
    public function getTodaySalesProperty(): float
    {
        return Transaction::whereDate('created_at', today())->sum('total');
    }

    public function getMonthSalesProperty(): float
    {
        return Transaction::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('total');
    }

    public function getTodayCountProperty(): int
    {
        return Transaction::whereDate('created_at', today())->count();
    }

    public function getMonthCountProperty(): int
    {
        return Transaction::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();
    }

    public function getLowStockProductsProperty()
    {
        return Product::lowStock()->orderBy('stock_qty')->get();
    }

    public function getTodayBestSellersProperty()
    {
        return TransactionItem::with('product')
            ->whereHas('transaction', fn($q) => $q->whereDate('created_at', today()))
            ->selectRaw('product_id, SUM(qty) as total_qty, SUM(line_total) as total_revenue')
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.manager.dashboard-overview')
            ->layout('layouts.manager');
    }
}
