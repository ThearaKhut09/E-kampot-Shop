<?php

// Test script to verify the product_options fix
require_once 'vendor/autoload.php';

use App\Models\OrderItem;

// Create a test order item with array product_options
$orderItem = new OrderItem([
    'product_options' => ['Color: Red', 'Size: Large', 'Material: Cotton']
]);

// Test the formatted accessor
echo "Array product_options: " . json_encode($orderItem->product_options) . "\n";
echo "Formatted product_options: " . $orderItem->formatted_product_options . "\n";

// Test with string product_options
$orderItem2 = new OrderItem([
    'product_options' => 'Color: Blue, Size: Medium'
]);

echo "String product_options: " . $orderItem2->product_options . "\n";
echo "Formatted product_options: " . $orderItem2->formatted_product_options . "\n";

// Test with null product_options
$orderItem3 = new OrderItem([
    'product_options' => null
]);

echo "Null product_options: " . var_export($orderItem3->product_options, true) . "\n";
echo "Formatted product_options: " . var_export($orderItem3->formatted_product_options, true) . "\n";
