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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            // $table->uuid('uuid')->unique();
            $table->string('order_number')->unique(); // ORD-...
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['retail','wholesale'])->default('retail');
            $table->decimal('total_price', 12, 2);
            $table->enum('status', ['pending','processing','paid','cancelled'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
