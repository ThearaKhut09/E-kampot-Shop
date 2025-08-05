<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$products = \App\Models\Product::select('id', 'name', 'title', 'status')->take(5)->get();

echo "Existing Products:\n";
echo "==================\n";
foreach ($products as $product) {
    echo "ID: {$product->id}\n";
    echo "Name: {$product->name}\n";
    echo "Title: " . ($product->title ?? 'NULL') . "\n";
    echo "Status: {$product->status}\n";
    echo "---\n";
}
