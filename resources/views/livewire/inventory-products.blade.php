<div>
    <!-- Header -->
    <div class="mb-6">
        <h1 class="page-title">Product Catalog</h1>
        <p class="text-gray-600">View and manage product information</p>
    </div>

    <!-- Filters -->
    <div class="card mb-6">
        <div class="card-header">
            <h2 class="card-title">🔍 Search & Filter</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="form-label">Search Products</label>
                    <input type="text" wire:model.live="search" class="form-input" placeholder="Search by name...">
                </div>
                <div>
                    <label class="form-label">Category</label>
                    <select wire:model="category" class="form-input">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}">{{ ucfirst($cat) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Stock Status</label>
                    <select wire:model="status" class="form-input">
                        <option value="">All Status</option>
                        <option value="critical">Critical Only</option>
                        <option value="low">Low Stock Only</option>
                        <option value="normal">Normal Stock Only</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button wire:click="$set('search', ''); $set('category', ''); $set('status', '')" class="btn-secondary w-full">
                        Clear Filters
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        @foreach($products as $product)
            <div class="card border-l-4 border-{{ $product->stock_status_color }}-500">
                <div class="p-4">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <h3 class="font-semibold text-gray-900 text-lg">{{ $product->name }}</h3>
                            <span class="badge badge-{{ $product->category === 'coffee' ? 'active' : 'low' }} text-xs mt-1">
                                {{ $product->category_label }}
                            </span>
                        </div>
                        <span class="px-2 py-1 text-xs rounded-full bg-{{ $product->stock_status_color }}-100 text-{{ $product->stock_status_color }}-800 font-semibold">
                            {{ ucfirst($product->stock_status) }}
                        </span>
                    </div>
                    
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between py-1 border-b">
                            <span class="text-gray-600">Current Stock:</span>
                            <span class="font-bold {{ $product->stock_status === 'critical' ? 'text-red-600' : ($product->stock_status === 'low' ? 'text-yellow-600' : 'text-green-600') }}">
                                {{ $product->stock_qty }} units
                            </span>
                        </div>
                        <div class="flex justify-between py-1 border-b">
                            <span class="text-gray-600">Threshold:</span>
                            <span class="font-medium">{{ $product->low_stock_threshold }} units</span>
                        </div>
                        <div class="flex justify-between py-1 border-b">
                            <span class="text-gray-600">Selling Price:</span>
                            <span class="font-semibold">₱{{ number_format($product->selling_price, 2) }}</span>
                        </div>
                        <div class="flex justify-between py-1">
                            <span class="text-gray-600">Cost Price:</span>
                            <span class="font-medium">₱{{ number_format($product->cost_price, 2) }}</span>
                        </div>
                    </div>

                    @if($product->stock_status === 'critical')
                        <div class="mt-3 p-2 bg-red-50 rounded-lg text-xs text-red-700 border border-red-200">
                            <strong>⚠️ Critical Stock:</strong> Below 50% of threshold. Restock urgently!
                        </div>
                    @elseif($product->stock_status === 'low')
                        <div class="mt-3 p-2 bg-yellow-50 rounded-lg text-xs text-yellow-700 border border-yellow-200">
                            <strong>⚠️ Low Stock:</strong> Consider restocking soon.
                        </div>
                    @else
                        <div class="mt-3 p-2 bg-green-50 rounded-lg text-xs text-green-700 border border-green-200">
                            <strong>✓ Good Stock:</strong> Adequate inventory level.
                        </div>
                    @endif

                    <div class="mt-3 pt-3 border-t flex justify-between items-center text-xs text-gray-500">
                        <span>Reorder Level: {{ $product->reorder_level }}</span>
                        <span class="badge {{ $product->is_active ? 'badge-active' : 'badge-inactive' }}">
                            {{ $product->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Pagination -->
    @if($products->hasPages())
        <div class="card">
            <div class="p-4">
                {{ $products->links() }}
            </div>
        </div>
    @endif

    <!-- No Results -->
    @if($products->count() === 0)
        <div class="card">
            <div class="p-8 text-center">
                <div class="text-gray-400 text-4xl mb-4">📦</div>
                <h3 class="text-lg font-semibold text-gray-700 mb-2">No products found</h3>
                <p class="text-gray-500">Try adjusting your search or filters</p>
            </div>
        </div>
    @endif

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6">
        <div class="kpi-card kpi-gold">
            <div class="kpi-label">Total Products</div>
            <div class="kpi-value">{{ $products->total() }}</div>
            <div class="kpi-sub">In catalog</div>
        </div>
        <div class="kpi-card kpi-red">
            <div class="kpi-label">Critical Stock</div>
            <div class="kpi-value">{{ \App\Models\Product::criticalStock()->count() }}</div>
            <div class="kpi-sub">Need immediate attention</div>
        </div>
        <div class="kpi-card kpi-moss">
            <div class="kpi-label">Low Stock</div>
            <div class="kpi-value">{{ \App\Models\Product::lowStock()->count() }}</div>
            <div class="kpi-sub">Should restock soon</div>
        </div>
        <div class="kpi-card kpi-roast">
            <div class="kpi-label">Normal Stock</div>
            <div class="kpi-value">{{ \App\Models\Product::whereRaw('stock_qty > low_stock_threshold')->count() }}</div>
            <div class="kpi-sub">Adequate levels</div>
        </div>
    </div>
</div>
