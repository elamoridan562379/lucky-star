<div class="dashboard-container" style="display: flex; flex-direction: column; min-height: 100%; flex: 1;">
    <!-- Header -->
    <div style="display:flex; align-items:baseline; justify-content:space-between; margin-bottom:1.75rem; flex-wrap:wrap; gap:1rem; flex-shrink: 0;">
        <h1 class="page-title" style="margin-bottom:0;">Good {{ now()->hour < 12 ? 'Morning' : (now()->hour < 17 ? 'Afternoon' : 'Evening') }}, {{ auth()->user()->name }}</h1>
        <div style="display:flex; align-items:center; gap:1rem;">
            <a href="{{ route('admin.products.create') }}" class="btn-primary" style="padding: 0.4rem 0.8rem; font-size: 0.7rem; background-color: #d4af37; color: white; border: none; border-radius: 6px; cursor: pointer; text-decoration: none;">
                <i class="fa-solid fa-plus"></i> Add Product
            </a>
            <a href="{{ route('admin.stock-in.create') }}" class="btn-secondary" style="padding: 0.4rem 0.8rem; font-size: 0.7rem; background-color: #4a2c00; color: white; border: none; border-radius: 6px; cursor: pointer; text-decoration: none;">
                <i class="fa-solid fa-download"></i> Stock In
            </a>
            <a href="{{ route('admin.reports.sales.create') }}" class="btn-secondary" style="padding: 0.4rem 0.8rem; font-size: 0.7rem; background-color: #4a2c00; color: white; border: none; border-radius: 6px; cursor: pointer; text-decoration: none;">
                <i class="fa-solid fa-file-invoice"></i> New Sale
            </a>
        </div>
    </div>

    {{-- KPI Grid --}}
    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(250px, 1fr)); gap:1rem; margin-bottom:1.75rem; flex-shrink: 0; width: 100%;">
        <div class="kpi-card kpi-gold">
            <div class="kpi-label">Today's Revenue</div>
            <div class="kpi-value" style="color:#8a5a20;">₱{{ number_format($todayStats['revenue'], 2) }}</div>
            <div class="kpi-sub">{{ $todayStats['count'] }} transactions today</div>
        </div>
        <div class="kpi-card kpi-roast">
            <div class="kpi-label">This Month</div>
            <div class="kpi-value">₱{{ number_format($monthStats['revenue'], 2) }}</div>
            <div class="kpi-sub">{{ $monthStats['count'] }} transactions</div>
        </div>
        <div class="kpi-card {{ $lowStockProducts->count() > 0 ? 'kpi-red' : 'kpi-moss' }}">
            <div class="kpi-label">Low Stock Alerts</div>
            <div class="kpi-value" style="{{ $lowStockProducts->count() > 0 ? 'color:#8a2020' : 'color:#2d5a2d' }}">
                {{ $lowStockProducts->count() }}
            </div>
            <div class="kpi-sub">products need restocking</div>
        </div>
        <div class="kpi-card kpi-gold">
            <div class="kpi-label">Top Seller Today</div>
            @if ($todayStats['top_seller'])
                <div class="kpi-value" style="font-size:1.2rem; color:#8a5a20; line-height:1.2;">{{ $todayStats['top_seller']->product->name }}</div>
                <div class="kpi-sub">{{ $todayStats['top_seller']->total_qty }} units sold</div>
            @else
                <div class="kpi-value" style="font-size:1.5rem; color:#c8b0a0;">—</div>
                <div class="kpi-sub">No sales yet</div>
            @endif
        </div>
    </div>

    <!-- Charts Section -->
    <div style="display:grid; grid-template-columns:1.2fr 0.8fr; gap:1.25rem; margin-bottom:1.75rem; width: 100%;">
        <!-- Weekly Revenue Trend -->
        <div class="card" style="padding:1.25rem;">
            <div class="card-header" style="margin-bottom:1rem; padding:0; border:none;">
                <span class="card-title">Weekly Revenue Trend</span>
            </div>
            <div style="position:relative; height:280px;">
                <canvas id="weeklyRevenueChart"></canvas>
            </div>
        </div>

        <!-- Sales by Category -->
        <div class="card" style="padding:1.25rem;">
            <div class="card-header" style="margin-bottom:1rem; padding:0; border:none;">
                <span class="card-title">Sales by Category</span>
                <div style="font-size:0.75rem; color:#9a7a68; margin-top:0.25rem;">
                    {{ array_sum($salesByCategoryData['data']) }} Units Sold Today
                </div>
            </div>
            <div style="position:relative; height:280px;">
                <canvas id="salesByCategoryChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Bottom Section -->
    <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:1.25rem; flex: 1; min-height: 0; width: 100%;">

        {{-- Low Stock --}}
        <div class="card" style="display: flex; flex-direction: column; min-height: 0;">
            <div class="card-header" style="flex-shrink: 0;">
                <span class="card-title">⚑ Low Stock Alerts</span>
                <a href="{{ route('manager.stock-in') }}" style="font-size:0.72rem; color:var(--caramel); font-weight:700; letter-spacing:0.04em;">Restock →</a>
            </div>
            <div style="flex: 1; overflow-y: auto; min-height: 0;">
                @if ($lowStockProducts->isEmpty())
                    <div style="padding:2.5rem 1.5rem; text-align:center; color:#c8b0a0;">
                        <div style="font-size:1.5rem; margin-bottom:0.5rem;">✓</div>
                        <div style="font-size:0.82rem; font-family:'Playfair Display',serif;">All stock levels healthy</div>
                    </div>
                @else
                    @foreach ($lowStockProducts->take(5) as $product)
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

        {{-- Recent Activity --}}
        <div class="card" style="display: flex; flex-direction: column; min-height: 0;">
            <div class="card-header" style="flex-shrink: 0;">
                <span class="card-title">Recent Activity</span>
            </div>
            <div style="flex: 1; overflow-y: auto; min-height: 0;">
                @if ($recentActivities->isEmpty())
                    <div style="padding:2.5rem 1.5rem; text-align:center; color:#c8b0a0;">
                        <div style="font-size:1.5rem; margin-bottom:0.5rem;">📋</div>
                        <div style="font-size:0.82rem; font-family:'Playfair Display',serif;">No recent activity</div>
                    </div>
                @else
                    @foreach ($recentActivities->take(6) as $activity)
                    <div style="display:flex; align-items:flex-start; gap:0.75rem; padding:0.8rem 1.25rem; border-bottom:1px solid rgba(74,37,24,0.06);">
                        <div style="font-size:1rem; flex-shrink:0;">{{ $activity['icon'] }}</div>
                        <div style="flex:1;">
                            <div style="font-size:0.78rem; font-weight:700; color:#3d2415;">{{ $activity['description'] }}</div>
                            <div style="font-size:0.68rem; color:#9a7a68;">{{ $activity['details'] }}</div>
                        </div>
                        <div style="font-size:0.6rem; color:#c8b0a0; text-align:right; flex-shrink:0;">
                            {{ $activity['time'] }}
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
                @if ($bestSellers->isEmpty())
                    <div style="padding:2.5rem 1.5rem; text-align:center; color:#c8b0a0;">
                        <div style="font-size:1.5rem; margin-bottom:0.5rem;">☕</div>
                        <div style="font-size:0.82rem; font-family:'Playfair Display',serif;">No sales recorded today</div>
                    </div>
                @else
                    @foreach ($bestSellers->take(6) as $item)
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('livewire:init', () => {
        // Weekly Revenue Chart
        const weeklyCtx = document.getElementById('weeklyRevenueChart');
        if (weeklyCtx) {
            const weeklyData = @json($weeklyRevenueData);
            
            new Chart(weeklyCtx.getContext('2d'), {
                type: 'line',
                data: {
                    labels: weeklyData.labels || [],
                    datasets: [
                        {
                            label: 'Revenue',
                            data: weeklyData.revenue || [],
                            borderColor: '#8a5a20',
                            backgroundColor: 'rgba(138, 90, 32, 0.1)',
                            borderWidth: 2,
                            tension: 0.3,
                            yAxisID: 'y'
                        },
                        {
                            label: 'Transactions',
                            data: weeklyData.transactions || [],
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
        const categoryCtx = document.getElementById('salesByCategoryChart');
        if (categoryCtx) {
            const categoryData = @json($salesByCategoryData);
            
            new Chart(categoryCtx.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: categoryData.labels || [],
                    datasets: [{
                        data: categoryData.data || [],
                        backgroundColor: Object.values(categoryData.colors || {}),
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
        // Charts will be re-rendered automatically when component updates
    });
</script>
@endpush
