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
        Schema::create('stock_headers', function (Blueprint $table) {
            $table->id();
            $table->string('document_number')->unique();
            $table->enum('type', ['in', 'out']);
            $table->dateTime('transaction_date');
            $table->text('notes')->nullable();
            
            // Receipt / Signature fields
            $table->string('sender_name')->nullable();
            $table->string('receiver_name')->nullable();
            $table->string('sender_signature')->nullable(); // File path
            $table->string('receiver_signature')->nullable(); // File path
            $table->boolean('receipt_locked')->default(false);
            
            // Created by
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_headers');
    }
};
