<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $fillable = [
        'product_id',
        'type',
        'qty',
        'reason',
        'ref_type',
        'ref_id',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    // ── Scopes ─────────────────────────────────────────────
    public function scopeIn($query)
    {
        return $query->where('type', 'in');
    }

    public function scopeOut($query)
    {
        return $query->where('type', 'out');
    }

    public function scopeForProduct($query, int $productId)
    {
        return $query->where('product_id', $productId);
    }

    // ── Relationships ──────────────────────────────────────
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ── Helpers ────────────────────────────────────────────
    public function getRefLinkAttribute(): ?string
    {
        if ($this->ref_type === 'transaction' && $this->ref_id) {
            return route('transactions.show', $this->ref_id);
        }
        return null;
    }

    public function getTypeLabelAttribute(): string
    {
        return $this->type === 'in' ? 'Stock In' : 'Stock Out';
    }

    public function getReasonLabelAttribute(): string
    {
        return match ($this->reason) {
            'sale'        => 'Sale',
            'restock'     => 'Restock',
            'adjustment'  => 'Adjustment',
            'wastage'     => 'Wastage',
            'void'        => 'Void',
            default       => ucfirst($this->reason),
        };
    }
}
