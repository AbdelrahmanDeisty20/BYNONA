<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

try {
    Schema::table('offers', function (Blueprint $table) {
        $table->foreignId('property_id')->nullable()->after('id')->constrained('properties')->cascadeOnDelete();
    });
    echo "Added property_id!\n";
} catch (\Exception $e) {
    echo "Error adding property_id: " . $e->getMessage() . "\n";
}
