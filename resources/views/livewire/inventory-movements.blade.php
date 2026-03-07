<div>
    <!-- Header -->
    <div class="mb-6">
        <h1 class="page-title">Stock Movement Log</h1>
        <p class="text-gray-600">View and filter all inventory movements</p>
    </div>

    <!-- Filters -->
    <div class="card mb-6">
        <div class="card-header">
            <h2 class="card-title">🔍 Filters</h2>
        </div>
        <div class="p-6">
            <form wire:submit.prevent="applyFilters" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
                    <div>
                        <label class="form-label">Date From</label>
                        <input type="date" wire:model="dateFrom" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Date To</label>
                        <input type="date" wire:model="dateTo" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Type</label>
                        <select wire:model="type" class="form-input">
                            <option value="">All Types</option>
                            <option value="in">Stock In</option>
                            <option value="out">Stock Out</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Reason</label>
                        <select wire:model="reason" class="form-input">
                            <option value="">All Reasons</option>
                            <option value="restock">Restock</option>
                            <option value="sale">Sale</option>
                            <option value="wastage">Wastage</option>
                            <option value="adjustment">Adjustment</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Product</label>
                        <select wire:model="productId" class="form-input">
                            <option value="">All Products</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex gap-4 mt-6">
                    <button type="submit" class="btn-primary">Apply Filters</button>
                    <button type="button" wire:click="clearFilters" class="btn-secondary">Clear</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Movements Table -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">📋 Stock Movements ({{ $movements->total() }} records)</h2>
        </div>
        <div class="p-6">
            @if($movements->count() > 0)
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Date & Time</th>
                                <th>Product</th>
                                <th>Type</th>
                                <th>Quantity</th>
                                <th>Reason</th>
                                <th>Stock Before</th>
                                <th>Stock After</th>
                                <th>Recorded By</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($movements as $movement)
                                <tr>
                                    <td>{{ $movement->created_at->format('M j, Y g:i A') }}</td>
                                    <td class="font-medium">{{ $movement->product->name }}</td>
                                    <td>
                                        <span class="badge {{ $movement->type === 'in' ? 'badge-in' : 'badge-out' }}">
                                            {{ strtoupper($movement->type) }}
                                        </span>
                                    </td>
                                    <td class="{{ $movement->type === 'in' ? 'text-green-600 font-semibold' : 'text-red-600 font-semibold' }}">
                                        {{ $movement->type === 'in' ? '+' : '-' }}{{ $movement->qty }}
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $movement->reason === 'restock' ? 'active' : ($movement->reason === 'wastage' ? 'inactive' : 'low') }}">
                                            {{ ucfirst($movement->reason) }}
                                        </span>
                                    </td>
                                    <td>{{ $movement->stock_before ?? '-' }}</td>
                                    <td>{{ $movement->stock_after ?? '-' }}</td>
                                    <td>{{ $movement->createdBy->name }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $movements->links() }}
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    @if($cleared)
                        <p class="mb-2">🔍 Filters cleared</p>
                        <p class="text-sm">Apply filters to search for movements</p>
                    @else
                        <p class="mb-2">📭 No movements found</p>
                        <p class="text-sm">Try adjusting your filters</p>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
