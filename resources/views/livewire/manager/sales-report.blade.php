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

    {{-- Sales Trend Chart --}}
    <div class="card" style="margin-bottom:1.5rem;">
        <div class="card-header"><span class="card-title">📈 Sales Trend</span></div>
        <div style="position:relative; height:300px; padding:1.25rem;">
            <canvas id="salesTrendChart"></canvas>
        </div>
    </div>

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1.25rem; margin-bottom:1.5rem;">
        {{-- Sales by Category Chart --}}
        <div class="card">
            <div class="card-header"><span class="card-title">📊 Sales by Category</span></div>
            <div style="position:relative; height:250px; padding:1.25rem;">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>

        {{-- Export Options --}}
        <div class="card">
            <div class="card-header"><span class="card-title">💾 Export Options</span></div>
            <div style="padding:1.25rem; display:flex; flex-direction:column; gap:0.75rem;">
                <button wire:click="exportCsv" class="btn-secondary" style="width:100%; justify-content:center;">
                    <i class="fa-solid fa-file-csv"></i> Export to CSV
                </button>
                <button wire:click="exportPdf" class="btn-secondary" style="width:100%; justify-content:center;">
                    <i class="fa-solid fa-file-pdf"></i> Export to PDF
                </button>
                <button wire:click="printReport" class="btn-secondary" style="width:100%; justify-content:center;">
                    <i class="fa-solid fa-print"></i> Print Report
                </button>
            </div>
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('livewire:init', () => {
        // Sales Trend Chart
        const trendCtx = document.getElementById('salesTrendChart');
        if (trendCtx) {
            const chartData = @json($this->chartData);
            
            new Chart(trendCtx.getContext('2d'), {
                type: 'line',
                data: {
                    labels: chartData.labels || [],
                    datasets: [
                        {
                            label: 'Revenue',
                            data: chartData.revenue || [],
                            borderColor: '#8a5a20',
                            backgroundColor: 'rgba(138, 90, 32, 0.1)',
                            borderWidth: 2,
                            tension: 0.3,
                            yAxisID: 'y'
                        },
                        {
                            label: 'Transactions',
                            data: chartData.transactions || [],
                            borderColor: '#2d6a2d',
                            backgroundColor: 'rgba(45, 106, 45, 0.1)',
                            borderWidth: 2,
                            tension: 0.3,
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                padding: 15,
                                font: {
                                    size: 11,
                                    family: "'Lato', sans-serif"
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(26, 15, 10, 0.9)',
                            titleFont: {
                                size: 12,
                                family: "'Lato', sans-serif"
                            },
                            bodyFont: {
                                size: 11,
                                family: "'Lato', sans-serif"
                            },
                            padding: 12,
                            cornerRadius: 8,
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.datasetIndex === 0) {
                                        label += '₱' + context.parsed.y.toLocaleString();
                                    } else {
                                        label += context.parsed.y + ' transactions';
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            title: {
                                display: true,
                                text: 'Revenue (₱)',
                                font: {
                                    size: 10,
                                    family: "'Lato', sans-serif"
                                }
                            },
                            ticks: {
                                callback: function(value) {
                                    return '₱' + value.toLocaleString();
                                }
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            title: {
                                display: true,
                                text: 'Transactions',
                                font: {
                                    size: 10,
                                    family: "'Lato', sans-serif"
                                }
                            },
                            grid: {
                                drawOnChartArea: false,
                            }
                        }
                    }
                }
            });
        }

        // Sales by Category Chart
        const categoryCtx = document.getElementById('categoryChart');
        if (categoryCtx) {
            const categoryData = @json($this->categoryData);
            
            new Chart(categoryCtx.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: categoryData.labels || [],
                    datasets: [{
                        data: categoryData.data || [],
                        backgroundColor: categoryData.colors || [],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 15,
                                font: {
                                    size: 11,
                                    family: "'Lato', sans-serif"
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(26, 15, 10, 0.9)',
                            titleFont: {
                                size: 12,
                                family: "'Lato', sans-serif"
                            },
                            bodyFont: {
                                size: 11,
                                family: "'Lato', sans-serif"
                            },
                            padding: 12,
                            cornerRadius: 8,
                            callbacks: {
                                label: function(context) {
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((context.parsed / total) * 100).toFixed(1);
                                    return context.label + ': ' + context.parsed + ' units (' + percentage + '%)';
                                }
                            }
                        }
                    }
                }
            });
        }
    });

    // Handle Livewire updates
    document.addEventListener('livewire:updated', () => {
        // Chart will be re-rendered automatically when component updates
    });
</script>
@endpush
