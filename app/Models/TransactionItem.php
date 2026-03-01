<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionItem extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'transaction_id',
        'product_id',
        'qty',
        'unit_price',
        'cost_price_at_sale',
        'line_total',
    ];

    protected $casts = [
        'unit_price'         => 'decimal:2',
        'cost_price_at_sale' => 'decimal:2',
        'line_total'         => 'decimal:2',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
