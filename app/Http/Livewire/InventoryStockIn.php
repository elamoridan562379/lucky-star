<?php

namespace App\Http\Livewire;

use App\Models\Product;
use App\Models\StockMovement;
use App\Services\InventoryService;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithPagination;

class InventoryStockIn extends Component
{
    use WithPagination;

    // ── Form ───────────────────────────────────────────────
    public ?int    $productId   = null;
    public int     $qty         = 1;
    public string  $receivedAt  = '';
    public string  $supplier    = '';
    public string  $remarks     = '';
    public ?float  $unitCost    = null;
    public string  $successMsg  = '';
    public ?string $errorMsg    = null;

    // ── Manual stock-out form ──────────────────────────────
    public bool    $showOutForm  = false;
    public ?int    $outProductId = null;
    public int     $outQty       = 1;
    public string  $outReason    = 'wastage';
    public string  $outNotes     = '';
    public ?string $outErrorMsg  = null;

    protected InventoryService $inventoryService;

    public function boot(InventoryService $inventoryService): void
    {
        $this->inventoryService = $inventoryService;
    }

    public function mount(): void
    {
        $this->receivedAt = now()->format('Y-m-d');
    }

    protected function rulesIn(): array
    {
        return [
            'productId'  => 'required|exists:products,id',
            'qty'        => 'required|integer|min:1',
            'receivedAt' => 'required|date',
            'supplier'   => 'nullable|string|max:200',
            'remarks'    => 'nullable|string|max:500',
            'unitCost'   => 'nullable|numeric|min:0',
        ];
    }

    protected function rulesOut(): array
    {
        return [
            'outProductId' => 'required|exists:products,id',
            'outQty'       => 'required|integer|min:1',
            'outReason'    => 'required|in:wastage,adjustment',
            'outNotes'     => 'nullable|string|max:500',
        ];
    }

    // ── Stock In ───────────────────────────────────────────
    public function processStockIn(): void
    {
        $this->validate($this->rulesIn());
        $this->errorMsg = null;

        try {
            $this->inventoryService->stockIn(
                productId:  $this->productId,
                qty:        $this->qty,
                reason:     'restock',
                notes:      $this->remarks,
                createdBy:  auth()->id(),
                supplier:   $this->supplier ?: null,
                receivedAt: $this->receivedAt,
                unitCost:   $this->unitCost,
            );

            $product          = Product::find($this->productId);
            $this->successMsg = "Stock updated! {$product->name} now has {$product->fresh()->stock_qty} units.";
            $this->resetInForm();
        } catch (ValidationException $e) {
            $this->errorMsg = collect($e->errors())->flatten()->implode(' ');
        }
    }

    // ── Manual Stock Out ───────────────────────────────────
    public function processStockOut(): void
    {
        $this->validate($this->rulesOut());
        $this->outErrorMsg = null;

        try {
            $this->inventoryService->manualStockOut(
                productId: $this->outProductId,
                qty:       $this->outQty,
                reason:    $this->outReason,
                notes:     $this->outNotes,
                createdBy: auth()->id(),
            );

            $product          = Product::find($this->outProductId);
            $this->successMsg = "Manual stock-out recorded. {$product->name} now has {$product->fresh()->stock_qty} units.";
            $this->showOutForm   = false;
            $this->resetOutForm();
        } catch (ValidationException $e) {
            $this->outErrorMsg = collect($e->errors())->flatten()->implode(' ');
        }
    }

    private function resetInForm(): void
    {
        $this->productId  = null;
        $this->qty        = 1;
        $this->supplier   = '';
        $this->remarks    = '';
        $this->unitCost   = null;
        $this->receivedAt = now()->format('Y-m-d');
        $this->resetValidation();
    }

    private function resetOutForm(): void
    {
        $this->outProductId = null;
        $this->outQty       = 1;
        $this->outReason    = 'wastage';
        $this->outNotes     = '';
        $this->resetValidation();
    }

    public function render()
    {
        $products         = Product::active()->orderBy('name')->get();
        $recentMovements  = StockMovement::with(['product', 'createdBy'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $layout = in_array(auth()->user()->role, ['admin', 'manager']) ? 'layouts.manager' : 'layouts.inventory-clerk';

        return view('livewire.inventory-stock-in', [
            'products'        => $products,
            'recentMovements' => $recentMovements,
        ])->layout($layout);
    }
}
