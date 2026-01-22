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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar');
            $table->string('name_en');
            $table->text('desc_ar');
            $table->text('desc_en');
            $table->decimal('retail_price', 10, 2)->index();
            $table->decimal('wholesale_price', 10, 2)->index();
            $table->integer('min_quantity')->nullable();
            $table->decimal('disscount_price', 10, 2)->nullable();
            $table->integer('stock')->default(0);
            $table->string('main_image');
            $table->json('images');
            $table->timestamps();
            $table->index('created_at');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};