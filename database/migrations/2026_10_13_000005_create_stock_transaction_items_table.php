<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_transaction_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_transaction_id')->constrained()->cascadeOnDelete();
            $table->foreignId('item_id')->constrained()->cascadeOnDelete();
            $table->decimal('quantity', 10, 2);
            $table->decimal('unit_cost', 14, 2)->default(0);
            $table->decimal('subtotal', 14, 2)->default(0);
            $table->date('expiry_date')->nullable();
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_transaction_items');
    }
};
