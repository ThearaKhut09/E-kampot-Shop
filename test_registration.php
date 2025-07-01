<?php
// Test script to verify user registration with role assignment

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Spatie\Permission\Models\Role;
use App\Models\User;

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing user registration role assignment...\n\n";

// Check if roles exist
echo "Checking roles:\n";
$roles = Role::all();
foreach ($roles as $role) {
    echo "- {$role->name}\n";
}
echo "\n";

// Check if customer role exists
$customerRole = Role::where('name', 'customer')->first();
if ($customerRole) {
    echo "✓ Customer role exists\n";
} else {
    echo "✗ Customer role does not exist - creating it...\n";
    $customerRole = Role::create(['name' => 'customer']);
    echo "✓ Customer role created\n";
}

echo "\nTest completed!\n";
