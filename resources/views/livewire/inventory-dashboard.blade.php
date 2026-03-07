<div>
    <!-- Header Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Total Products</dt>
                        <dd class="text-lg font-semibold text-gray-900">{{ $totalProducts }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-red-500 rounded-md p-3">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Critical Stock</dt>
                        <dd class="text-lg font-semibold text-red-600">{{ $criticalCount }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Low Stock</dt>
                        <dd class="text-lg font-semibold text-yellow-600">{{ $lowStockCount }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Normal Stock</dt>
                        <dd class="text-lg font-semibold text-green-600">{{ $totalProducts - $criticalCount - $lowStockCount }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Stock Status</label>
                <select wire:model.live="filter" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="all">All Products</option>
                    <option value="critical">Critical Only</option>
                    <option value="low">Low Stock Only</option>
                    <option value="normal">Normal Stock Only</option>
                </select>
            </div>
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                <select wire:model.live="category" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="all">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}">{{ ucfirst($cat) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button wire:click="refreshStats" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                    Refresh
                </button>
            </div>
        </div>
    </div>

    <!-- Products Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        @foreach($products as $product)
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-{{ $product->stock_status_color }}-500">
                <div class="flex justify-between items-start mb-2">
                    <h3 class="font-semibold text-gray-900">{{ $product->name }}</h3>
                    <span class="px-2 py-1 text-xs rounded-full bg-{{ $product->stock_status_color }}-100 text-{{ $product->stock_status_color }}-800">
                        {{ ucfirst($product->stock_status) }}
                    </span>
                </div>
                
                <div class="space-y-1 text-sm text-gray-600">
                    <div class="flex justify-between">
                        <span>Current Stock:</span>
                        <span class="font-medium {{ $product->stock_status === 'critical' ? 'text-red-600' : ($product->stock_status === 'low' ? 'text-yellow-600' : 'text-green-600') }}">
                            {{ $product->stock_qty }} units
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span>Threshold:</span>
                        <span>{{ $product->low_stock_threshold }} units</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Category:</span>
                        <span>{{ $product->category_label }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Price:</span>
                        <span>₱{{ number_format($product->selling_price, 2) }}</span>
                    </div>
                </div>

                @if($product->stock_status === 'critical')
                    <div class="mt-3 p-2 bg-red-50 rounded text-xs text-red-700">
                        <strong>⚠️ Critical:</strong> Stock below 50% of threshold. Restock immediately!
                    </div>
                @elseif($product->stock_status === 'low')
                    <div class="mt-3 p-2 bg-yellow-50 rounded text-xs text-yellow-700">
                        <strong>⚠️ Low Stock:</strong> Consider restocking soon.
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="bg-white rounded-lg shadow px-4 py-3">
        {{ $products->links() }}
    </div>

    <!-- Recent Stock Movements -->
    <div class="bg-white rounded-lg shadow p-6 mt-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Stock Movements</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reason</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">By</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">When</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($recentMovements as $movement)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $movement->product->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $movement->type === 'in' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ strtoupper($movement->type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $movement->type === 'in' ? '+' : '-' }}{{ $movement->qty }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $movement->reason }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $movement->createdBy->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $movement->created_at->diffForHumans() }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
