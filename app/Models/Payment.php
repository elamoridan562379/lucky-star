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
    ];

    protected $casts = [
        'cash_received' => 'decimal:2',
        'change_amount' => 'decimal:2',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
