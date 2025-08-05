<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Creating a test product with title...\n";

// Get first category for testing
$category = \App\Models\Category::first();
if (!$category) {
    echo "No categories found. Creating a test category...\n";
    $category = \App\Models\Category::create([
        'name' => 'Test Category',
        'slug' => 'test-category',
        'description' => 'Test category for products',
        'status' => 'active',
        'sort_order' => 1
    ]);
}

// Create a test product with title
$product = new \App\Models\Product();
$product->name = 'Test Product ' . date('His');
$product->title = 'Amazing Test Product - Premium Edition ' . date('His');
$product->description = 'This is a test product created to verify the title functionality works correctly on the frontend.';
$product->price = 99.99;
$product->stock_quantity = 10;
$product->status = 'active';
$product->featured = true; // Make it featured so it shows on home page
$product->save();

// Attach to category
$product->categories()->attach($category->id);

echo "✓ Created test product:\n";
echo "  Name: {$product->name}\n";
echo "  Title: {$product->title}\n";
echo "  ID: {$product->id}\n";
echo "  Featured: Yes\n";
echo "\nNow visit the home page and products page to see the title in action!\n";
