<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\StockMovement;
use Illuminate\Support\Collection;

class DashboardService
{
    public function getWeeklyRevenueData(): array
    {
        $data = Transaction::selectRaw('
                DATE(created_at) as date,
                SUM(total) as revenue,
                COUNT(*) as transactions
            ')
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->limit(7) // Add limit to prevent infinite loops
            ->get();

        $labels = [];
        $revenue = [];
        $transactions = [];

        // Fill in missing days with zero values
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $dayData = $data->firstWhere('date', $date);
            
            $labels[] = now()->subDays($i)->format('D, M j');
            $revenue[] = $dayData ? (float) $dayData->revenue : 0;
            $transactions[] = $dayData ? (int) $dayData->transactions : 0;
        }

        return [
            'labels' => $labels,
            'revenue' => $revenue,
            'transactions' => $transactions
        ];
    }

    public function getSalesByCategoryData(): array
    {
        $data = TransactionItem::with('product')
            ->whereHas('transaction', fn($q) => $q->whereDate('created_at', today()))
            ->selectRaw('
                products.category,
                SUM(transaction_items.qty) as total_qty
            ')
            ->join('products', 'transaction_items.product_id', '=', 'products.id')
            ->groupBy('products.category')
            ->orderByDesc('total_qty')
            ->limit(10) // Add limit to prevent performance issues
            ->get();

        $labels = [];
        $data_points = [];
        $colors = [
            'Coffee' => '#6F4E37',
            'Iced_coffee' => '#D2B48C', 
            'Non_coffee' => '#8B9E5A',
            'Pastries' => '#E6A85C',
            'Bottled' => '#4A90E2',
            'Other' => '#9B59B6'
        ];

        foreach ($data as $item) {
            $labels[] = ucfirst($item->category);
            $data_points[] = (int) $item->total_qty;
        }

        return [
            'labels' => $labels,
            'data' => $data_points,
            'colors' => array_intersect_key($colors, array_flip($labels))
        ];
    }

    public function getRecentActivities(): Collection
    {
        $activities = collect();
        
        // Recent sales (last 5)
        $recentSales = Transaction::with('items.product')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($transaction) {
                $firstItem = $transaction->items->first();
                return [
                    'type' => 'sale',
                    'description' => $firstItem ? $firstItem->product->name : 'Sale',
                    'details' => '₱' . number_format($transaction->total, 2),
                    'time' => $transaction->created_at->diffForHumans(),
                    'icon' => '💰'
                ];
            });

        // Recent stock movements (last 3)
        $recentMovements = StockMovement::with('product')
            ->latest()
            ->take(3)
            ->get()
            ->map(function ($movement) {
                return [
                    'type' => 'stock',
                    'description' => $movement->product->name,
                    'details' => ucfirst($movement->type) . ': ' . $movement->quantity . ' units',
                    'time' => $movement->created_at->diffForHumans(),
                    'icon' => $movement->type === 'in' ? '📥' : '📤'
                ];
            });

        return $recentSales->merge($recentMovements)->sortByDesc('time')->values();
    }

    public function getTodayBestSellers(): Collection
    {
        return TransactionItem::with('product')
            ->whereHas('transaction', fn($q) => $q->whereDate('created_at', today()))
            ->selectRaw('
                product_id,
                SUM(qty) as total_qty,
                SUM(line_total) as total_revenue
            ')
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->limit(10) // Add limit to prevent performance issues
            ->get();
    }

    public function getLowStockProducts(): Collection
    {
        return Product::lowStock()->orderBy('stock_qty')->limit(20)->get();
    }

    public function getTodayStats(): array
    {
        $todayTransactions = Transaction::whereDate('created_at', today());
        
        return [
            'revenue' => (float) $todayTransactions->sum('total'),
            'count' => $todayTransactions->count(),
            'top_seller' => $this->getTodayBestSellers()->first()
        ];
    }

    public function getMonthStats(): array
    {
        $monthTransactions = Transaction::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month);
            
        return [
            'revenue' => (float) $monthTransactions->sum('total'),
            'count' => $monthTransactions->count()
        ];
    }
}
