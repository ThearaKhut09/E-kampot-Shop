<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'key' => 'site_name',
                'value' => 'E-Kampot Shop',
                'type' => 'string',
                'description' => 'Name of the website'
            ],
            [
                'key' => 'site_description',
                'value' => 'Your one-stop shop for everything you need',
                'type' => 'string',
                'description' => 'Site description for SEO'
            ],
            [
                'key' => 'site_email',
                'value' => 'info@ekampot.com',
                'type' => 'string',
                'description' => 'Contact email address'
            ],
            [
                'key' => 'site_phone',
                'value' => '+1 (555) 123-4567',
                'type' => 'string',
                'description' => 'Contact phone number'
            ],
            [
                'key' => 'site_address',
                'value' => '123 Main Street, Kampot, Cambodia',
                'type' => 'string',
                'description' => 'Physical address'
            ],
            [
                'key' => 'default_currency',
                'value' => 'USD',
                'type' => 'string',
                'description' => 'Default currency code'
            ],
            [
                'key' => 'tax_rate',
                'value' => '10',
                'type' => 'integer',
                'description' => 'Tax rate percentage'
            ],
            [
                'key' => 'shipping_cost',
                'value' => '5.00',
                'type' => 'string',
                'description' => 'Default shipping cost'
            ],
            [
                'key' => 'free_shipping_threshold',
                'value' => '50.00',
                'type' => 'string',
                'description' => 'Minimum order amount for free shipping'
            ],
            [
                'key' => 'products_per_page',
                'value' => '12',
                'type' => 'integer',
                'description' => 'Number of products per page'
            ],
            [
                'key' => 'featured_products_count',
                'value' => '8',
                'type' => 'integer',
                'description' => 'Number of featured products to show on homepage'
            ],
            [
                'key' => 'allow_reviews',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Allow customers to write reviews'
            ],
            [
                'key' => 'auto_approve_reviews',
                'value' => 'false',
                'type' => 'boolean',
                'description' => 'Automatically approve reviews'
            ],
            [
                'key' => 'maintenance_mode',
                'value' => 'false',
                'type' => 'boolean',
                'description' => 'Enable maintenance mode'
            ],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }
    }
}
