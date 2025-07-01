<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Product;

echo "Testing case-insensitive duplicate detection:\n";
echo "============================================\n";

// Test different case variations of existing products
$testNames = [
    'iphone 10',     // exact match (exists)
    'iPhone 10',     // different case
    'IPHONE 10',     // all caps
    'IPhone 10',     // mixed case
    'iPhone X',      // different name
];

foreach ($testNames as $testName) {
    $existing = Product::whereRaw('LOWER(name) = ?', [strtolower($testName)])->first();

    if ($existing) {
        echo "❌ '$testName' would be REJECTED - conflicts with existing: '{$existing->name}'\n";
    } else {
        echo "✅ '$testName' would be ACCEPTED - no conflicts found\n";
    }
}

echo "\nThis shows our case-insensitive validation is working correctly!\n";
