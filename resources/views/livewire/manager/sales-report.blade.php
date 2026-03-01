<div>
    <h1 class="page-title">Sales Report</h1>

    {{-- Date Filter --}}
    <div class="card" style="padding:1rem 1.25rem; margin-bottom:1.25rem; display:flex; flex-wrap:wrap; gap:0.75rem; align-items:flex-end;">
        <div>
            <label class="form-label">From Date</label>
            <input wire:model.live="dateFrom" type="date" class="form-input" style="width:165px;">
        </div>
        <div>
            <label class="form-label">To Date</label>
            <input wire:model.live="dateTo" type="date" class="form-input" style="width:165px;">
        </div>
    </div>

    {{-- Summary --}}
    <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:1rem; margin-bottom:1.5rem;">
        <div class="kpi-card kpi-gold">
            <div class="kpi-label">Total Revenue</div>
            <div class="kpi-value" style="color:#8a5a20;">₱{{ number_format($this->summary['total_sales'], 2) }}</div>
        </div>
        <div class="kpi-card kpi-roast">
            <div class="kpi-label">Transactions</div>
            <div class="kpi-value">{{ number_format($this->summary['total_count']) }}</div>
        </div>
        <div class="kpi-card kpi-gold">
            <div class="kpi-label">Avg per Transaction</div>
            <div class="kpi-value" style="color:#8a5a20;">₱{{ number_format($this->summary['avg_sale'], 2) }}</div>
        </div>
    </div>

    <div style="display:grid; grid-template-columns:1fr 2fr; gap:1.25rem;">

        {{-- Best Sellers --}}
        <div class="card">
            <div class="card-header"><span class="card-title">↗ Best Sellers</span></div>
            @if ($this->bestSellers->isEmpty())
                <div style="padding:2.5rem; text-align:center; color:#c8b0a0; font-family:'Playfair Display',serif; font-style:italic;">No data for period</div>
            @else
                @foreach ($this->bestSellers as $item)
                <div style="display:flex; align-items:center; gap:0.85rem; padding:0.8rem 1.25rem; border-bottom:1px solid rgba(74,37,24,0.06);">
                    <span style="width:24px; height:24px; border-radius:50%; background:#fdf5e8; color:#8a5a20; font-size:0.7rem; font-weight:700; display:flex; align-items:center; justify-content:center; flex-shrink:0; border:1px solid #e8d0a0;">
                        {{ $loop->iteration }}
                    </span>
                    <div style="flex:1;">
                        <div style="font-size:0.82rem; font-weight:700; color:#3d2415; font-family:'Playfair Display',serif;">{{ $item->product->name }}</div>
                    </div>
                    <div style="text-align:right;">
                        <div style="font-size:0.82rem; font-weight:700; color:#3d2415;">{{ $item->total_qty }}</div>
                        <div style="font-size:0.65rem; color:#9a7a68;">units</div>
                    </div>
                </div>
                @endforeach
            @endif
        </div>

        {{-- Transactions --}}
        <div class="card">
            <div class="card-header"><span class="card-title">Transaction Details</span></div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Receipt #</th>
                        <th>Date</th>
                        <th>Cashier</th>
                        <th style="text-align:right;">Total</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transactions as $txn)
                    <tr>
                        <td style="font-family:'Courier New',monospace; font-weight:700; font-size:0.78rem; color:#8a5a20;">{{ $txn->receipt_no }}</td>
                        <td style="font-size:0.75rem; color:#7a5c44; white-space:nowrap;">{{ $txn->created_at->format('M d H:i') }}</td>
                        <td style="font-size:0.78rem; color:#6a4a35;">{{ $txn->cashier->name }}</td>
                        <td style="text-align:right; font-weight:700; color:#3d2415;">₱{{ number_format($txn->total, 2) }}</td>
                        <td><a href="{{ route('transactions.show', $txn->id) }}" class="btn-link" style="font-size:0.72rem;">View</a></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align:center; padding:2.5rem; color:#c8b0a0; font-style:italic; font-family:'Playfair Display',serif;">No transactions in this period.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div style="padding:1rem 1.25rem; border-top:1px solid rgba(74,37,24,0.06);">
                {{ $transactions->links() }}
            </div>
        </div>
    </div>
</div>
