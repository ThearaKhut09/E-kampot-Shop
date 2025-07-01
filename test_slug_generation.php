<?php

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Product;

echo "Testing unique slug generation:\n";
echo "===============================\n";

// Test the generateUniqueSlug method
$product = new Product();

$testNames = ['iPhone 10', 'iPhone 10', 'iPhone 10', 'Samsung Galaxy'];

foreach ($testNames as $index => $name) {
    $slug = $product->generateUniqueSlug($name);
    echo "Test " . ($index + 1) . ": '$name' => '$slug'\n";

    // Create a dummy product with this slug to test uniqueness
    Product::create([
        'name' => $name . ' Test ' . ($index + 1),
        'slug' => $slug,
        'description' => 'Test product',
        'price' => 100,
        'stock_quantity' => 10,
        'sku' => 'TEST-' . ($index + 1),
        'status' => 'active'
    ]);
}

echo "\nCreated test products successfully!\n";
echo "Checking database for created slugs:\n";

$products = Product::where('sku', 'LIKE', 'TEST-%')->orderBy('id')->get();
foreach ($products as $product) {
    echo "Name: {$product->name}, Slug: {$product->slug}\n";
}

// Clean up test products
Product::where('sku', 'LIKE', 'TEST-%')->delete();
echo "\nTest products cleaned up.\n";
