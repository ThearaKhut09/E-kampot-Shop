<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class DemoUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create or get roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $customerRole = Role::firstOrCreate(['name' => 'customer']);

        // Create admin user
        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'first_name' => 'Admin',
                'last_name' => 'User',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'phone' => '+855123456789',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Assign admin role
        if (!$admin->hasRole('admin')) {
            $admin->assignRole($adminRole);
        }

        // Create customer user
        $customer = User::updateOrCreate(
            ['email' => 'customer@example.com'],
            [
                'name' => 'Customer User',
                'first_name' => 'Customer',
                'last_name' => 'User',
                'email' => 'customer@example.com',
                'password' => Hash::make('password'),
                'phone' => '+855987654321',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Assign customer role
        if (!$customer->hasRole('customer')) {
            $customer->assignRole($customerRole);
        }

        echo "Demo users created successfully:\n";
        echo "Admin: admin@example.com / password\n";
        echo "Customer: customer@example.com / password\n";
    }
}
