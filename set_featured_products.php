<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Setting more products as featured for home page display...\n";

// Set the products with titles as featured
$productNames = [
    'iPhone 15 Pro',
    'Samsung Galaxy S24 Ultra',
    'MacBook Air M3',
    'Dell XPS 13'
];

foreach ($productNames as $name) {
    $product = \App\Models\Product::where('name', $name)->first();
    if ($product) {
        $product->featured = true;
        $product->save();
        echo "✓ Set '{$name}' as featured (Title: " . ($product->title ?: 'NULL') . ")\n";
    } else {
        echo "✗ Product '{$name}' not found\n";
    }
}

echo "\nNow checking featured products:\n";
echo "==============================\n";
$featured = \App\Models\Product::where('featured', true)->get();
foreach ($featured as $product) {
    echo "- {$product->name} → " . ($product->title ?: 'No title') . "\n";
}
