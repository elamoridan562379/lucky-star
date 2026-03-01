<?php

namespace App\Http\Livewire\Cashier;

use App\Models\Transaction;
use Livewire\Component;

class ReceiptView extends Component
{
    public Transaction $transaction;

    public function mount(int $id): void
    {
        $this->transaction = Transaction::with(['items.product', 'payment', 'cashier'])
            ->findOrFail($id);

        // Cashier can only view their own receipts
        if (auth()->user()->isCashier() && $this->transaction->cashier_id !== auth()->id()) {
            abort(403);
        }
    }

    public function render()
    {
        return view('livewire.cashier.receipt-view')
            ->layout('layouts.print');
    }
}
