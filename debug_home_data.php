<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing home page data exactly as HomeController loads it...\n\n";

// Replicate the exact query from HomeController
$featuredProducts = \App\Models\Product::with('categories')
    ->active()
    ->featured()
    ->inStock()
    ->take(8)
    ->get();

echo "FEATURED PRODUCTS (as loaded by HomeController):\n";
echo "===============================================\n";
foreach ($featuredProducts as $product) {
    echo "Name: {$product->name}\n";
    echo "Title: " . ($product->title ?: 'NULL') . "\n";
    echo "Display: " . ($product->title ?: $product->name) . "\n";
    echo "Featured: " . ($product->featured ? 'Yes' : 'No') . "\n";
    echo "Active: " . ($product->status === 'active' ? 'Yes' : 'No') . "\n";
    echo "In Stock: " . ($product->in_stock ? 'Yes' : 'No') . "\n";
    echo "---\n";
}

echo "\nNEW PRODUCTS (as loaded by HomeController):\n";
echo "==========================================\n";
$newProducts = \App\Models\Product::with('categories')
    ->active()
    ->inStock()
    ->latest()
    ->take(8)
    ->get();

foreach ($newProducts as $product) {
    echo "Name: {$product->name}\n";
    echo "Title: " . ($product->title ?: 'NULL') . "\n";
    echo "Display: " . ($product->title ?: $product->name) . "\n";
    echo "---\n";
}
