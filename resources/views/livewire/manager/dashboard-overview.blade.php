<div class="dashboard-container" style="display: flex; flex-direction: column; min-height: 100%; flex: 1;">
    <!-- Header -->
    <div style="display:flex; align-items:baseline; justify-content:space-between; margin-bottom:1.75rem; flex-wrap:wrap; gap:1rem; flex-shrink: 0;">
        <h1 class="page-title" style="margin-bottom:0;">Good {{ now()->hour < 12 ? 'Morning' : (now()->hour < 17 ? 'Afternoon' : 'Evening') }}, {{ auth()->user()->name }}</h1>
        <span style="font-size:0.72rem; color:#9a7a68; letter-spacing:0.06em; text-transform:uppercase; font-family:'Lato',sans-serif;">
            {{ now()->format('l, F j Y') }}
        </span>
    </div>

    {{-- KPI Grid --}}
    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(250px, 1fr)); gap:1rem; margin-bottom:1.75rem; flex-shrink: 0; width: 100%;">
        <div class="kpi-card kpi-gold">
            <div class="kpi-label">Today's Revenue</div>
            <div class="kpi-value" style="color:#8a5a20;">₱{{ number_format($this->todaySales, 2) }}</div>
            <div class="kpi-sub">{{ $this->todayCount }} transactions today</div>
        </div>
        <div class="kpi-card kpi-roast">
            <div class="kpi-label">This Month</div>
            <div class="kpi-value">₱{{ number_format($this->monthSales, 2) }}</div>
            <div class="kpi-sub">{{ $this->monthCount }} transactions</div>
        </div>
        <div class="kpi-card {{ $this->lowStockProducts->count() > 0 ? 'kpi-red' : 'kpi-moss' }}">
            <div class="kpi-label">Low Stock Alerts</div>
            <div class="kpi-value" style="{{ $this->lowStockProducts->count() > 0 ? 'color:#8a2020' : 'color:#2d5a2d' }}">
                {{ $this->lowStockProducts->count() }}
            </div>
            <div class="kpi-sub">products need restocking</div>
        </div>
        <div class="kpi-card kpi-gold">
            <div class="kpi-label">Top Seller Today</div>
            @if ($this->todayBestSellers->first())
                <div class="kpi-value" style="font-size:1.2rem; color:#8a5a20; line-height:1.2;">{{ $this->todayBestSellers->first()->product->name }}</div>
                <div class="kpi-sub">{{ $this->todayBestSellers->first()->total_qty }} units sold</div>
            @else
                <div class="kpi-value" style="font-size:1.5rem; color:#c8b0a0;">—</div>
                <div class="kpi-sub">No sales yet</div>
            @endif
        </div>
    </div>

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1.25rem; flex: 1; min-height: 0; width: 100%;">

        {{-- Low Stock --}}
        <div class="card" style="display: flex; flex-direction: column; min-height: 0;">
            <div class="card-header" style="flex-shrink: 0;">
                <span class="card-title">⚑ Low Stock Alerts</span>
                <a href="{{ route('inventory.stock-in') }}" style="font-size:0.72rem; color:var(--caramel); font-weight:700; letter-spacing:0.04em;">Restock →</a>
            </div>
            <div style="flex: 1; overflow-y: auto; min-height: 0;">
                @if ($this->lowStockProducts->isEmpty())
                    <div style="padding:2.5rem 1.5rem; text-align:center; color:#c8b0a0;">
                        <div style="font-size:1.5rem; margin-bottom:0.5rem;">✓</div>
                        <div style="font-size:0.82rem; font-family:'Playfair Display',serif;">All stock levels healthy</div>
                    </div>
                @else
                    @foreach ($this->lowStockProducts as $product)
                    <div style="display:flex; align-items:center; justify-content:space-between; padding:0.8rem 1.25rem; border-bottom:1px solid rgba(74,37,24,0.06);">
                        <div>
                            <div style="font-size:0.82rem; font-weight:700; color:#3d2415;">{{ $product->name }}</div>
                            <div style="font-size:0.68rem; color:#9a7a68; text-transform:uppercase; letter-spacing:0.06em;">{{ $product->category_label }}</div>
                        </div>
                        <div style="text-align:right;">
                            <div style="font-size:0.9rem; font-weight:700; color:{{ $product->stock_qty == 0 ? '#c0392b' : '#c8811a' }};">
                                {{ $product->stock_qty }} left
                            </div>
                            <div style="font-size:0.65rem; color:#c8b0a0;">reorder at {{ $product->reorder_level }}</div>
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>

        {{-- Best Sellers --}}
        <div class="card" style="display: flex; flex-direction: column; min-height: 0;">
            <div class="card-header" style="flex-shrink: 0;">
                <span class="card-title">↗ Today's Best Sellers</span>
                <a href="{{ route('reports.sales') }}" style="font-size:0.72rem; color:var(--caramel); font-weight:700; letter-spacing:0.04em;">Full report →</a>
            </div>
            <div style="flex: 1; overflow-y: auto; min-height: 0;">
                @if ($this->todayBestSellers->isEmpty())
                    <div style="padding:2.5rem 1.5rem; text-align:center; color:#c8b0a0;">
                        <div style="font-size:1.5rem; margin-bottom:0.5rem;">☕</div>
                        <div style="font-size:0.82rem; font-family:'Playfair Display',serif;">No sales recorded today</div>
                    </div>
                @else
                    @foreach ($this->todayBestSellers as $item)
                    <div style="display:flex; align-items:center; gap:1rem; padding:0.8rem 1.25rem; border-bottom:1px solid rgba(74,37,24,0.06);">
                        <div style="width:26px; height:26px; border-radius:50%; background:#fdf5e8; color:#8a5a20; font-size:0.72rem; font-weight:700; display:flex; align-items:center; justify-content:center; flex-shrink:0; border:1px solid #e8d0a0;">
                            {{ $loop->iteration }}
                        </div>
                        <div style="flex:1;">
                            <div style="font-size:0.82rem; font-weight:700; color:#3d2415;">{{ $item->product->name }}</div>
                        </div>
                        <div style="text-align:right;">
                            <div style="font-size:0.82rem; font-weight:700; color:#3d2415;">{{ $item->total_qty }} units</div>
                            <div style="font-size:0.68rem; color:#9a7a68;">₱{{ number_format($item->total_revenue, 2) }}</div>
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>
