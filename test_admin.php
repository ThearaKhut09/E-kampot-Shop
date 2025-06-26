<?php
// Test script to verify admin panel functionality
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

use App\Models\User;
use Spatie\Permission\Models\Role;

echo "=== E-Kampot Shop Admin Panel Health Check ===\n\n";

// Check if admin role exists
echo "1. Checking if admin role exists...\n";
$adminRole = Role::where('name', 'admin')->first();
if ($adminRole) {
    echo "✓ Admin role exists\n";
} else {
    echo "✗ Admin role missing\n";
}

// Check if admin user exists
echo "\n2. Checking if admin user exists...\n";
$adminUser = User::where('email', 'admin@ekampot.com')->first();
if ($adminUser) {
    echo "✓ Admin user exists: {$adminUser->email}\n";
    if ($adminUser->hasRole('admin')) {
        echo "✓ Admin user has admin role\n";
    } else {
        echo "✗ Admin user missing admin role\n";
    }
} else {
    echo "✗ Admin user missing\n";
}

// Check admin routes
echo "\n3. Admin routes check...\n";
$adminRoutes = [
    'admin.dashboard',
    'admin.products.index',
    'admin.categories.index',
    'admin.orders.index',
    'admin.users.index',
    'admin.settings.index',
    'admin.system.index'
];

foreach ($adminRoutes as $route) {
    try {
        $url = route($route);
        echo "✓ Route '{$route}' exists: {$url}\n";
    } catch (Exception $e) {
        echo "✗ Route '{$route}' missing\n";
    }
}

echo "\n=== Health Check Complete ===\n";
