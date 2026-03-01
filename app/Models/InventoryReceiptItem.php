<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryReceiptItem extends Model
{
    protected $table = 'inventory_receipt_items';

    public $timestamps = false;

    protected $fillable = [
        'inventory_receipt_id',
        'product_id',
        'qty',
        'unit_cost',
    ];


    public function receipt()
    {
        return $this->belongsTo(InventoryReceipt::class, 'inventory_receipt_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
