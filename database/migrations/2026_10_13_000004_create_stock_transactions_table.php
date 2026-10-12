<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_code')->unique(); // e.g., IN-20261013-0001, OUT-..., ADJ-...
            $table->string('type'); // in, out, adjustment
            $table->date('date');
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('destination_or_reason'); // Pembelian Supplier, Pemakaian Bar, Kitchen, Rusak/Spill, Stock Opname
            $table->string('reference_number')->nullable(); // No Faktur / Surat Jalan / Form Opname
            $table->text('notes')->nullable();
            $table->decimal('total_cost', 14, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_transactions');
    }
};
