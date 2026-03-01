<?php

namespace App\Http\Livewire\Manager;

use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

class ProductManager extends Component
{
    use WithPagination;

    // ── List state ─────────────────────────────────────────
    public string $search   = '';
    public string $category = '';

    // ── Modal state ────────────────────────────────────────
    public bool    $showModal    = false;
    public bool    $confirmDelete = false;
    public ?int    $deleteId     = null;
    public ?int    $editId       = null;

    // ── Form fields ────────────────────────────────────────
    public string $name          = '';
    public string $formCategory  = 'coffee';
    public string $sellingPrice  = '';
    public string $costPrice     = '';
    public int    $stockQty      = 0;
    public int    $reorderLevel  = 5;
    public bool   $isActive      = true;

    protected $queryString = ['search', 'category'];

    protected function rules(): array
    {
        return [
            'name'         => 'required|string|max:200',
            'formCategory' => 'required|in:coffee,iced_coffee,non_coffee',
            'sellingPrice' => 'required|numeric|min:0.01',
            'costPrice'    => 'nullable|numeric|min:0',
            'stockQty'     => 'required|integer|min:0',
            'reorderLevel' => 'required|integer|min:0',
            'isActive'     => 'boolean',
        ];
    }

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingCategory(): void { $this->resetPage(); }

    // ── Open create modal ──────────────────────────────────
    public function openCreate(): void
    {
        $this->resetForm();
        $this->editId    = null;
        $this->showModal = true;
    }

    // ── Open edit modal ────────────────────────────────────
    public function openEdit(int $id): void
    {
        $product = Product::findOrFail($id);
        $this->editId       = $id;
        $this->name         = $product->name;
        $this->formCategory = $product->category;
        $this->sellingPrice = $product->selling_price;
        $this->costPrice    = $product->cost_price ?? '';
        $this->stockQty     = $product->stock_qty;
        $this->reorderLevel = $product->reorder_level;
        $this->isActive     = $product->is_active;
        $this->showModal    = true;
    }

    // ── Save (create or update) ───────────────────────────
    public function save(): void
    {
        $this->validate();

        $data = [
            'name'          => $this->name,
            'category'      => $this->formCategory,
            'selling_price' => $this->sellingPrice,
            'cost_price'    => $this->costPrice ?: null,
            'stock_qty'     => $this->stockQty,
            'reorder_level' => $this->reorderLevel,
            'is_active'     => $this->isActive,
        ];

        if ($this->editId) {
            Product::findOrFail($this->editId)->update($data);
            session()->flash('success', 'Product updated successfully.');
        } else {
            Product::create($data);
            session()->flash('success', 'Product created successfully.');
        }

        $this->showModal = false;
        $this->resetForm();

        // Tell PosTerminal to refresh its product list
        $this->dispatch('productUpdated');
    }

    // ── Toggle active ──────────────────────────────────────
    public function toggleActive(int $id): void
    {
        $product = Product::findOrFail($id);
        $product->update(['is_active' => ! $product->is_active]);
        $this->dispatch('productUpdated');
    }

    // ── Delete ─────────────────────────────────────────────
    public function confirmDeleteProduct(int $id): void
    {
        $this->deleteId      = $id;
        $this->confirmDelete = true;
    }

    public function deleteProduct(): void
    {
        Product::findOrFail($this->deleteId)->delete();
        $this->confirmDelete = false;
        $this->deleteId      = null;
        session()->flash('success', 'Product deleted.');
        $this->dispatch('productUpdated');
    }

    public function cancelDelete(): void
    {
        $this->confirmDelete = false;
        $this->deleteId      = null;
    }

    private function resetForm(): void
    {
        $this->name         = '';
        $this->formCategory = 'coffee';
        $this->sellingPrice = '';
        $this->costPrice    = '';
        $this->stockQty     = 0;
        $this->reorderLevel = 5;
        $this->isActive     = true;
        $this->resetValidation();
    }

    public function render()
    {
        $products = Product::when($this->search,   fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->when($this->category, fn($q) => $q->where('category', $this->category))
            ->orderBy('category')->orderBy('name')
            ->paginate(15);

        return view('livewire.manager.product-manager', ['products' => $products])
            ->layout('layouts.manager');
    }
}
