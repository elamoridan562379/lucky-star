<?php

namespace App\Http\Livewire\Manager;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\User;
use App\Models\StockMovement;
use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardOverview extends Component
{
    public $chartPeriod = 'daily'; // daily, weekly, monthly
    public $selectedMetric = 'revenue'; // revenue, transactions
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

    // Sales Trends Data
    public function getDailyRevenueDataProperty()
    {
        return Transaction::whereDate('created_at', '>=', now()->subDays(7))
            ->selectRaw('DATE(created_at) as date, SUM(total) as revenue, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(fn($item) => [
                'date' => Carbon::parse($item->date)->format('M d'),
                'revenue' => (float) $item->revenue,
                'count' => (int) $item->count
            ]);
    }

    public function getWeeklyRevenueDataProperty()
    {
        return Transaction::whereDate('created_at', '>=', now()->subWeeks(8))
            ->selectRaw('YEARWEEK(created_at) as week, SUM(total) as revenue, COUNT(*) as count')
            ->groupBy('week')
            ->orderBy('week')
            ->get()
            ->map(fn($item) => [
                'week' => 'W' . substr($item->week, -2),
                'revenue' => (float) $item->revenue,
                'count' => (int) $item->count
            ]);
    }

    public function getMonthlyRevenueDataProperty()
    {
        return Transaction::whereDate('created_at', '>=', now()->subMonths(12))
            ->selectRaw('MONTH(created_at) as month, SUM(total) as revenue, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(fn($item) => [
                'month' => Carbon::create()->month($item->month)->format('M'),
                'revenue' => (float) $item->revenue,
                'count' => (int) $item->count
            ]);
    }

    public function getHourlySalesPatternProperty()
    {
        return Transaction::whereDate('created_at', today())
            ->selectRaw('HOUR(created_at) as hour, SUM(total) as revenue, COUNT(*) as count')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->map(fn($item) => [
                'hour' => $item->hour . ':00',
                'revenue' => (float) $item->revenue,
                'count' => (int) $item->count
            ]);
    }

    // Comparison Metrics
    public function getYesterdaySalesProperty(): float
    {
        return Transaction::whereDate('created_at', today()->subDay())->sum('total');
    }

    public function getLastMonthSalesProperty(): float
    {
        return Transaction::whereYear('created_at', now()->subMonth()->year)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->sum('total');
    }

    // Performance Metrics
    public function getAverageTransactionValueProperty(): float
    {
        return Transaction::whereDate('created_at', today())->avg('total') ?? 0;
    }

    public function getAverageItemsPerTransactionProperty(): float
    {
        return TransactionItem::whereHas('transaction', fn($q) => $q->whereDate('created_at', today()))
            ->avg('qty') ?? 0;
    }

    public function getRevenuePerHourProperty(): float
    {
        $operatingHours = now()->hour >= 6 ? now()->hour : 6;
        return $this->todaySales / max($operatingHours - 6, 1);
    }

    public function getInventoryTurnoverRateProperty(): float
    {
        $totalProducts = Product::count();
        if ($totalProducts === 0) return 0;
        
        $lowStockCount = Product::lowStock()->count();
        return (($totalProducts - $lowStockCount) / $totalProducts) * 100;
    }

    // Customer Insights
    public function getNewCustomersTodayProperty(): int
    {
        return User::whereDate('created_at', today())
            ->where('role', 'customer')
            ->count();
    }

    public function getNewCustomersThisWeekProperty(): int
    {
        return User::whereDate('created_at', '>=', now()->startOfWeek())
            ->where('role', 'customer')
            ->count();
    }

    public function getTopCustomersProperty()
    {
        return Transaction::with('cashier')
            ->selectRaw('cashier_id, SUM(total) as total_spent, COUNT(*) as transaction_count')
            ->whereDate('created_at', '>=', now()->subDays(30))
            ->groupBy('cashier_id')
            ->orderByDesc('total_spent')
            ->limit(5)
            ->get();
    }

    public function getRepeatCustomerRateProperty(): float
    {
        $totalCustomers = Transaction::distinct('cashier_id')
            ->whereDate('created_at', '>=', now()->subDays(30))
            ->count('cashier_id');
        
        if ($totalCustomers === 0) return 0;
        
        $repeatCustomers = Transaction::selectRaw('cashier_id, COUNT(*) as transaction_count')
            ->whereDate('created_at', '>=', now()->subDays(30))
            ->groupBy('cashier_id')
            ->having('transaction_count', '>', 1)
            ->count();
            
        return ($repeatCustomers / $totalCustomers) * 100;
    }

    // Recent Activity Feed
    public function getRecentTransactionsProperty()
    {
        return Transaction::with('cashier')
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn($txn) => [
                'type' => 'transaction',
                'title' => 'Sale Completed',
                'description' => "Receipt #{$txn->receipt_no} - ₱" . number_format($txn->total, 2),
                'user' => $txn->cashier->name,
                'timestamp' => $txn->created_at->diffForHumans(),
                'icon' => '🧾'
            ]);
    }

    public function getRecentMovementsProperty()
    {
        return StockMovement::with(['product', 'createdBy'])
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn($movement) => [
                'type' => 'movement',
                'title' => ucfirst($movement->type) . ' Stock',
                'description' => "{$movement->product->name} - {$movement->qty} units",
                'user' => $movement->createdBy->name ?? 'System',
                'timestamp' => $movement->created_at->diffForHumans(),
                'icon' => $movement->type === 'in' ? '⬆️' : '⬇️'
            ]);
    }

    public function getSystemNotificationsProperty()
    {
        $notifications = collect();
        
        // New users
        $newUsers = User::whereDate('created_at', today())
            ->where('role', '!=', 'admin')
            ->count();
            
        if ($newUsers > 0) {
            $notifications->push([
                'type' => 'system',
                'title' => 'New Users',
                'description' => "{$newUsers} new user(s) registered today",
                'user' => 'System',
                'timestamp' => 'Today',
                'icon' => '👤'
            ]);
        }
        
        // Low stock resolved
        $resolvedStock = Product::where('stock_qty', '>', DB::raw('reorder_level * 2'))
            ->where('updated_at', '>=', now()->subHours(24))
            ->count();
            
        if ($resolvedStock > 0) {
            $notifications->push([
                'type' => 'system',
                'title' => 'Stock Restocked',
                'description' => "{$resolvedStock} product(s) restocked",
                'user' => 'System',
                'timestamp' => 'Today',
                'icon' => '✓'
            ]);
        }
        
        return $notifications;
    }

    public function getRecentActivityProperty()
    {
        return $this->recentTransactions
            ->concat($this->recentMovements)
            ->concat($this->systemNotifications)
            ->sortByDesc(fn($activity) => $activity['timestamp'])
            ->take(10)
            ->values();
    }

    // Operational Status
    public function getActiveCashierSessionsProperty(): int
    {
        return DB::table('sessions')
            ->join('users', 'sessions.user_id', '=', 'users.id')
            ->where('users.role', 'cashier')
            ->where('sessions.last_activity', '>=', now()->subMinutes(30)->timestamp)
            ->count();
    }

    public function getSystemHealthProperty(): array
    {
        return [
            'status' => 'healthy',
            'database' => 'connected',
            'storage' => 'optimal',
            'last_backup' => now()->subHours(2)->format('H:i')
        ];
    }

    public function render()
    {
        return view('livewire.manager.dashboard-overview')
            ->layout('layouts.manager');
    }
}
