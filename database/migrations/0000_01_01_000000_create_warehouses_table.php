<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('city')->nullable(); // Added city
            $table->text('address')->nullable();
            $table->text('description')->nullable(); // Added description
            $table->string('pic')->nullable(); // Added pic
            $table->string('phone')->nullable(); // Added phone
            $table->boolean('is_active')->default(true); // Added is_active
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warehouses');
    }
};
