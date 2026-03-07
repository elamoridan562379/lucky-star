<?php

namespace App\Http\Livewire;

use App\Models\Product;
use App\Models\StockMovement;
use Livewire\Component;
use Livewire\WithPagination;

class InventoryDashboard extends Component
{
    use WithPagination;

    public string $filter = 'all';
    public string $category = 'all';
    public int $criticalCount = 0;
    public int $lowStockCount = 0;
    public int $totalProducts = 0;

    protected $queryString = [
        'filter' => ['except' => 'all'],
        'category' => ['except' => 'all'],
    ];

    public function mount(): void
    {
        $this->refreshStats();
    }

    public function refreshStats(): void
    {
        $this->criticalCount = Product::criticalStock()->count();
        $this->lowStockCount = Product::lowStock()->count();
        $this->totalProducts = Product::active()->count();
    }

    public function getProductsProperty()
    {
        $query = Product::active()->with(['stockMovements' => function ($q) {
            $q->latest()->limit(1);
        }]);

        // Apply filter
        match ($this->filter) {
            'critical' => $query->criticalStock(),
            'low' => $query->lowStock(),
            'normal' => $query->whereRaw('stock_qty > low_stock_threshold'),
            default => null,
        };

        // Apply category filter
        if ($this->category !== 'all') {
            $query->byCategory($this->category);
        }

        return $query->orderBy('category')->orderBy('name')->paginate(12);
    }

    public function getCategoriesProperty()
    {
        return Product::active()
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');
    }

    public function getRecentMovementsProperty()
    {
        return StockMovement::with(['product', 'createdBy'])
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();
    }

    public function render()
    {
        return view('livewire.inventory-dashboard', [
            'products' => $this->products,
            'categories' => $this->categories,
            'recentMovements' => $this->recentMovements,
        ])->layout('layouts.inventory-clerk');
    }
}
