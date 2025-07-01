<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Product;

echo "Existing Products:\n";
echo "==================\n";

$products = Product::select('id', 'name', 'slug')->get();

foreach ($products as $product) {
    echo "ID: {$product->id} | Name: {$product->name} | Slug: {$product->slug}\n";
}

echo "\nTotal products: " . $products->count() . "\n";
