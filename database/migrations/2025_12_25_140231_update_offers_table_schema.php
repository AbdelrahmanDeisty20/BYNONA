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
        // 1. Drop old foreign key if it exists
        if (Schema::hasColumn('offers', 'product_id')) {
            Schema::table('offers', function (Blueprint $table) {
                // We use a raw DB statement to drop the foreign key to avoid errors if it doesn't exist
                // or if the name is different. But let's try the Laravel way first with a check.
                try {
                    $table->dropForeign(['product_id']);
                } catch (\Exception $e) {}
                $table->dropColumn('product_id');
            });
        }

        // 2. Add new columns
        Schema::table('offers', function (Blueprint $table) {
            if (!Schema::hasColumn('offers', 'property_id')) {
                $table->unsignedBigInteger('property_id')->nullable()->after('id');
            }
            if (!Schema::hasColumn('offers', 'discount_wholesale')) {
                $table->decimal('discount_wholesale', 8, 2)->nullable()->after('discount_retail');
            }
        });

        // 3. Add foreign key constraint
        Schema::table('offers', function (Blueprint $table) {
            $table->foreign('property_id')->references('id')->on('properties')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            //
        });
    }
};
