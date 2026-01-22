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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string("title_ar");
            $table->string("title_en");
            $table->text("message_ar");
            $table->text("message_en");
            $table->foreignId("user_id")->constrained("users")->cascadeOnDelete();
            $table->foreignId("order_id")->constrained("orders")->cascadeOnDelete();
            $table->foreignId("offer_id")->constrained("offers")->cascadeOnDelete();
            $table->foreignId("Product_id")->constrained("products")->cascadeOnDelete();
            $table->foreignId("payment_id")->constrained("payments")->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};