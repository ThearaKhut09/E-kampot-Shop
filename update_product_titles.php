<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Updating existing products with title field...\n";

// Let's create some example titles for existing products
$updates = [
    'iPhone 15 Pro' => 'iPhone 15 Pro Max 256GB',
    'Samsung Galaxy S24 Ultra' => 'Samsung Galaxy S24 Ultra 512GB',
    'MacBook Air M3' => 'MacBook Air M3 15-inch',
    'Dell XPS 13' => 'Dell XPS 13 9320 Laptop',
    'Classic Denim Jacket' => 'Premium Classic Denim Jacket'
];

foreach ($updates as $productName => $title) {
    $product = \App\Models\Product::where('name', $productName)->first();
    if ($product) {
        $product->title = $title;
        $product->save();
        echo "✓ Updated '{$productName}' with title '{$title}'\n";
    } else {
        echo "✗ Product '{$productName}' not found\n";
    }
}

echo "\nDone! You can now test the title functionality.\n";
