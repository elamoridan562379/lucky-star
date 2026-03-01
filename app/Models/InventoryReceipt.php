<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryReceipt extends Model
{
    protected $fillable = [
        'receipt_no',
        'received_at',
        'supplier',
        'remarks',
        'created_by',
    ];

    protected $casts = [
        'received_at' => 'date',
    ];

    public function items()
    {
        return $this->hasMany(InventoryReceiptItem::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
