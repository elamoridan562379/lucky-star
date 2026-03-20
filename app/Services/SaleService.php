<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaleService
{
    /**
     * Process a checkout atomically.
     *
     * $cart = [
     *   ['id' => 1, 'qty' => 2],
     *   ['id' => 3, 'qty' => 1],
     * ]
     *
     * @throws ValidationException
     * @throws \Throwable
     */
    public function checkout(array $cart, float $cashReceived, string $token, int $cashierId, string $paymentMethod = 'cash', ?string $referenceNumber = null): Transaction
    {
        // ── Step 0: Idempotency check (outside transaction) ──────────────
        $existing = Transaction::with(['items.product', 'payment', 'cashier'])
            ->where('checkout_token', $token)
            ->first();

        if ($existing) {
            return $existing;
        }

        // ── Step 1: Open DB transaction ───────────────────────────────────
        return DB::transaction(function () use ($cart, $cashReceived, $token, $cashierId, $paymentMethod, $referenceNumber) {

            // ── Step 2: Lock product rows ─────────────────────────────────
            $productIds = collect($cart)->pluck('id')->unique()->values()->all();
            $products   = Product::whereIn('id', $productIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            // ── Step 3: Validate each cart item ───────────────────────────
            $errors = [];
            foreach ($cart as $item) {
                $product = $products->get($item['id']);

                if (! $product) {
                    $errors[] = "Product ID {$item['id']} not found.";
                    continue;
                }
                if (! $product->is_active) {
                    $errors[] = "{$product->name} is no longer available.";
                    continue;
                }
                if ($product->stock_qty < $item['qty']) {
                    $errors[] = "Insufficient stock for {$product->name}. Available: {$product->stock_qty}, requested: {$item['qty']}.";
                }
            }

            if (! empty($errors)) {
                throw ValidationException::withMessages(['cart' => $errors]);
            }

            // ── Step 3.5: Validate payment method ───────────────────────────
            $validPaymentMethods = ['cash', 'gcash', 'paymaya'];
            if (! in_array($paymentMethod, $validPaymentMethods)) {
                throw ValidationException::withMessages([
                    'payment_method' => ["Invalid payment method: {$paymentMethod}"],
                ]);
            }

            if (in_array($paymentMethod, ['gcash', 'paymaya']) && empty($referenceNumber)) {
                throw ValidationException::withMessages([
                    'reference_number' => ["Reference number is required for {$paymentMethod} payments."],
                ]);
            }

            // Validate reference number format
            if ($paymentMethod === 'gcash' && !empty($referenceNumber)) {
                // GCash reference numbers are typically 9-12 digits
                if (!preg_match('/^\d{9,12}$/', $referenceNumber)) {
                    throw ValidationException::withMessages([
                        'reference_number' => ["GCash reference number must be 9-12 digits."],
                    ]);
                }
            }

            if ($paymentMethod === 'paymaya' && !empty($referenceNumber)) {
                // PayMaya reference numbers are typically 8-12 alphanumeric
                if (!preg_match('/^[A-Za-z0-9]{8,12}$/', $referenceNumber)) {
                    throw ValidationException::withMessages([
                        'reference_number' => ["PayMaya reference number must be 8-12 alphanumeric characters."],
                    ]);
                }
            }

            // ── Step 4: Calculate totals ──────────────────────────────────
            $subtotal = 0;
            foreach ($cart as $item) {
                $subtotal += $products[$item['id']]->selling_price * $item['qty'];
            }
            $total        = $subtotal; // discount extension point
            $changeAmount = $cashReceived - $total;

            if ($paymentMethod === 'cash' && $cashReceived < $total) {
                throw ValidationException::withMessages([
                    'cash_received' => ["Cash received (₱{$cashReceived}) is less than total (₱{$total})."],
                ]);
            }

            // ── Step 5: Create transaction header ─────────────────────────
            $transaction = Transaction::create([
                'receipt_no'     => $this->generateReceiptNo(),
                'checkout_token' => $token,
                'cashier_id'     => $cashierId,
                'subtotal'       => $subtotal,
                'discount_total' => 0,
                'total'          => $total,
            ]);

            // ── Step 6: Create transaction items ──────────────────────────
            foreach ($cart as $item) {
                $product = $products[$item['id']];
                TransactionItem::create([
                    'transaction_id'     => $transaction->id,
                    'product_id'         => $product->id,
                    'qty'                => $item['qty'],
                    'unit_price'         => $product->selling_price,
                    'cost_price_at_sale' => $product->cost_price,
                    'line_total'         => $product->selling_price * $item['qty'],
                ]);
            }

            // ── Step 7: Create payment record ─────────────────────────────
            $paymentData = [
                'transaction_id' => $transaction->id,
                'method'         => $paymentMethod,
            ];

            if ($paymentMethod === 'cash') {
                $paymentData['cash_received'] = $cashReceived;
                $paymentData['change_amount'] = $changeAmount;
            } else {
                $paymentData['reference_number'] = $referenceNumber;
                $paymentData['cash_received'] = $total; // For consistency
                $paymentData['change_amount'] = 0; // No change for digital payments
            }

            Payment::create($paymentData);

            // ── Step 8: Deduct stock + log movements ──────────────────────
            foreach ($cart as $item) {
                $product = $products[$item['id']];

                $product->decrement('stock_qty', $item['qty']);

                StockMovement::create([
                    'product_id' => $product->id,
                    'type'       => 'out',
                    'reason'     => 'sale',
                    'qty'        => $item['qty'],
                    'ref_type'   => 'transaction',
                    'ref_id'     => $transaction->id,
                    'created_by' => $cashierId,
                ]);
            }

            // ── Commit ─────────────────────────────────────────────────────
            return $transaction->load(['items.product', 'payment', 'cashier']);
        });
    }

    /**
     * Generate a daily-reset receipt number: LS-YYYYMMDD-0001
     * Uses a locking SELECT to prevent duplicate numbers under concurrency.
     */
    private function generateReceiptNo(): string
    {
        $today  = now()->format('Ymd');
        $prefix = "LS-{$today}-";

        // Lock the sequence row for this day within the existing transaction
        $last = Transaction::where('receipt_no', 'like', "{$prefix}%")
            ->lockForUpdate()
            ->orderByDesc('receipt_no')
            ->value('receipt_no');

        if ($last) {
            $sequence = (int) substr($last, -4) + 1;
        } else {
            $sequence = 1;
        }

        return $prefix . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}
