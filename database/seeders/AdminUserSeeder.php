<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles if they don't exist
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $customerRole = Role::firstOrCreate(['name' => 'customer']);

        // Create admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@ekampot.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $admin->assignRole('admin');

        // Create a customer user for testing
        $customer = User::firstOrCreate(
            ['email' => 'customer@ekampot.com'],
            [
                'name' => 'Test Customer',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $customer->assignRole('customer');

        $this->command->info('Admin user created: admin@ekampot.com / password');
        $this->command->info('Customer user created: customer@ekampot.com / password');
    }
}
