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
    public bool $showCreateModal = false;

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'type' => 'required|in:in,out',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|string|max:255',
            'product_id' => 'required|exists:products,id',
        ]);

        StockMovement::create([
            'type' => $this->type,
            'quantity' => $this->quantity,
            'reason' => $this->reason,
            'product_id' => $this->productId,
            'created_by' => auth()->id(),
        ]);

        session()->flash('success', 'Stock movement recorded successfully.');
        $this->showCreateModal = false;
        $this->resetForm();
    }

    public function resetForm(): void
    {
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->type = '';
        $this->reason = '';
        $this->productId = null;
        $this->quantity = '';
        $this->cleared = false;
        $this->resetValidation();
    }

    // ✅ when true, show empty table (after pressing Clear)
    public bool $cleared = false;

    protected $queryString = [
        'dateFrom',
        'dateTo',
        'type',
        'reason',
        'productId',
    ];

    protected function rules(): array
    {
        return [
            'type' => 'required|in:in,out',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|string|max:255',
            'product_id' => 'required|exists:products,id',
        ];
    }

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
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->type = '';
        $this->reason = '';
        $this->productId = null;
        $this->quantity = '';
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

            $layout = auth()->user()->role === 'inventory_clerk' ? 'layouts.inventory-clerk' : 'layouts.manager';

            return view('livewire.manager.stock-movements-table', [
                'movements' => $movements,
                'products'  => $products,
            ])->layout($layout);
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

        $layout = auth()->user()->role === 'inventory_clerk' ? 'layouts.inventory-clerk' : 'layouts.manager';

        return view('livewire.manager.stock-movements-table', [
            'movements' => $movements,
            'products'  => $products,
        ])->layout($layout);
    }
}
