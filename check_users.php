<?php

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

echo "Users and their roles:\n";
echo "=====================\n";

$users = User::with('roles')->get();

foreach ($users as $user) {
    $roles = $user->roles->pluck('name')->join(', ');
    echo "ID: {$user->id}\n";
    echo "Name: {$user->name}\n";
    echo "Email: {$user->email}\n";
    echo "Roles: {$roles}\n";
    echo "Active: " . ($user->is_active ? 'Yes' : 'No') . "\n";
    echo "-------------------\n";
}
