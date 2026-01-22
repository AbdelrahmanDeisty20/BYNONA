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
        if (Schema::hasColumn('properties', 'key_en')) {
            Schema::table('properties', function (Blueprint $table) {
                $table->dropColumn('key_en');
            });
        }

        Schema::table('properties', function (Blueprint $table) {
            if (!Schema::hasColumn('properties', 'product_id')) {
                $table->foreignId('product_id')->after('id')->constrained('products')->cascadeOnDelete();
            }
            if (!Schema::hasColumn('properties', 'main_image')) {
                $table->string('main_image')->nullable()->after('product_id');
            }
            if (!Schema::hasColumn('properties', 'images')) {
                $table->json('images')->nullable()->after('main_image');
            }
            if (!Schema::hasColumn('properties', 'retail_price')) {
                $table->decimal('retail_price', 10, 2)->nullable()->after('images');
            }
            if (!Schema::hasColumn('properties', 'wholesale_price')) {
                $table->decimal('wholesale_price', 10, 2)->nullable()->after('retail_price');
            }
            if (!Schema::hasColumn('properties', 'min_quantity')) {
                $table->integer('min_quantity')->default(1)->after('wholesale_price');
            }
            if (!Schema::hasColumn('properties', 'stock')) {
                $table->integer('stock')->default(0)->after('min_quantity');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            //
        });
    }
};
