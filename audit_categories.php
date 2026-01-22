<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;

$products = Product::has('categories', '>', 1)->with('categories')->get();

echo 'Total products with multiple categories: ' . $products->count() . "\n";

foreach ($products->take(50) as $p) {
    echo "Product ID {$p->id}: " . $p->categories->map(fn($c) => "[{$c->id}] {$c->name_en}")->implode(', ') . "\n";
}
