<?php

namespace App\Http\Livewire\Cashier;

use App\Models\Transaction;
use Livewire\Component;
use Livewire\WithPagination;

class TransactionHistory extends Component
{
    use WithPagination;

    public string $dateFrom = '';
    public string $dateTo   = '';
    public string $search   = '';

    protected $queryString = ['dateFrom', 'dateTo', 'search'];

    public function mount(): void
    {
        $this->dateFrom = now()->format('Y-m-d');
        $this->dateTo   = now()->format('Y-m-d');
    }

    public function updatingSearch(): void  { $this->resetPage(); }
    public function updatingDateFrom(): void { $this->resetPage(); }
    public function updatingDateTo(): void   { $this->resetPage(); }

    public function render()
    {
        $query = Transaction::with(['cashier', 'payment'])
            ->when($this->dateFrom, fn($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo,   fn($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->when($this->search,   fn($q) => $q->where('receipt_no', 'like', "%{$this->search}%"))
            ->orderByDesc('created_at');

        // Cashier sees only their own transactions
        if (auth()->user()->isCashier()) {
            $query->where('cashier_id', auth()->id());
        }

        $layout = auth()->user()->isCashier() ? 'layouts.cashier' : 'layouts.manager';

        return view('livewire.cashier.transaction-history', [
            'transactions' => $query->paginate(20),
        ])->layout($layout);
    }
}
