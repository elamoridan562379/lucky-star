<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'transaction_id',
        'method',
        'cash_received',
        'change_amount',
        'reference_number',
        'payment_details',
    ];

    protected $casts = [
        'cash_received' => 'decimal:2',
        'change_amount' => 'decimal:2',
        'payment_details' => 'array',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
