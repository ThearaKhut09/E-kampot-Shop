<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Phones',
                'description' => 'Smartphones, feature phones, and mobile accessories',
                'is_active' => true,
                'sort_order' => 1,
                'children' => [
                    ['name' => 'Smartphones', 'description' => 'Latest smartphones from top brands'],
                    ['name' => 'Feature Phones', 'description' => 'Basic phones and feature phones'],
                    ['name' => 'Phone Accessories', 'description' => 'Cases, chargers, and phone accessories'],
                ]
            ],
            [
                'name' => 'Electronics',
                'description' => 'Consumer electronics and gadgets',
                'is_active' => true,
                'sort_order' => 2,
                'children' => [
                    ['name' => 'Laptops', 'description' => 'Laptops for work and gaming'],
                    ['name' => 'Desktops', 'description' => 'Desktop computers and workstations'],
                    ['name' => 'Tablets', 'description' => 'Tablets and e-readers'],
                    ['name' => 'Smartwatches', 'description' => 'Smart watches and fitness trackers'],
                    ['name' => 'Headphones', 'description' => 'Headphones and earbuds'],
                    ['name' => 'Speakers', 'description' => 'Bluetooth and wireless speakers'],
                ]
            ],
            [
                'name' => 'Fashion',
                'description' => 'Clothing and fashion accessories',
                'is_active' => true,
                'sort_order' => 3,
                'children' => [
                    ['name' => 'Men\'s Clothing', 'description' => 'Clothing for men'],
                    ['name' => 'Women\'s Clothing', 'description' => 'Clothing for women'],
                    ['name' => 'Kids\' Clothing', 'description' => 'Clothing for children'],
                    ['name' => 'Shoes', 'description' => 'Footwear for all ages'],
                    ['name' => 'Bags', 'description' => 'Handbags, backpacks, and luggage'],
                    ['name' => 'Jewelry', 'description' => 'Jewelry and fashion accessories'],
                ]
            ],
            [
                'name' => 'Home & Kitchen',
                'description' => 'Home appliances and kitchen essentials',
                'is_active' => true,
                'sort_order' => 4,
                'children' => [
                    ['name' => 'Kitchen Appliances', 'description' => 'Microwaves, blenders, and kitchen gadgets'],
                    ['name' => 'Cookware', 'description' => 'Pots, pans, and cooking utensils'],
                    ['name' => 'Home Decor', 'description' => 'Decorative items and artwork'],
                    ['name' => 'Furniture', 'description' => 'Home and office furniture'],
                ]
            ],
            [
                'name' => 'Beauty & Personal Care',
                'description' => 'Beauty and personal care products',
                'is_active' => true,
                'sort_order' => 5,
                'children' => [
                    ['name' => 'Skincare', 'description' => 'Skincare products and treatments'],
                    ['name' => 'Makeup', 'description' => 'Cosmetics and makeup products'],
                    ['name' => 'Haircare', 'description' => 'Hair care and styling products'],
                    ['name' => 'Grooming', 'description' => 'Personal grooming products'],
                ]
            ],
            [
                'name' => 'Sports & Outdoors',
                'description' => 'Sports equipment and outdoor gear',
                'is_active' => true,
                'sort_order' => 6,
                'children' => [
                    ['name' => 'Fitness Equipment', 'description' => 'Gym and fitness equipment'],
                    ['name' => 'Camping Gear', 'description' => 'Camping and hiking equipment'],
                    ['name' => 'Sports Apparel', 'description' => 'Athletic wear and sports clothing'],
                    ['name' => 'Bicycles', 'description' => 'Bikes and cycling accessories'],
                ]
            ],
            [
                'name' => 'Books & Stationery',
                'description' => 'Books and office supplies',
                'is_active' => true,
                'sort_order' => 7,
                'children' => [
                    ['name' => 'Books', 'description' => 'Fiction, non-fiction, and educational books'],
                    ['name' => 'Notebooks', 'description' => 'Notebooks and journals'],
                    ['name' => 'Pens & Pencils', 'description' => 'Writing instruments'],
                    ['name' => 'Office Supplies', 'description' => 'Office and school supplies'],
                ]
            ],
            [
                'name' => 'Toys & Games',
                'description' => 'Toys and entertainment',
                'is_active' => true,
                'sort_order' => 8,
                'children' => [
                    ['name' => 'Toys', 'description' => 'Toys for all ages'],
                    ['name' => 'Board Games', 'description' => 'Board games and puzzles'],
                    ['name' => 'Gaming Consoles', 'description' => 'Video game consoles and accessories'],
                ]
            ],
            [
                'name' => 'Health & Wellness',
                'description' => 'Health and wellness products',
                'is_active' => true,
                'sort_order' => 9,
                'children' => [
                    ['name' => 'Supplements', 'description' => 'Vitamins and dietary supplements'],
                    ['name' => 'Fitness Trackers', 'description' => 'Health monitoring devices'],
                    ['name' => 'Medical Devices', 'description' => 'Medical and health care devices'],
                ]
            ],
            [
                'name' => 'Automotive',
                'description' => 'Car accessories and tools',
                'is_active' => true,
                'sort_order' => 10,
                'children' => [
                    ['name' => 'Car Accessories', 'description' => 'Interior and exterior car accessories'],
                    ['name' => 'Tools', 'description' => 'Automotive tools and equipment'],
                    ['name' => 'Maintenance', 'description' => 'Car care and maintenance products'],
                ]
            ],
        ];

        foreach ($categories as $categoryData) {
            $children = $categoryData['children'] ?? [];
            unset($categoryData['children']);

            $category = Category::create($categoryData);

            foreach ($children as $childData) {
                $childData['parent_id'] = $category->id;
                Category::create($childData);
            }
        }
    }
}
