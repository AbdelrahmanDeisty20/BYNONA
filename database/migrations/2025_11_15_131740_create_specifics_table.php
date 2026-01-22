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
        Schema::create('specifics', function (Blueprint $table) {
            $table->id();
            
            $table->string('key_ar');
            $table->string('key_en');
            $table->text('value_ar');
            $table->text('value_en');
            $table->foreignId("product_id")->constrained("products")->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('specifics');
    }
};