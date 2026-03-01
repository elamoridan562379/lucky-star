<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_no', 30)->unique();
            $table->string('checkout_token', 64)->unique()->nullable();
            $table->foreignId('cashier_id')->constrained('users')->restrictOnDelete();
            $table->decimal('subtotal', 10, 2);
            $table->decimal('discount_total', 10, 2)->nullable()->default(0);
            $table->decimal('total', 10, 2);
            $table->timestamps();

            $table->index('created_at');
            $table->index('checkout_token');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
