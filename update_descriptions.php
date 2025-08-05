<?php

require 'vendor/autoload.php';
require 'bootstrap/app.php';

use App\Models\Product;

echo "Updating product descriptions...\n";

// Sample descriptions to add to products
$descriptions = [
    'A high-quality product with excellent features and great value for money.',
    'Premium quality item designed for everyday use with modern styling.',
    'Durable and reliable product that meets all your needs and expectations.',
    'Perfect blend of style, functionality, and affordability.',
    'Top-rated product with outstanding performance and customer satisfaction.',
    'Innovative design with premium materials for long-lasting use.',
    'Essential item for modern lifestyle with superior quality and comfort.',
    'Carefully crafted product with attention to detail and user experience.',
    'Professional-grade quality suitable for both personal and business use.',
    'Sleek and modern design with advanced features and reliable performance.',
];

// Get products that don't have short descriptions
$products = Product::where(function($query) {
    $query->whereNull('short_description')
          ->orWhere('short_description', '');
})->get();

echo "Found " . $products->count() . " products without descriptions.\n";

// Update products with descriptions
foreach ($products as $index => $product) {
    $descriptionIndex = $index % count($descriptions);
    $product->update([
        'short_description' => $descriptions[$descriptionIndex]
    ]);
    echo "Updated: {$product->name}\n";
}

echo "All products now have descriptions!\n";
