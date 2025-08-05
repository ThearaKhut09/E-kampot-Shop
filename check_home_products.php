<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Checking current products on home page...\n\n";

echo "FEATURED PRODUCTS:\n";
echo "==================\n";
$featuredProducts = \App\Models\Product::active()->where('featured', true)->take(8)->get();
foreach ($featuredProducts as $product) {
    echo "ID: {$product->id}\n";
    echo "Name: {$product->name}\n";
    echo "Title: " . ($product->title ?: 'NULL') . "\n";
    echo "Featured: " . ($product->featured ? 'Yes' : 'No') . "\n";
    echo "Status: {$product->status}\n";
    echo "---\n";
}

echo "\nNEW PRODUCTS (Latest):\n";
echo "======================\n";
$newProducts = \App\Models\Product::active()->latest()->take(8)->get();
foreach ($newProducts as $product) {
    echo "ID: {$product->id}\n";
    echo "Name: {$product->name}\n";
    echo "Title: " . ($product->title ?: 'NULL') . "\n";
    echo "Featured: " . ($product->featured ? 'Yes' : 'No') . "\n";
    echo "Created: {$product->created_at}\n";
    echo "---\n";
}
