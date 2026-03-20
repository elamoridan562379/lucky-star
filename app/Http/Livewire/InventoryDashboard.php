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
<<<<<<< HEAD
    public int $criticalCount = 0;
    public int $lowStockCount = 0;
    public int $totalProducts = 0;
=======
    public string $search = '';
    public int $criticalCount = 0;
    public int $lowStockCount = 0;
    public int $totalProducts = 0;
    public float $totalValue = 0;
>>>>>>> 17e9c9617d7de32f80264abdd22516d36dfc6413

    protected $queryString = [
        'filter' => ['except' => 'all'],
        'category' => ['except' => 'all'],
<<<<<<< HEAD
=======
        'search' => ['except' => ''],
>>>>>>> 17e9c9617d7de32f80264abdd22516d36dfc6413
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
<<<<<<< HEAD
=======
        
        $products = Product::active()->get(['stock_qty', 'cost_price']);
        $this->totalValue = $products->sum(function($product) {
            return $product->stock_qty * ($product->cost_price ?? 0);
        });
>>>>>>> 17e9c9617d7de32f80264abdd22516d36dfc6413
    }

    public function getProductsProperty()
    {
        $query = Product::active()->with(['stockMovements' => function ($q) {
            $q->latest()->limit(1);
        }]);

<<<<<<< HEAD
=======
        // Apply search
        if (!empty($this->search)) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

>>>>>>> 17e9c9617d7de32f80264abdd22516d36dfc6413
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

<<<<<<< HEAD
=======
    public function quickStockIn($productId, $qty = 10): void
    {
        // Validate input
        if (!$productId || !$qty) return;
        
        $product = Product::find($productId);
        if (!$product) {
            session()->flash('error', 'Product not found');
            return;
        }

        // Create stock movement for this specific product only
        StockMovement::create([
            'product_id' => (int)$productId,
            'type' => 'in',
            'qty' => (int)$qty,
            'reason' => 'restock',
            'created_by' => auth()->id(),
        ]);

        // Update only this specific product's stock
        $product->stock_qty += (int)$qty;
        $product->save();

        // Refresh stats and products
        $this->refreshStats();
        
        // Dispatch event for UI update
        $this->dispatch('stockUpdated', [
            'productId' => (int)$productId,
            'newStock' => $product->stock_qty,
            'message' => "Added {$qty} units to {$product->name}"
        ]);
    }

    public function getStockPercentage($product): float
    {
        if ($product->low_stock_threshold <= 0) return 100;
        return min(100, ($product->stock_qty / $product->low_stock_threshold) * 100);
    }

>>>>>>> 17e9c9617d7de32f80264abdd22516d36dfc6413
    public function render()
    {
        $layout = in_array(auth()->user()->role, ['admin', 'manager']) ? 'layouts.manager' : 'layouts.inventory-clerk';

        return view('livewire.inventory-dashboard', [
            'products' => $this->products,
            'categories' => $this->categories,
            'recentMovements' => $this->recentMovements,
<<<<<<< HEAD
=======
            'totalValue' => $this->totalValue,
>>>>>>> 17e9c9617d7de32f80264abdd22516d36dfc6413
        ])->layout($layout);
    }
}
