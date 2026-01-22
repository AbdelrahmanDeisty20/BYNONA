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
        Schema::create('orders_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained("orders")->cascadeOnDelete();
            $table->foreignId('property_id')->constrained("properties")->cascadeOnDelete();
            $table->integer('quantity');
            $table->decimal('price', 10, 2); // السعر النهائي بعد العروض
            $table->enum('sale_type', ['retail','wholesale'])->default('retail');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders_items');
    }
};
