<?php

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Route;

echo "Profile Route Information:\n";
echo "========================\n";

// Get the profile.edit route
$route = Route::getRoutes()->getByName('profile.edit');

if ($route) {
    echo "Route: " . $route->uri() . "\n";
    echo "Methods: " . implode(', ', $route->methods()) . "\n";
    echo "Action: " . $route->getActionName() . "\n";
    echo "Middleware: " . implode(', ', $route->middleware()) . "\n";
    echo "\nRoute is properly configured!\n";
} else {
    echo "Profile route not found!\n";
}

// Also check what middleware is registered for 'role'
echo "\nRegistered Middleware:\n";
echo "===================\n";
$middleware = app('router')->getMiddleware();
foreach ($middleware as $name => $class) {
    echo "$name => $class\n";
}
