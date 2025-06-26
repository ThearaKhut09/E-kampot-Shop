<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $customerRole = Role::firstOrCreate(['name' => 'customer']);

        // Create permissions
        $permissions = [
            // Product management
            'manage products',
            'create products',
            'edit products',
            'delete products',
            'view products',

            // Category management
            'manage categories',
            'create categories',
            'edit categories',
            'delete categories',
            'view categories',

            // Order management
            'manage orders',
            'view orders',
            'update order status',

            // User management
            'manage users',
            'view users',
            'edit users',
            'delete users',

            // Review management
            'manage reviews',
            'approve reviews',
            'delete reviews',

            // Settings
            'manage settings',

            // Customer permissions
            'place orders',
            'view own orders',
            'write reviews',
            'manage own profile',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign all permissions to admin
        $adminRole->givePermissionTo(Permission::all());

        // Assign customer permissions
        $customerRole->givePermissionTo([
            'place orders',
            'view own orders',
            'write reviews',
            'manage own profile',
            'view products',
            'view categories',
        ]);

        // Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@ekampot.com',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $admin->assignRole('admin');

        // Create test customer
        $customer = User::create([
            'name' => 'Customer User',
            'email' => 'customer@example.com',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $customer->assignRole('customer');
    }
}
