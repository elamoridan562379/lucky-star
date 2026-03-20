<div>
    <!-- Header -->
    <div class="mb-6">
        <h1 class="page-title">Stock Management</h1>
        <p class="text-gray-600">Manage stock levels, track movements, and monitor inventory</p>
    </div>

    <!-- Success/Error Messages -->
    @if ($successMsg)
        <div class="alert-success">✓ {{ $successMsg }}</div>
    @endif
    @if ($errorMsg)
        <div class="alert-error">{{ $errorMsg }}</div>
    @endif

    <!-- Stock In Form -->
    <div class="card mb-6">
        <div class="card-header">
            <h2 class="card-title">📦 Stock In - Receive New Inventory</h2>
        </div>
        <div class="p-6">
            <form wire:submit="processStockIn" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Product *</label>
                        <select wire:model="productId" class="form-input">
                            <option value="">Select a product...</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }} (Current: {{ $product->stock_qty }})</option>
                            @endforeach
                        </select>
                        @error('productId') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="form-label">Quantity *</label>
                        <input type="number" wire:model="qty" min="1" class="form-input" placeholder="Enter quantity">
                        @error('qty') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="form-label">Date Received *</label>
                        <input type="date" wire:model="receivedAt" class="form-input">
                        @error('receivedAt') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="form-label">Supplier</label>
                        <input type="text" wire:model="supplier" class="form-input" placeholder="Supplier name (optional)">
                        @error('supplier') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="form-label">Unit Cost</label>
                        <input type="number" wire:model="unitCost" step="0.01" min="0" class="form-input" placeholder="₱0.00">
                        @error('unitCost') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="form-label">Remarks</label>
                        <input type="text" wire:model="remarks" class="form-input" placeholder="Notes about this stock in">
                        @error('remarks') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="flex justify-end mt-6">
                    <button type="submit" class="btn-primary">
                        Process Stock In
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Manual Stock Out -->
    <div class="card mb-6">
        <div class="card-header py-4">
            <div class="flex items-center gap-4">
                <h2 class="card-title flex-shrink-0">📤 Manual Stock Out</h2>
                <button wire:click="$toggle('showOutForm')" class="btn-secondary flex-shrink-0">
                    {{ $showOutForm ? 'Cancel' : 'Record Stock Out' }}
                </button>
            </div>
        </div>
        @if ($showOutForm)
            <div class="p-6 border-t">
                <form wire:submit="processStockOut" class="space-y-4">
                    @if ($outErrorMsg)
                        <div class="alert-error">{{ $outErrorMsg }}</div>
                    @endif
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Product *</label>
                            <select wire:model="outProductId" class="form-input">
                                <option value="">Select a product...</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }} (Current: {{ $product->stock_qty }})</option>
                                @endforeach
                            </select>
                            @error('outProductId') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label class="form-label">Quantity *</label>
                            <input type="number" wire:model="outQty" min="1" class="form-input" placeholder="Enter quantity">
                            @error('outQty') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label class="form-label">Reason *</label>
                            <select wire:model="outReason" class="form-input">
                                <option value="wastage">Wastage/Damage</option>
                                <option value="adjustment">Inventory Adjustment</option>
                            </select>
                            @error('outReason') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label class="form-label">Notes</label>
                            <input type="text" wire:model="outNotes" class="form-input" placeholder="Additional notes">
                            @error('outNotes') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 mt-6">
                        <button type="button" wire:click="resetOutForm" class="btn-secondary">Clear</button>
                        <button type="submit" class="btn-danger">Process Stock Out</button>
                    </div>
                </form>
            </div>
        @endif
    </div>

    <!-- Recent Movements -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">📋 Recent Stock Movements</h2>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Type</th>
                            <th>Quantity</th>
                            <th>Reason</th>
                            <th>By</th>
                            <th>When</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentMovements as $movement)
                            <tr>
                                <td class="font-medium">{{ $movement->product->name }}</td>
                                <td>
                                    <span class="badge {{ $movement->type === 'in' ? 'badge-in' : 'badge-out' }}">
                                        {{ strtoupper($movement->type) }}
                                    </span>
                                </td>
                                <td class="{{ $movement->type === 'in' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $movement->type === 'in' ? '+' : '-' }}{{ $movement->qty }}
                                </td>
                                <td>{{ $movement->reason }}</td>
                                <td>{{ $movement->createdBy->name }}</td>
                                <td>{{ $movement->created_at->format('M j, Y g:i A') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
