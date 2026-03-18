@props(['title' => 'Quick Actions'])

<div class="card">
    <div class="card-header">
        <span class="card-title">⚡ {{ $title }}</span>
    </div>
    <div style="padding:1.25rem;">
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:0.75rem;">
            <a href="{{ route('manager.products') }}" class="btn-primary" style="display:flex; align-items:center; justify-content:center; gap:0.5rem; text-decoration:none;">
                <span>➕</span> Add Product
            </a>
            <a href="{{ route('manager.stock-in') }}" class="btn-secondary" style="display:flex; align-items:center; justify-content:center; gap:0.5rem; text-decoration:none;">
                <span>📦</span> Stock In
            </a>
            <a href="{{ route('reports.sales') }}" class="btn-secondary" style="display:flex; align-items:center; justify-content:center; gap:0.5rem; text-decoration:none;">
                <span>📊</span> Daily Report
            </a>
            <a href="{{ route('transactions.index') }}" class="btn-secondary" style="display:flex; align-items:center; justify-content:center; gap:0.5rem; text-decoration:none;">
                <span>🧾</span> Orders
            </a>
        </div>
        
        {{-- Hourly Sales Pattern --}}
        <div style="margin-top:1.5rem; padding-top:1.25rem; border-top:1px solid rgba(74,37,24,0.08);">
            <div style="font-size:0.75rem; font-weight:700; color:#3d2415; margin-bottom:0.75rem;">🕐 Peak Hours Today</div>
            <div style="height:120px; position:relative;">
                <canvas id="hourlyChart"></canvas>
            </div>
        </div>
    </div>
</div>
