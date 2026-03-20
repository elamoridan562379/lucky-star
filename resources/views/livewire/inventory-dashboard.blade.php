<div class="dashboard-container" style="display: flex; flex-direction: column; min-height: 100%; flex: 1;">
    <!-- Header -->
    <div style="display:flex; align-items:baseline; justify-content:space-between; margin-bottom:1.75rem; flex-wrap:wrap; gap:1rem; flex-shrink: 0;">
        <h1 class="page-title" style="margin-bottom:0;">Inventory Overview</h1>
        <span style="font-size:0.72rem; color:#9a7a68; letter-spacing:0.06em; text-transform:uppercase; font-family:'Lato',sans-serif;">
            {{ now()->format('l, F j Y') }}
        </span>
    </div>

    <!-- Header Stats -->
    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(250px, 1fr)); gap:1rem; margin-bottom:1.75rem; flex-shrink: 0; width: 100%;">
        <div class="kpi-card kpi-roast">
            <div class="kpi-label">Total Products</div>
            <div class="kpi-value">{{ $totalProducts }}</div>
            <div class="kpi-sub">Active in catalog</div>
        </div>

        <div class="kpi-card kpi-red">
            <div class="kpi-label">Critical Stock</div>
            <div class="kpi-value" style="color:#8a2020">{{ $criticalCount }}</div>
            <div class="kpi-sub">Needs immediate restock</div>
        </div>

        <div class="kpi-card kpi-gold">
            <div class="kpi-label">Low Stock</div>
            <div class="kpi-value" style="color:#8a5a20">{{ $lowStockCount }}</div>
            <div class="kpi-sub">Below threshold</div>
        </div>

        <div class="kpi-card kpi-moss">
            <div class="kpi-label">Normal Stock</div>
            <div class="kpi-value" style="color:#2d5a2d">{{ $totalProducts - $criticalCount - $lowStockCount }}</div>
            <div class="kpi-sub">Healthy levels</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-6" style="flex-shrink: 0;">
        <div class="card-header">
            <span class="card-title">🔍 Filters & Search</span>
            <button wire:click="refreshStats" class="btn-secondary" style="padding: 0.35rem 0.8rem; font-size: 0.7rem;">Refresh</button>
        </div>
        <div class="p-6">
            <div style="display:flex; flex-wrap:wrap; gap:1.25rem;">
                <div style="flex:1; min-width:200px;">
                    <label class="form-label">Stock Status</label>
                    <select wire:model.live="filter" class="form-input">
                        <option value="all">All Products</option>
                        <option value="critical">Critical Only</option>
                        <option value="low">Low Stock Only</option>
                        <option value="normal">Normal Stock Only</option>
                    </select>
                </div>
                <div style="flex:1; min-width:200px;">
                    <label class="form-label">Category</label>
                    <select wire:model.live="category" class="form-input">
                        <option value="all">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}">{{ ucfirst(str_replace('_', ' ', $cat)) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Grid -->
    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(350px, 1fr)); gap:1.25rem; margin-bottom:1.75rem; flex: 1; min-height: 0; width: 100%;">
        @foreach($products as $product)
            <div class="card" style="border-left: 4px solid {{ $product->stock_status === 'critical' ? '#c0392b' : ($product->stock_status === 'low' ? '#d4a847' : '#3d7a3d') }}">
                <div class="p-4">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:0.75rem;">
                        <h3 style="font-family:'Playfair Display',serif; font-weight:700; color:var(--roast); font-size:1rem;">{{ $product->name }}</h3>
                        <span class="badge {{ $product->stock_status === 'critical' ? 'badge-out' : ($product->stock_status === 'low' ? 'badge-low' : 'badge-active') }}">
                            {{ ucfirst($product->stock_status) }}
                        </span>
                    </div>
                    
                    <div style="font-size:0.82rem; color:#3d2415; display:grid; gap:0.4rem;">
                        <div style="display:flex; justify-content:space-between;">
                            <span style="color:#9a7a68;">Current Stock:</span>
                            <span style="font-weight:700; color:{{ $product->stock_status === 'critical' ? '#c0392b' : ($product->stock_status === 'low' ? '#8a5a20' : '#2d5a2d') }}">
                                {{ $product->stock_qty }} units
                            </span>
                        </div>
                        <div style="display:flex; justify-content:space-between;">
                            <span style="color:#9a7a68;">Threshold:</span>
                            <span>{{ $product->low_stock_threshold }} units</span>
                        </div>
                        <div style="display:flex; justify-content:space-between;">
                            <span style="color:#9a7a68;">Category:</span>
                            <span style="text-transform:uppercase; font-size:0.7rem; letter-spacing:0.04em; font-weight:700;">{{ $product->category_label }}</span>
                        </div>
                    </div>

                    @if($product->stock_status === 'critical')
                        <div style="margin-top:1rem; padding:0.6rem; background:#fdf0f0; border-radius:8px; font-size:0.7rem; color:#8a2020; border:1px solid rgba(192,57,43,0.1);">
                            <strong>⚠️ Critical:</strong> Stock below 50% of threshold.
                        </div>
                    @elseif($product->stock_status === 'low')
                        <div style="margin-top:1rem; padding:0.6rem; background:#fff8e8; border-radius:8px; font-size:0.7rem; color:#8a5a10; border:1px solid rgba(212,168,71,0.1);">
                            <strong>⚠️ Low Stock:</strong> Consider restocking soon.
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div style="margin-bottom:1.75rem; flex-shrink: 0;">
        {{ $products->links() }}
    </div>

    <!-- Recent Stock Movements -->
    <div class="card" style="flex-shrink: 0;">
        <div class="card-header">
            <span class="card-title">📋 Recent Stock Movements</span>
        </div>
        <div class="p-0">
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Type</th>
                            <th>Qty</th>
                            <th>Reason</th>
                            <th>By</th>
                            <th>When</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentMovements as $movement)
                            <tr>
                                <td style="font-weight:700;">{{ $movement->product->name }}</td>
                                <td>
                                    <span class="badge {{ $movement->type === 'in' ? 'badge-in' : 'badge-out' }}">
                                        {{ strtoupper($movement->type) }}
                                    </span>
                                </td>
                                <td style="font-weight:700; color:{{ $movement->type === 'in' ? '#2d6a2d' : '#8a2020' }}">
                                    {{ $movement->type === 'in' ? '+' : '-' }}{{ $movement->qty }}
                                </td>
                                <td>{{ ucfirst($movement->reason) }}</td>
                                <td>{{ $movement->createdBy->name }}</td>
                                <td style="color:#9a7a68; font-size:0.75rem;">{{ $movement->created_at->diffForHumans() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

