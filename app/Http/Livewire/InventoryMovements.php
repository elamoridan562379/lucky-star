<?php

namespace App\Http\Livewire;

use App\Models\Product;
use App\Models\StockMovement;
use Livewire\Component;
use Livewire\WithPagination;

class InventoryMovements extends Component
{
    use WithPagination;

    public string $dateFrom   = '';
    public string $dateTo     = '';
    public string $type       = '';
    public string $reason     = '';
    public ?int   $productId  = null;
    public bool   $cleared    = false;

    protected $queryString = [
        'dateFrom'   => ['except' => ''],
        'dateTo'     => ['except' => ''],
        'type'       => ['except' => ''],
        'reason'     => ['except' => ''],
        'productId'  => ['except' => ''],
    ];

    public function clearFilters(): void
    {
        $this->dateFrom   = '';
        $this->dateTo     = '';
        $this->type       = '';
        $this->reason     = '';
        $this->productId  = null;

        $this->cleared = true;
        $this->resetPage();
    }

    public function applyFilters(): void
    {
        $this->cleared = false;
        $this->resetPage();
    }

    public function render()
    {
        // If cleared, show no results
        if ($this->cleared) {
            $movements = StockMovement::query()
                ->whereRaw('1=0')
                ->paginate(25);

            $products = Product::orderBy('name')->get();

            return view('livewire.inventory-movements', [
                'movements' => $movements,
                'products'  => $products,
            ])->layout('layouts.inventory-clerk');
        }

        $movements = StockMovement::with(['product', 'createdBy'])
            ->when($this->dateFrom !== '', fn($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo !== '',   fn($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->when($this->type !== '',     fn($q) => $q->where('type', $this->type))
            ->when($this->reason !== '',   fn($q) => $q->where('reason', $this->reason))
            ->when(!is_null($this->productId), fn($q) => $q->where('product_id', $this->productId))
            ->orderByDesc('created_at')
            ->paginate(25);

        $products = Product::orderBy('name')->get();

        return view('livewire.inventory-movements', [
            'movements' => $movements,
            'products'  => $products,
        ])->layout('layouts.inventory-clerk');
    }
}
