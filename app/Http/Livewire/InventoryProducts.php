<?php

namespace App\Http\Livewire;

use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

class InventoryProducts extends Component
{
    use WithPagination;

    public string $search    = '';
    public string $category  = '';
    public string $status   = '';

    protected $queryString = [
        'search'   => ['except' => ''],
        'category' => ['except' => ''],
        'status'   => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCategory()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function getCategoriesProperty()
    {
        return Product::active()
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');
    }

    public function render()
    {
        $products = Product::when($this->search,   fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->when($this->category, fn($q) => $q->where('category', $this->category))
            ->when($this->status, function($q) {
                match($this->status) {
                    'critical' => $q->criticalStock(),
                    'low' => $q->lowStock(),
                    'normal' => $q->whereRaw('stock_qty > low_stock_threshold'),
                    default => null,
                };
            })
            ->orderBy('category')
            ->orderBy('name')
            ->paginate(20);

        return view('livewire.inventory-products', [
            'products' => $products,
            'categories' => $this->categories,
        ])->layout('layouts.inventory-clerk');
    }
}
