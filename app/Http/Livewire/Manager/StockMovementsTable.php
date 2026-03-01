<?php

namespace App\Http\Livewire\Manager;

use App\Models\Product;
use App\Models\StockMovement;
use Livewire\Component;
use Livewire\WithPagination;

class StockMovementsTable extends Component
{
    use WithPagination;

    public string $dateFrom = '';
    public string $dateTo = '';
    public string $type = '';
    public string $reason = '';
    public ?int $productId = null;

    // ✅ when true, show empty table (after pressing Clear)
    public bool $cleared = false;

    // keep filters in URL
    protected $queryString = [
        'dateFrom',
        'dateTo',
        'type',
        'reason',
        'productId',
    ];

    public function mount(): void
    {
        // default values (if you want initial list visible)
        $this->dateFrom = now()->subDays(30)->format('Y-m-d');
        $this->dateTo   = now()->format('Y-m-d');
    }

    // If any filter changes, show results again
    public function updatingDateFrom(): void  { $this->cleared = false; $this->resetPage(); }
    public function updatingDateTo(): void    { $this->cleared = false; $this->resetPage(); }
    public function updatingType(): void      { $this->cleared = false; $this->resetPage(); }
    public function updatingReason(): void    { $this->cleared = false; $this->resetPage(); }
    public function updatingProductId(): void { $this->cleared = false; $this->resetPage(); }

    // ✅ Clear = blank inputs + empty table
    public function clearFilters(): void
    {
        $this->dateFrom  = '';
        $this->dateTo    = '';
        $this->type      = '';
        $this->reason    = '';
        $this->productId = null;

        $this->cleared = true;
        $this->resetPage();
    }

    public function render()
    {
        // ✅ If cleared, show no results
        if ($this->cleared) {
            $movements = StockMovement::query()
                ->whereRaw('1=0')
                ->paginate(25);

            $products = Product::orderBy('name')->get();

            return view('livewire.manager.stock-movements-table', [
                'movements' => $movements,
                'products'  => $products,
            ])->layout('layouts.manager');
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

        return view('livewire.manager.stock-movements-table', [
            'movements' => $movements,
            'products'  => $products,
        ])->layout('layouts.manager');
    }
}
