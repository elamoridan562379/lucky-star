<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Transaction;
use App\Models\User;
use App\Services\InventoryService;
use App\Services\SaleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class PosSystemTest extends TestCase
{
    use RefreshDatabase;

    private SaleService $saleService;
    private InventoryService $inventoryService;
    private User $cashier;
    private User $manager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->saleService      = app(SaleService::class);
        $this->inventoryService = app(InventoryService::class);

        $this->cashier = User::factory()->create(['role' => 'cashier']);
        $this->manager = User::factory()->create(['role' => 'manager']);
    }

    // ── Helper ─────────────────────────────────────────────
    private function makeProduct(array $overrides = []): Product
    {
        return Product::create(array_merge([
            'name'          => 'Test Coffee ' . Str::random(4),
            'category'      => 'coffee',
            'selling_price' => 100.00,
            'cost_price'    => 40.00,
            'stock_qty'     => 20,
            'reorder_level' => 5,
            'is_active'     => true,
        ], $overrides));
    }

    // ══════════════════════════════════════════════════════
    // TEST 1: Stock-in increases stock_qty and logs movement
    // ══════════════════════════════════════════════════════
    public function test_stock_in_increases_stock_and_logs_movement(): void
    {
        $product     = $this->makeProduct(['stock_qty' => 10]);
        $initialQty  = $product->stock_qty;

        $this->actingAs($this->manager);

        $movement = $this->inventoryService->stockIn(
            productId:  $product->id,
            qty:        15,
            reason:     'restock',
            notes:      'Test restock',
            createdBy:  $this->manager->id,
        );

        // Stock increased
        $this->assertEquals($initialQty + 15, $product->fresh()->stock_qty);

        // Movement logged correctly
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'type'       => 'in',
            'reason'     => 'restock',
            'qty'        => 15,
            'created_by' => $this->manager->id,
        ]);

        $this->assertEquals('in', $movement->type);
        $this->assertEquals(15, $movement->qty);
    }

    // ══════════════════════════════════════════════════════
    // TEST 2: Sale creates stock_out movement and decrements stock
    // ══════════════════════════════════════════════════════
    public function test_sale_creates_stock_out_movement_and_decrements_stock(): void
    {
        $product    = $this->makeProduct(['stock_qty' => 10, 'selling_price' => 100]);
        $token      = Str::uuid()->toString();
        $cashierId  = $this->cashier->id;

        $this->actingAs($this->cashier);

        $transaction = $this->saleService->checkout(
            cart:         [['id' => $product->id, 'qty' => 3]],
            cashReceived: 400,
            token:        $token,
            cashierId:    $cashierId
        );

        // Stock decremented
        $this->assertEquals(7, $product->fresh()->stock_qty);

        // Stock movement created
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'type'       => 'out',
            'reason'     => 'sale',
            'qty'        => 3,
            'ref_type'   => 'transaction',
            'ref_id'     => $transaction->id,
            'created_by' => $cashierId,
        ]);

        // Transaction created
        $this->assertDatabaseHas('transactions', [
            'id'         => $transaction->id,
            'cashier_id' => $cashierId,
            'total'      => 300.00,
        ]);

        // Payment recorded
        $this->assertDatabaseHas('payments', [
            'transaction_id' => $transaction->id,
            'cash_received'  => 400.00,
            'change_amount'  => 100.00,
        ]);
    }

    // ══════════════════════════════════════════════════════
    // TEST 3: Checkout is blocked when stock is insufficient
    // ══════════════════════════════════════════════════════
    public function test_checkout_blocked_when_stock_insufficient(): void
    {
        $product = $this->makeProduct(['stock_qty' => 2]);
        $token   = Str::uuid()->toString();

        $this->expectException(ValidationException::class);

        $this->saleService->checkout(
            cart:         [['id' => $product->id, 'qty' => 5]], // Requesting 5, only 2 available
            cashReceived: 1000,
            token:        $token,
            cashierId:    $this->cashier->id
        );

        // Stock must NOT have changed
        $this->assertEquals(2, $product->fresh()->stock_qty);

        // No transaction created
        $this->assertDatabaseCount('transactions', 0);
        $this->assertDatabaseCount('stock_movements', 0);
    }

    // ══════════════════════════════════════════════════════
    // TEST 4: Duplicate checkout token is rejected (idempotency)
    // ══════════════════════════════════════════════════════
    public function test_duplicate_checkout_token_returns_same_transaction(): void
    {
        $product = $this->makeProduct(['stock_qty' => 20]);
        $token   = Str::uuid()->toString();

        // First checkout
        $txn1 = $this->saleService->checkout(
            cart:         [['id' => $product->id, 'qty' => 2]],
            cashReceived: 300,
            token:        $token,
            cashierId:    $this->cashier->id
        );

        $stockAfterFirst = $product->fresh()->stock_qty;

        // Second checkout with SAME token
        $txn2 = $this->saleService->checkout(
            cart:         [['id' => $product->id, 'qty' => 2]],
            cashReceived: 300,
            token:        $token,
            cashierId:    $this->cashier->id
        );

        // Returns same transaction
        $this->assertEquals($txn1->id, $txn2->id);

        // Stock was only deducted ONCE
        $this->assertEquals($stockAfterFirst, $product->fresh()->stock_qty);

        // Only one transaction in DB
        $this->assertDatabaseCount('transactions', 1);

        // Only one set of movements (qty=2, not 4)
        $movementsCount = StockMovement::where('product_id', $product->id)->count();
        $this->assertEquals(1, $movementsCount);
    }

    // ══════════════════════════════════════════════════════
    // TEST 5: Manual stock-out blocked if insufficient stock
    // ══════════════════════════════════════════════════════
    public function test_manual_stock_out_blocked_if_insufficient(): void
    {
        $product = $this->makeProduct(['stock_qty' => 3]);

        $this->expectException(ValidationException::class);

        $this->inventoryService->manualStockOut(
            productId: $product->id,
            qty:       10, // More than available
            reason:    'wastage',
            notes:     'Test',
            createdBy: $this->manager->id,
        );

        // Stock unchanged
        $this->assertEquals(3, $product->fresh()->stock_qty);

        // No movement created
        $this->assertDatabaseCount('stock_movements', 0);
    }

    // ══════════════════════════════════════════════════════
    // BONUS: Checkout rolls back atomically on partial failure
    // ══════════════════════════════════════════════════════
    public function test_checkout_is_fully_atomic_on_multi_item_failure(): void
    {
        $product1 = $this->makeProduct(['stock_qty' => 10, 'selling_price' => 100]);
        $product2 = $this->makeProduct(['stock_qty' => 1,  'selling_price' => 80]);
        $token    = Str::uuid()->toString();

        try {
            $this->saleService->checkout(
                cart: [
                    ['id' => $product1->id, 'qty' => 3],
                    ['id' => $product2->id, 'qty' => 5], // This will fail
                ],
                cashReceived: 1000,
                token:        $token,
                cashierId:    $this->cashier->id
            );
        } catch (ValidationException $e) {
            // Expected
        }

        // BOTH products' stock must be unchanged (full rollback)
        $this->assertEquals(10, $product1->fresh()->stock_qty);
        $this->assertEquals(1,  $product2->fresh()->stock_qty);

        // No transaction, no movements
        $this->assertDatabaseCount('transactions', 0);
        $this->assertDatabaseCount('stock_movements', 0);
    }

    // ══════════════════════════════════════════════════════
    // BONUS: Inactive product is blocked at checkout
    // ══════════════════════════════════════════════════════
    public function test_inactive_product_blocked_at_checkout(): void
    {
        $product = $this->makeProduct(['is_active' => false, 'stock_qty' => 50]);
        $token   = Str::uuid()->toString();

        $this->expectException(ValidationException::class);

        $this->saleService->checkout(
            cart:         [['id' => $product->id, 'qty' => 1]],
            cashReceived: 200,
            token:        $token,
            cashierId:    $this->cashier->id
        );
    }

    // ══════════════════════════════════════════════════════
    // BONUS: RBAC — cashier cannot access manager routes
    // ══════════════════════════════════════════════════════
    public function test_cashier_cannot_access_manager_routes(): void
    {
        $this->actingAs($this->cashier);

        $this->get(route('dashboard'))->assertStatus(403);
        $this->get(route('products.index'))->assertStatus(403);
        $this->get(route('inventory.stock-in'))->assertStatus(403);
        $this->get(route('reports.sales'))->assertStatus(403);
    }

    public function test_manager_can_access_manager_routes(): void
    {
        $this->actingAs($this->manager);

        // At minimum should not 403 (may redirect etc. but not forbidden)
        $this->get(route('dashboard'))->assertStatus(200);
    }
}
