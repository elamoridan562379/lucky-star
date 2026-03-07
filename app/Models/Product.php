<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'selling_price',
        'cost_price',
        'stock_qty',
        'reorder_level',
        'low_stock_threshold',
        'is_active',
    ];

    protected $casts = [
        'selling_price' => 'decimal:2',
        'cost_price'    => 'decimal:2',
        'is_active'     => 'boolean',
    ];

    // ── Scopes ─────────────────────────────────────────────
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeLowStock($query)
    {
        return $query->whereRaw('stock_qty <= low_stock_threshold')->where('is_active', true);
    }

    public function scopeCriticalStock($query)
    {
        return $query->whereRaw('stock_qty <= (low_stock_threshold / 2)')->where('is_active', true);
    }

    // ── Helpers ────────────────────────────────────────────
    public function isLowStock(): bool
    {
        return $this->stock_qty <= $this->low_stock_threshold;
    }

    public function isCriticalStock(): bool
    {
        return $this->stock_qty <= ($this->low_stock_threshold / 2);
    }

    public function getStockStatusAttribute(): string
    {
        if ($this->isCriticalStock()) return 'critical';
        if ($this->isLowStock()) return 'low';
        return 'normal';
    }

    public function getStockStatusColorAttribute(): string
    {
        return match ($this->stock_status) {
            'critical' => 'red',
            'low' => 'yellow',
            'normal' => 'green',
            default => 'gray',
        };
    }

    public function hasStock(int $qty = 1): bool
    {
        return $this->stock_qty >= $qty;
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'coffee'      => 'Coffee',
            'iced_coffee' => 'Iced Coffee',
            'non_coffee'  => 'Non-Coffee',
            default       => ucfirst($this->category),
        };
    }

    // ── Relationships ──────────────────────────────────────
    public function transactionItems()
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }
}
