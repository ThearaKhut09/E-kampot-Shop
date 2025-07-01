<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Product;
use Illuminate\Support\Facades\Validator;

echo "Testing product name validation:\n";
echo "===============================\n";

// Check existing product names
$existingProducts = Product::select('name')->get()->pluck('name')->toArray();
echo "Existing product names:\n";
foreach ($existingProducts as $name) {
    echo "- $name\n";
}

echo "\nTesting validation rules:\n";

// Test data
$testData = [
    'name' => 'iPhone 10', // This already exists
    'description' => 'Test description',
    'price' => 100,
    'stock_quantity' => 10,
    'sku' => 'TEST-UNIQUE-SKU',
    'status' => 'active',
    'categories' => [1]
];

// Validation rules (same as in controller)
$rules = [
    'name' => 'required|string|max:255|unique:products,name',
    'description' => 'required|string',
    'price' => 'required|numeric|min:0',
    'stock_quantity' => 'required|integer|min:0',
    'sku' => 'required|string|max:100|unique:products',
    'categories' => 'required|array|min:1',
    'status' => 'required|in:active,inactive',
];

$messages = [
    'name.unique' => 'A product with this name already exists. Please choose a different name or modify the existing product.',
    'sku.unique' => 'This SKU is already in use. Please enter a unique SKU.',
];

$validator = Validator::make($testData, $rules, $messages);

if ($validator->fails()) {
    echo "Validation failed (as expected):\n";
    foreach ($validator->errors()->all() as $error) {
        echo "- $error\n";
    }
} else {
    echo "Validation passed (unexpected)\n";
}

echo "\nTesting with unique name:\n";
$testData['name'] = 'Unique Product Name Test';
$validator = Validator::make($testData, $rules, $messages);

if ($validator->fails()) {
    echo "Validation failed:\n";
    foreach ($validator->errors()->all() as $error) {
        echo "- $error\n";
    }
} else {
    echo "Validation passed (as expected)\n";
}
