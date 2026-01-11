<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stock_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_header_id')->nullable()->constrained('stock_headers')->onDelete('cascade');
            $table->foreignId('item_id')->constrained()->onDelete('restrict');
            $table->foreignId('user_id')->constrained()->onDelete('restrict');
            $table->string('type', 10); // 'in' or 'out'
            $table->integer('quantity');
            $table->integer('stock_before');
            $table->integer('stock_after');
            $table->text('notes')->nullable();
            $table->timestamp('transaction_date');
            $table->timestamps();
            
            // Indexes for performance and reporting
            $table->index('item_id');
            $table->index('user_id');
            $table->index('type');
            $table->index('transaction_date');
            $table->index(['item_id', 'transaction_date']);
            $table->index('stock_header_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_transactions');
    }
};
