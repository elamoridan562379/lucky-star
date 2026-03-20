<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'receipt_no',
        'checkout_token',
        'cashier_id',
        'subtotal',
        'discount_total',
        'total',
    ];

    protected $casts = [
        'subtotal'       => 'decimal:2',
        'discount_total' => 'decimal:2',
        'total'          => 'decimal:2',
        'created_at'     => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────
    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'ref_id')
            ->where('ref_type', 'transaction');
    }
}
