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
        Schema::create('order_item', function (Blueprint $table) {
            $table->id();
            // $table->foreignId('cart_id')->constrained("carts")->cascadeOnDelete();
            $table->foreignId('order_id')->constrained("orders")->cascadeOnDelete();
            $table->foreignId('product_id')->constrained("products")->cascadeOnDelete();
            $table->integer('quantity');
            $table->decimal('price', 10, 2); // السعر النهائي بعد العروض
            $table->enum('sale_type', ['retail','wholesale'])->default('retail');
            $table->timestamps();
            $table->unique([ 'product_id','order_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_item');
    }
};