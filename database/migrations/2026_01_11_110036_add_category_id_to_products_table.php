<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Step 1: Add category_id column
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->nullable()->after('brand_id');
        });

        // Step 2: Migrate data from pivot table to products.category_id
        DB::table('products')->get()->each(function ($product) {
            $firstCategory = DB::table('category_product')
                ->where('product_id', $product->id)
                ->first();

            if ($firstCategory) {
                // Verify category exists before assigning
                $categoryExists = DB::table('categories')
                    ->where('id', $firstCategory->category_id)
                    ->exists();

                if ($categoryExists) {
                    DB::table('products')
                        ->where('id', $product->id)
                        ->update(['category_id' => $firstCategory->category_id]);
                }
            }
        });

        // Step 3: Clean up any invalid category_id values (safety check)
        DB::statement('
            UPDATE products 
            SET category_id = NULL 
            WHERE category_id IS NOT NULL 
            AND category_id NOT IN (SELECT id FROM categories)
        ');

        // Step 4: Add foreign key
        Schema::table('products', function (Blueprint $table) {
            $table
                ->foreign('category_id')
                ->references('id')
                ->on('categories')
                ->nullOnDelete();
        });

        // Step 4: Drop pivot table
        Schema::dropIfExists('category_product');
    }

    public function down(): void
    {
        // Recreate pivot table
        Schema::create('category_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->timestamps();
        });

        // Migrate data back
        DB::table('products')->whereNotNull('category_id')->get()->each(function ($product) {
            DB::table('category_product')->insert([
                'category_id' => $product->category_id,
                'product_id' => $product->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        // Drop category_id
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
    }
};
