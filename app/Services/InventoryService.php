<?php

namespace App\Services;

use App\Models\InventoryReceipt;
use App\Models\InventoryReceiptItem;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InventoryService
{
    /**
     * Process a stock-in for a single product (simple form).
     *
     * @throws ValidationException|\Throwable
     */
    public function stockIn(
        int    $productId,
        int    $qty,
        string $reason = 'restock',
        string $notes = '',
        int    $createdBy = 0,
        ?string $supplier = null,
        ?string $receivedAt = null,
        ?float  $unitCost = null,
    ): StockMovement {
        if ($qty <= 0) {
            throw ValidationException::withMessages(['qty' => ['Quantity must be greater than zero.']]);
        }

        return DB::transaction(function () use (
            $productId, $qty, $reason, $notes, $createdBy, $supplier, $receivedAt, $unitCost
        ) {
            $product = Product::where('id', $productId)->lockForUpdate()->firstOrFail();

            // Optionally create an inventory receipt record
            $receiptId   = null;
            $receiptType = null;

            if ($supplier || $receivedAt) {
                $irReceiptNo = $this->generateInventoryReceiptNo();
                $ir = InventoryReceipt::create([
                    'receipt_no'  => $irReceiptNo,
                    'received_at' => $receivedAt ?? now()->toDateString(),
                    'supplier'    => $supplier,
                    'remarks'     => $notes,
                    'created_by'  => $createdBy,
                ]);
                InventoryReceiptItem::create([
                    'inventory_receipt_id' => $ir->id,
                    'product_id'           => $productId,
                    'qty'                  => $qty,
                    'unit_cost'            => $unitCost,
                ]);
                $receiptId   = $ir->id;
                $receiptType = 'inventory_receipt';
            }

            $product->increment('stock_qty', $qty);

            $movement = StockMovement::create([
                'product_id' => $productId,
                'type'       => 'in',
                'reason'     => $reason,
                'qty'        => $qty,
                'ref_type'   => $receiptType,
                'ref_id'     => $receiptId,
                'notes'      => $notes,
                'created_by' => $createdBy,
            ]);

            return $movement;
        });
    }

    /**
     * Process a manual stock-out (wastage / adjustment). Manager only.
     *
     * @throws ValidationException|\Throwable
     */
    public function manualStockOut(
        int    $productId,
        int    $qty,
        string $reason,
        string $notes = '',
        int    $createdBy = 0,
    ): StockMovement {
        if (! in_array($reason, ['wastage', 'adjustment'])) {
            throw ValidationException::withMessages(['reason' => ['Reason must be wastage or adjustment.']]);
        }
        if ($qty <= 0) {
            throw ValidationException::withMessages(['qty' => ['Quantity must be greater than zero.']]);
        }

        return DB::transaction(function () use ($productId, $qty, $reason, $notes, $createdBy) {
            $product = Product::where('id', $productId)->lockForUpdate()->firstOrFail();

            if ($product->stock_qty < $qty) {
                throw ValidationException::withMessages([
                    'qty' => ["Insufficient stock. Available: {$product->stock_qty}, requested: {$qty}."],
                ]);
            }

            $product->decrement('stock_qty', $qty);

            $movement = StockMovement::create([
                'product_id' => $productId,
                'type'       => 'out',
                'reason'     => $reason,
                'qty'        => $qty,
                'ref_type'   => null,
                'ref_id'     => null,
                'notes'      => $notes,
                'created_by' => $createdBy,
            ]);

            return $movement;
        });
    }

    /**
     * Verify that a product's stock_qty matches the sum of its movements.
     * Returns null if no discrepancy, or the expected qty if mismatch.
     */
    public function reconcile(int $productId): ?array
    {
        $product = Product::findOrFail($productId);
        $sum     = StockMovement::where('product_id', $productId)
            ->selectRaw("SUM(CASE WHEN type='in' THEN qty ELSE -qty END) as net_qty")
            ->value('net_qty') ?? 0;

        if ((int) $sum !== (int) $product->stock_qty) {
            return [
                'product_id'  => $productId,
                'name'        => $product->name,
                'recorded_qty' => $product->stock_qty,
                'computed_qty' => (int) $sum,
                'discrepancy' => $product->stock_qty - (int) $sum,
            ];
        }

        return null;
    }

    private function generateInventoryReceiptNo(): string
    {
        $today  = now()->format('Ymd');
        $prefix = "IR-{$today}-";
        $last   = InventoryReceipt::where('receipt_no', 'like', "{$prefix}%")
            ->lockForUpdate()
            ->orderByDesc('receipt_no')
            ->value('receipt_no');
        $seq = $last ? (int) substr($last, -4) + 1 : 1;
        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}
