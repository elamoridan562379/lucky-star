<div class="dashboard-container" style="display: flex; flex-direction: column; min-height: 100%; flex: 1;">
    <!-- Header -->
    <div style="display:flex; align-items:baseline; justify-content:space-between; margin-bottom:1.75rem; flex-wrap:wrap; gap:1rem; flex-shrink: 0;">
        <h1 class="page-title" style="margin-bottom:0;">Good {{ now()->hour < 12 ? 'Morning' : (now()->hour < 17 ? 'Afternoon' : 'Evening') }}, {{ auth()->user()->name }}</h1>
        <span style="font-size:0.72rem; color:#9a7a68; letter-spacing:0.06em; text-transform:uppercase; font-family:'Lato',sans-serif;">
            {{ now()->format('l, F j Y') }}
        </span>
    </div>

    {{-- Row 1: Enhanced KPI Cards --}}
    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(240px, 1fr)); gap:1rem; margin-bottom:1.75rem; flex-shrink: 0; width: 100%;">
        <div class="kpi-card kpi-gold">
            <div class="kpi-label">Today's Revenue</div>
            <div class="kpi-value" style="color:#8a5a20;">₱{{ number_format($this->todaySales, 2) }}</div>
            <div class="kpi-sub">{{ $this->todayCount }} transactions</div>
            @if($this->yesterdaySales > 0)
                <div class="kpi-sub" style="color: {{ $this->todaySales >= $this->yesterdaySales ? '#2d5a2d' : '#c0392b' }}; font-size: 0.65rem;">
                    {{ $this->todaySales >= $this->yesterdaySales ? '↑' : '↓' }} {{ number_format((($this->todaySales - $this->yesterdaySales) / $this->yesterdaySales) * 100, 1) }}% vs yesterday
                </div>
            @endif
        </div>
        
        <div class="kpi-card kpi-roast">
            <div class="kpi-label">This Month</div>
            <div class="kpi-value">₱{{ number_format($this->monthSales, 2) }}</div>
            <div class="kpi-sub">{{ $this->monthCount }} transactions</div>
            @if($this->lastMonthSales > 0)
                <div class="kpi-sub" style="color: {{ $this->monthSales >= $this->lastMonthSales ? '#2d5a2d' : '#c0392b' }}; font-size: 0.65rem;">
                    {{ $this->monthSales >= $this->lastMonthSales ? '↑' : '↓' }} {{ number_format((($this->monthSales - $this->lastMonthSales) / $this->lastMonthSales) * 100, 1) }}% vs last month
                </div>
            @endif
        </div>
        
        <div class="kpi-card {{ $this->lowStockProducts->count() > 0 ? 'kpi-red' : 'kpi-moss' }}">
            <div class="kpi-label">Low Stock Alerts</div>
            <div class="kpi-value" style="{{ $this->lowStockProducts->count() > 0 ? 'color:#8a2020' : 'color:#2d5a2d' }}">
                {{ $this->lowStockProducts->count() }}
            </div>
            <div class="kpi-sub">products need restocking</div>
        </div>
        
        <div class="kpi-card kpi-gold">
            <div class="kpi-label">Avg Transaction Value</div>
            <div class="kpi-value" style="font-size:1.5rem; color:#8a5a20;">₱{{ number_format($this->averageTransactionValue, 2) }}</div>
            <div class="kpi-sub">{{ number_format($this->averageItemsPerTransaction, 1) }} items per sale</div>
        </div>
    </div>

    {{-- Row 2: Sales Trends Chart --}}
    <div class="card" style="margin-bottom:1.75rem; flex-shrink: 0;">
        <div class="card-header">
            <span class="card-title">📈 Sales Trends</span>
            <div style="display:flex; gap:0.5rem; align-items:center;">
                <select wire:model.live="chartPeriod" style="
                    border:1px solid rgba(74,37,24,0.2);
                    border-radius:6px;
                    padding:0.35rem 0.65rem;
                    font-size:0.75rem;
                    color:var(--roast);
                    background:white;
                    outline:none;
                ">
                    <option value="daily">Daily (7 days)</option>
                    <option value="weekly">Weekly (8 weeks)</option>
                    <option value="monthly">Monthly (12 months)</option>
                </select>
            </div>
        </div>
        <div style="padding:1.5rem; height:300px; position:relative;">
            <canvas id="salesChart"></canvas>
        </div>
    </div>

    {{-- Row 3: Activity Feed and Quick Actions --}}
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1.25rem; margin-bottom:1.75rem;">
        {{-- Recent Activity Feed --}}
        <div class="card" style="display: flex; flex-direction: column;">
            <div class="card-header">
                <span class="card-title">🕒 Recent Activity</span>
                <a href="{{ route('manager.movements') }}" style="font-size:0.72rem; color:var(--caramel); font-weight:700; letter-spacing:0.04em;">View all →</a>
            </div>
            <div style="flex: 1; max-height: 400px; overflow-y: auto;">
                @if($this->recentActivity->isEmpty())
                    <div style="padding:2.5rem 1.5rem; text-align:center; color:#c8b0a0;">
                        <div style="font-size:1.5rem; margin-bottom:0.5rem;">📋</div>
                        <div style="font-size:0.82rem; font-family:'Playfair Display',serif;">No recent activity</div>
                    </div>
                @else
                    @foreach($this->recentActivity as $activity)
                        <div style="display:flex; align-items:flex-start; gap:0.75rem; padding:0.8rem 1.25rem; border-bottom:1px solid rgba(74,37,24,0.06);">
                            <div style="font-size:1rem; flex-shrink:0;">{{ $activity['icon'] }}</div>
                            <div style="flex:1;">
                                <div style="font-size:0.75rem; font-weight:700; color:#3d2415;">{{ $activity['title'] }}</div>
                                <div style="font-size:0.7rem; color:#9a7a68; margin-top:0.15rem;">{{ $activity['description'] }}</div>
                                <div style="font-size:0.65rem; color:#c8b0a0; margin-top:0.2rem;">{{ $activity['user'] }} • {{ $activity['timestamp'] }}</div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        {{-- Quick Actions Panel --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">⚡ Quick Actions</span>
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
    </div>

    {{-- Row 4: Performance Metrics + Customer Insights --}}
    <div style="display:grid; grid-template-columns:2fr 1fr; gap:1.25rem;">
        {{-- Performance Metrics --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">📊 Performance Metrics</span>
            </div>
            <div style="padding:1.25rem;">
                <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:1rem;">
                    <div style="text-align:center; padding:1rem; background:#faf5ee; border-radius:10px;">
                        <div style="font-size:0.7rem; letter-spacing:0.08em; text-transform:uppercase; color:#9a7a68; margin-bottom:0.5rem;">Revenue / Hour</div>
                        <div style="font-size:1.25rem; font-weight:700; color:#8a5a20;">₱{{ number_format($this->revenuePerHour, 2) }}</div>
                    </div>
                    <div style="text-align:center; padding:1rem; background:#faf5ee; border-radius:10px;">
                        <div style="font-size:0.7rem; letter-spacing:0.08em; text-transform:uppercase; color:#9a7a68; margin-bottom:0.5rem;">Inventory Turnover</div>
                        <div style="font-size:1.25rem; font-weight:700; color:#2d5a2d;">{{ number_format($this->inventoryTurnoverRate, 1) }}%</div>
                    </div>
                    <div style="text-align:center; padding:1rem; background:#faf5ee; border-radius:10px;">
                        <div style="font-size:0.7rem; letter-spacing:0.08em; text-transform:uppercase; color:#9a7a68; margin-bottom:0.5rem;">Active Sessions</div>
                        <div style="font-size:1.25rem; font-weight:700; color:#4a2518;">{{ $this->activeCashierSessions }}</div>
                    </div>
                    <div style="text-align:center; padding:1rem; background:#faf5ee; border-radius:10px;">
                        <div style="font-size:0.7rem; letter-spacing:0.08em; text-transform:uppercase; color:#9a7a68; margin-bottom:0.5rem;">System Health</div>
                        <div style="font-size:1.25rem; font-weight:700; color:#2d5a2d;">✓ Healthy</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Customer Insights --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">👥 Customer Insights</span>
            </div>
            <div style="padding:1.25rem;">
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:0.75rem; margin-bottom:1rem;">
                    <div style="text-align:center; padding:0.75rem; background:#f8f4f0; border-radius:8px;">
                        <div style="font-size:0.6rem; text-transform:uppercase; color:#9a7a68; margin-bottom:0.25rem;">New Today</div>
                        <div style="font-size:1.1rem; font-weight:700; color:#4a2518;">{{ $this->newCustomersToday }}</div>
                    </div>
                    <div style="text-align:center; padding:0.75rem; background:#f8f4f0; border-radius:8px;">
                        <div style="font-size:0.6rem; text-transform:uppercase; color:#9a7a68; margin-bottom:0.25rem;">New This Week</div>
                        <div style="font-size:1.1rem; font-weight:700; color:#4a2518;">{{ $this->newCustomersThisWeek }}</div>
                    </div>
                </div>
                
                <div style="margin-bottom:1rem;">
                    <div style="font-size:0.7rem; text-transform:uppercase; color:#9a7a68; margin-bottom:0.5rem;">Repeat Customer Rate</div>
                    <div style="font-size:1.2rem; font-weight:700; color:#2d5a2d;">{{ number_format($this->repeatCustomerRate, 1) }}%</div>
                </div>
                
                @if(auth()->user()->role === 'admin' || $this->topCustomers->isNotEmpty())
                    <div>
                        <div style="font-size:0.7rem; text-transform:uppercase; color:#9a7a68; margin-bottom:0.5rem;">Top Customers (30 days)</div>
                        @foreach($this->topCustomers->take(3) as $customer)
                            <div style="display:flex; justify-content:space-between; align-items:center; padding:0.4rem 0; border-bottom:1px solid rgba(74,37,24,0.04);">
                                <div style="font-size:0.75rem; color:#4a2518;">
                                    {{ auth()->user()->role === 'admin' ? $customer->cashier->name : 'Customer ' . $customer->cashier_id }}
                                </div>
                                <div style="font-size:0.7rem; font-weight:700; color:#8a5a20;">₱{{ number_format($customer->total_spent, 2) }}</div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sales Trends Chart
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    const chartData = @json($this->chartPeriod === 'daily' ? $this->dailyRevenueData : ($this->chartPeriod === 'weekly' ? $this->weeklyRevenueData : $this->monthlyRevenueData));
    
    new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: chartData.map(item => item.date || item.week || item.month),
            datasets: [{
                label: 'Revenue',
                data: chartData.map(item => item.revenue),
                borderColor: '#c8813a',
                backgroundColor: 'rgba(200, 129, 58, 0.1)',
                borderWidth: 2,
                tension: 0.4,
                fill: true
            }, {
                label: 'Transactions',
                data: chartData.map(item => item.count),
                borderColor: '#4a2518',
                backgroundColor: 'rgba(74, 37, 24, 0.1)',
                borderWidth: 2,
                tension: 0.4,
                fill: true,
                yAxisID: 'y1'
            }]
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
                        font: { size: 11 }
                    }
                }
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
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
                    grid: {
                        drawOnChartArea: false,
                    },
                }
            }
        }
    });

    // Hourly Sales Pattern Chart
    const hourlyCtx = document.getElementById('hourlyChart').getContext('2d');
    const hourlyData = @json($this->hourlySalesPattern);
    
    new Chart(hourlyCtx, {
        type: 'bar',
        data: {
            labels: hourlyData.map(item => item.hour),
            datasets: [{
                label: 'Revenue',
                data: hourlyData.map(item => item.revenue),
                backgroundColor: '#c8813a',
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₱' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush
