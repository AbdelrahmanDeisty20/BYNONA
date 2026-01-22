<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use Illuminate\Support\Facades\DB;

$products = Product::has('categories', '>', 1)->with('categories')->get();
$total = $products->count();
echo "Starting consolidation for {$total} products...\n";

$fixedCount = 0;
foreach ($products as $p) {
    $categoryIds = $p->categories->pluck('id')->unique()->toArray();

    // If more than one unique category, and 'Home' (ID 9) is one of them, remove 'Home'
    if (count($categoryIds) > 1 && in_array(9, $categoryIds)) {
        $categoryIds = array_values(array_filter($categoryIds, fn($id) => $id != 9));
    }

    // If still more than one category, just keep the first one
    if (count($categoryIds) > 1) {
        $categoryIds = [reset($categoryIds)];
    }

    // Sync to ensure exactly one category and no duplicates
    $p->categories()->sync($categoryIds);
    $fixedCount++;

    if ($fixedCount % 50 == 0) {
        echo "Fixed {$fixedCount}/{$total}...\n";
    }
}

echo "Consolidation complete. Fixed {$fixedCount} products.\n";
