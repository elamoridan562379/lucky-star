<?php

namespace App\Http\Livewire\Cashier;

use App\Models\Product;
use App\Services\SaleService;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class PosTerminal extends Component
{
    // ── State ──────────────────────────────────────────────
    public string $activeCategory = 'coffee';
    public string $search         = '';
    public array  $cart           = [];

    // ✅ make it string so it can be blank after checkout
    public string $cashReceived   = '';

    public bool   $showReceipt    = false;
    public ?array $receiptData    = null;
    public string $checkoutToken  = '';
    public ?string $errorMessage  = null;
    public bool   $processing     = false;

    protected SaleService $saleService;

    public function boot(SaleService $saleService): void
    {
        $this->saleService = $saleService;
    }

    public function mount(): void
    {
        $this->checkoutToken = Str::uuid()->toString();
        $this->cashReceived = '';
    }

    // ── Computed ───────────────────────────────────────────
    public function getProductsProperty()
    {
        return Product::active()
            ->byCategory($this->activeCategory)
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->orderBy('name')
            ->get();
    }

    public function getSubtotalProperty(): float
    {
        return collect($this->cart)->sum(fn ($item) => $item['price'] * $item['qty']);
    }

    public function getTotalProperty(): float
    {
        return $this->subtotal;
    }

    // ✅ numeric version of cashReceived (safe for comparisons)
    public function getCashFloatProperty(): float
    {
        return (float) ($this->cashReceived === '' ? 0 : $this->cashReceived);
    }

    public function getChangeProperty(): float
    {
        return max(0, $this->cashFloat - $this->total);
    }

    public function getCartCountProperty(): int
    {
        return collect($this->cart)->sum('qty');
    }

    // ── Cart actions ───────────────────────────────────────
    public function addToCart(int $productId): void
    {
        $product = Product::active()->find($productId);
        if (! $product) return;

        $key = (string) $productId;

        if (isset($this->cart[$key])) {
            if ($this->cart[$key]['qty'] >= $product->stock_qty) {
                $this->errorMessage = "Only {$product->stock_qty} {$product->name} in stock.";
                return;
            }
            $this->cart[$key]['qty']++;
        } else {
            if ($product->stock_qty < 1) {
                $this->errorMessage = "{$product->name} is out of stock.";
                return;
            }
            $this->cart[$key] = [
                'id'    => $product->id,
                'name'  => $product->name,
                'price' => (float) $product->selling_price,
                'qty'   => 1,
                'stock' => $product->stock_qty,
            ];
        }

        $this->errorMessage = null;
    }

    public function incrementQty(string $key): void
    {
        if (!isset($this->cart[$key])) return;

        $product = Product::find($this->cart[$key]['id']);
        if ($product && $this->cart[$key]['qty'] < $product->stock_qty) {
            $this->cart[$key]['qty']++;
        } else {
            $this->errorMessage = "Cannot add more — not enough stock.";
        }
    }

    public function decrementQty(string $key): void
    {
        if (!isset($this->cart[$key])) return;

        if ($this->cart[$key]['qty'] > 1) {
            $this->cart[$key]['qty']--;
        } else {
            $this->removeFromCart($key);
        }
    }

    public function removeFromCart(string $key): void
    {
        unset($this->cart[$key]);
        $this->cart = array_values($this->cart) ? $this->cart : [];
    }

    // ✅ clears cart + makes cash tendered BLANK
    public function clearCart(): void
    {
        $this->cart = [];
        $this->cashReceived = ''; // ✅ blank after confirm
        $this->errorMessage = null;
        $this->processing = false;
    }

    // ── Checkout ───────────────────────────────────────────
    public function confirmCheckout(): void
    {
        $this->errorMessage = null;

        if (empty($this->cart)) {
            $this->errorMessage = 'Cart is empty.';
            return;
        }

        if ($this->cashFloat < $this->total) {
            $this->errorMessage = 'Cash received is less than total.';
            return;
        }

        $this->processing = true;

        try {
            $cartPayload = array_values(array_map(
                fn ($item) => ['id' => $item['id'], 'qty' => $item['qty']],
                $this->cart
            ));

            $transaction = $this->saleService->checkout(
                $cartPayload,
                $this->cashFloat,
                $this->checkoutToken,
                auth()->id()
            );

            $this->receiptData = $this->buildReceiptData($transaction);
            $this->showReceipt = true;

            // ✅ clear cart + blank cash
            $this->clearCart();

            // ✅ new token forces the cash input to rebuild (wire:key)
            $this->checkoutToken = Str::uuid()->toString();

            // ✅ toast
            $this->dispatch('saleCompleted');

        } catch (ValidationException $e) {
            $this->errorMessage = collect($e->errors())->flatten()->implode(' ');
        } catch (\Exception $e) {
            $this->errorMessage = 'Checkout failed. Please try again.';
            \Log::error('POS Checkout error', ['error' => $e->getMessage()]);
        } finally {
            $this->processing = false;
        }
    }

    public function closeReceipt(): void
    {
        $this->showReceipt = false;
        $this->receiptData = null;
    }

    // ── Helpers ────────────────────────────────────────────
    private function buildReceiptData($transaction): array
    {
        return [
            'receipt_no'    => $transaction->receipt_no,
            'cashier'       => $transaction->cashier->name,
            'created_at'    => $transaction->created_at->format('Y-m-d H:i:s'),
            'items'         => $transaction->items->map(fn ($i) => [
                'name'       => $i->product->name,
                'qty'        => $i->qty,
                'unit_price' => $i->unit_price,
                'line_total' => $i->line_total,
            ])->toArray(),
            'subtotal'      => $transaction->subtotal,
            'total'         => $transaction->total,
            'cash_received' => $transaction->payment->cash_received,
            'change_amount' => $transaction->payment->change_amount,
        ];
    }

    public function render()
    {
        return view('livewire.cashier.pos-terminal')
            ->layout('layouts.cashier');
    }
}
