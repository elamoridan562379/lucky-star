<div>
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1.5rem; flex-wrap:wrap; gap:1rem;">
        <h1 class="page-title" style="margin-bottom:0;">Products</h1>
        <button wire:click="openCreate" class="btn-primary">+ Add Product</button>
    </div>

    {{-- Filters --}}
    <div class="card" style="padding:1rem 1.25rem; margin-bottom:1.25rem; display:flex; flex-wrap:wrap; gap:0.75rem; align-items:center;">
        <div style="position:relative;">
            <svg style="position:absolute; left:0.7rem; top:50%; transform:translateY(-50%); width:14px; height:14px; color:#9a7a68;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search products..."
                class="form-input" style="padding-left:2.2rem; width:220px;">
        </div>
        <select wire:model.live="category" class="form-input" style="width:160px;">
            <option value="">All Categories</option>
            <option value="coffee">Coffee</option>
            <option value="iced_coffee">Iced Coffee</option>
            <option value="non_coffee">Non-Coffee</option>
        </select>
    </div>

    {{-- Table --}}
    <div class="card">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Category</th>
                    <th style="text-align:right;">Price</th>
                    <th style="text-align:right;">Stock</th>
                    <th style="text-align:center;">Status</th>
                    <th style="text-align:center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                <tr wire:key="prod-{{ $product->id }}">
                    <td>
                        <span style="font-weight:700; font-family:'Playfair Display',serif; font-size:0.88rem;">{{ $product->name }}</span>
                        @if ($product->isLowStock())
                            <span class="badge badge-low" style="margin-left:0.5rem;">Low</span>
                        @endif
                    </td>
                    <td style="color:#7a5c44; font-size:0.78rem;">{{ $product->category_label }}</td>
                    <td style="text-align:right; font-weight:700; color:#4a2518;">₱{{ number_format($product->selling_price, 2) }}</td>
                    <td style="text-align:right; font-weight:700; color:{{ $product->stock_qty == 0 ? '#c0392b' : ($product->isLowStock() ? '#c8811a' : '#3d2415') }};">
                        {{ $product->stock_qty }}
                    </td>
                    <td style="text-align:center;">
                        <button wire:click="toggleActive({{ $product->id }})"
                            class="badge {{ $product->is_active ? 'badge-active' : 'badge-inactive' }}"
                            style="cursor:pointer; border:none; transition:all 0.15s;">
                            {{ $product->is_active ? 'Active' : 'Inactive' }}
                        </button>
                    </td>
                    <td style="text-align:center;">
                        <div style="display:flex; align-items:center; justify-content:center; gap:1rem;">
                            <button wire:click="openEdit({{ $product->id }})" class="btn-link">Edit</button>
                            <button wire:click="confirmDeleteProduct({{ $product->id }})" class="btn-link" style="color:#c0392b;">Delete</button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center; padding:3rem; color:#c8b0a0; font-family:'Playfair Display',serif; font-style:italic;">
                        No products found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div style="padding:1rem 1.25rem; border-top:1px solid rgba(74,37,24,0.06);">
            {{ $products->links() }}
        </div>
    </div>

    {{-- Create / Edit Modal --}}
    @if ($showModal)
    <div class="modal-bg">
        <div class="modal-box">
            <div class="modal-header">
                <span class="modal-title">{{ $editId ? 'Edit Product' : 'New Product' }}</span>
                <button wire:click="$set('showModal', false)" class="modal-close">✕</button>
            </div>
            <div class="modal-body" style="display:flex; flex-direction:column; gap:1rem;">
                <div>
                    <label class="form-label">Product Name *</label>
                    <input wire:model="name" type="text" class="form-input @error('name') border-red-400 @enderror">
                    @error('name')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="form-label">Category *</label>
                    <select wire:model="formCategory" class="form-input">
                        <option value="coffee">Coffee</option>
                        <option value="iced_coffee">Iced Coffee</option>
                        <option value="non_coffee">Non-Coffee</option>
                    </select>
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:0.75rem;">
                    <div>
                        <label class="form-label">Selling Price *</label>
                        <input wire:model="sellingPrice" type="number" step="0.01" min="0" class="form-input">
                        @error('sellingPrice')<span class="form-error">{{ $message }}</span>@enderror
                    </div>
                    <div>
                        <label class="form-label">Cost Price</label>
                        <input wire:model="costPrice" type="number" step="0.01" min="0" class="form-input">
                    </div>
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:0.75rem;">
                    <div>
                        <label class="form-label">Stock Qty *</label>
                        <input wire:model="stockQty" type="number" min="0" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Reorder Level</label>
                        <input wire:model="reorderLevel" type="number" min="0" class="form-input">
                    </div>
                </div>
                <label style="display:flex; align-items:center; gap:0.6rem; cursor:pointer;">
                    <input wire:model="isActive" type="checkbox" style="accent-color:var(--caramel); width:16px; height:16px;">
                    <span style="font-size:0.82rem; color:#4a2518;">Active — visible in POS terminal</span>
                </label>
            </div>
            <div class="modal-footer">
                <button wire:click="$set('showModal', false)" class="btn-secondary">Cancel</button>
                <button wire:click="save" class="btn-primary">{{ $editId ? 'Update' : 'Create' }}</button>
            </div>
        </div>
    </div>
    @endif

    {{-- Delete Confirm --}}
    @if ($confirmDelete)
    <div class="modal-bg">
        <div class="modal-box" style="max-width:380px;">
            <div class="modal-header">
                <span class="modal-title">Delete Product?</span>
                <button wire:click="cancelDelete" class="modal-close">✕</button>
            </div>
            <div class="modal-body">
                <p style="font-size:0.85rem; color:#6a4a35; line-height:1.6;">This action cannot be undone. Products linked to past transactions cannot be deleted.</p>
            </div>
            <div class="modal-footer">
                <button wire:click="cancelDelete" class="btn-secondary">Cancel</button>
                <button wire:click="deleteProduct" class="btn-danger">Delete</button>
            </div>
        </div>
    </div>
    @endif
</div>
