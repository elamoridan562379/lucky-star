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
