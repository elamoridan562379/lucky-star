<?php

namespace App\Http\Livewire\Manager;

use App\Models\Transaction;
use App\Models\TransactionItem;
use Livewire\Component;
use Livewire\WithPagination;

class SalesReport extends Component
{
    use WithPagination;

    public string $dateFrom = '';
    public string $dateTo   = '';

    protected $queryString = ['dateFrom', 'dateTo'];

    public function mount(): void
    {
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo   = now()->format('Y-m-d');
    }

    public function updatingDateFrom(): void { $this->resetPage(); }
    public function updatingDateTo(): void   { $this->resetPage(); }

    // ── Summary ────────────────────────────────────────────
    public function getSummaryProperty(): array
    {
        $q = Transaction::whereBetween('created_at', [
            $this->dateFrom . ' 00:00:00',
            $this->dateTo   . ' 23:59:59',
        ]);

        return [
            'total_sales'  => $q->sum('total'),
            'total_count'  => $q->count(),
            'avg_sale'     => $q->count() ? $q->sum('total') / $q->count() : 0,
        ];
    }

    // ── Best sellers ───────────────────────────────────────
    public function getBestSellersProperty()
    {
        return TransactionItem::with('product')
            ->whereHas('transaction', fn($q) => $q->whereBetween('created_at', [
                $this->dateFrom . ' 00:00:00',
                $this->dateTo   . ' 23:59:59',
            ]))
            ->selectRaw('product_id, SUM(qty) as total_qty, SUM(line_total) as total_revenue')
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->with('product')
            ->limit(10)
            ->get();
    }

    // ── Chart Data ────────────────────────────────────────────
    public function getChartDataProperty(): array
    {
        $transactions = Transaction::whereBetween('created_at', [
            $this->dateFrom . ' 00:00:00',
            $this->dateTo   . ' 23:59:59',
        ])
        ->orderBy('created_at')
        ->get()
        ->groupBy(function($date) {
            return $date->created_at->format('Y-m-d');
        });

        $labels = [];
        $revenue = [];
        $count = [];

        $period = new \DatePeriod(
            new \DateTime($this->dateFrom),
            new \DateInterval('P1D'),
            new \DateTime($this->dateTo . ' +1 day')
        );

        foreach ($period as $day) {
            $dateStr = $day->format('Y-m-d');
            $dayTransactions = $transactions->get($dateStr, collect());
            
            $labels[] = $day->format('M d');
            $revenue[] = $dayTransactions->sum('total');
            $count[] = $dayTransactions->count();
        }

        return [
            'labels' => $labels,
            'revenue' => $revenue,
            'transactions' => $count,
        ];
    }

    // ── Category Data ────────────────────────────────────────────
    public function getCategoryDataProperty(): array
    {
        $categoryData = TransactionItem::with('product')
            ->whereHas('transaction', fn($q) => $q->whereBetween('created_at', [
                $this->dateFrom . ' 00:00:00',
                $this->dateTo   . ' 23:59:59',
            ]))
            ->selectRaw('products.category, SUM(qty) as total_qty, SUM(line_total) as total_revenue')
            ->join('products', 'transaction_items.product_id', '=', 'products.id')
            ->groupBy('products.category')
            ->orderByDesc('total_revenue')
            ->get();

        $labels = [];
        $data = [];
        $colors = [
            '#8a5a20', '#2d6a2d', '#c8811a', '#8a2020', '#4a2c00',
            '#6a4a35', '#9a7a68', '#c8b0a0', '#e8d0a0', '#fdf5e8'
        ];

        foreach ($categoryData as $category) {
            $categoryLabel = match ($category->category) {
                'coffee' => 'Coffee',
                'iced_coffee' => 'Iced Coffee',
                'non_coffee' => 'Non-Coffee',
                default => ucfirst($category->category),
            };
            
            $labels[] = $categoryLabel;
            $data[] = $category->total_qty;
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'colors' => array_slice($colors, 0, count($labels)),
        ];
    }

    // ── Export Methods ────────────────────────────────────────────
    public function exportCsv()
    {
        $transactions = Transaction::with(['cashier', 'items.product'])
            ->whereBetween('created_at', [
                $this->dateFrom . ' 00:00:00',
                $this->dateTo   . ' 23:59:59',
            ])
            ->orderByDesc('created_at')
            ->get();

        $csv = "Receipt #,Date,Cashier,Total,Items\n";
        
        foreach ($transactions as $txn) {
            $items = $txn->items->map(function($item) {
                return $item->product->name . ' (x' . $item->qty . ')';
            })->implode('; ');
            
            $csv .= "{$txn->receipt_no},{$txn->created_at->format('Y-m-d H:i')},{$txn->cashier->name},{$txn->total},\"{$items}\"\n";
        }

        $filename = "sales_report_{$this->dateFrom}_to_{$this->dateTo}.csv";
        
        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }

    public function exportPdf()
    {
        // For now, redirect to print view
        // In a real implementation, you'd use a PDF library like DomPDF
        return $this->printReport();
    }

    public function printReport()
    {
        $transactions = Transaction::with(['cashier', 'items.product', 'payment'])
            ->whereBetween('created_at', [
                $this->dateFrom . ' 00:00:00',
                $this->dateTo   . ' 23:59:59',
            ])
            ->orderByDesc('created_at')
            ->get();

        return view('reports.sales-print', [
            'summary' => $this->summary,
            'bestSellers' => $this->bestSellers,
            'transactions' => $transactions,
            'dateFrom' => $this->dateFrom,
            'dateTo' => $this->dateTo,
            'chartData' => $this->chartData,
            'categoryData' => $this->categoryData,
        ]);
    }

    // ── Transactions list ──────────────────────────────────
    public function render()
    {
        $transactions = Transaction::with(['cashier', 'items.product', 'payment'])
            ->whereBetween('created_at', [
                $this->dateFrom . ' 00:00:00',
                $this->dateTo   . ' 23:59:59',
            ])
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('livewire.manager.sales-report', [
            'transactions' => $transactions,
        ])->layout('layouts.manager');
    }
}
