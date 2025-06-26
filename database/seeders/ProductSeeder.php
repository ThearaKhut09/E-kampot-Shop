<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            // Phones
            [
                'name' => 'iPhone 15 Pro',
                'description' => 'The latest iPhone with advanced features and premium build quality.',
                'short_description' => 'Latest iPhone with Pro features',
                'price' => 999.00,
                'sale_price' => 899.00,
                'stock_quantity' => 25,
                'is_featured' => true,
                'status' => 'active',
                'images' => ['https://via.placeholder.com/600x600/1a1a1a/ffffff?text=iPhone+15+Pro'],
                'attributes' => ['color' => 'Titanium Blue', 'storage' => '256GB'],
                'categories' => ['Phones', 'Smartphones']
            ],
            [
                'name' => 'Samsung Galaxy S24 Ultra',
                'description' => 'Flagship Android phone with S Pen and advanced camera system.',
                'short_description' => 'Premium Android flagship with S Pen',
                'price' => 1199.00,
                'stock_quantity' => 20,
                'is_featured' => true,
                'status' => 'active',
                'images' => ['https://via.placeholder.com/600x600/2c2c2c/ffffff?text=Galaxy+S24+Ultra'],
                'attributes' => ['color' => 'Titanium Gray', 'storage' => '512GB'],
                'categories' => ['Phones', 'Smartphones']
            ],

            // Electronics - Laptops
            [
                'name' => 'MacBook Air M3',
                'description' => 'Ultra-thin and powerful laptop with M3 chip for incredible performance.',
                'short_description' => 'Lightweight laptop with M3 chip',
                'price' => 1299.00,
                'stock_quantity' => 15,
                'is_featured' => true,
                'status' => 'active',
                'images' => ['https://via.placeholder.com/600x600/e6e6e6/333333?text=MacBook+Air+M3'],
                'attributes' => ['color' => 'Silver', 'ram' => '16GB', 'storage' => '512GB SSD'],
                'categories' => ['Electronics', 'Laptops']
            ],
            [
                'name' => 'Dell XPS 13',
                'description' => 'Premium Windows laptop with InfinityEdge display and excellent build quality.',
                'short_description' => 'Premium Windows ultrabook',
                'price' => 1099.00,
                'sale_price' => 999.00,
                'stock_quantity' => 12,
                'status' => 'active',
                'images' => ['https://via.placeholder.com/600x600/2d3748/ffffff?text=Dell+XPS+13'],
                'attributes' => ['color' => 'Platinum Silver', 'ram' => '16GB', 'storage' => '512GB SSD'],
                'categories' => ['Electronics', 'Laptops']
            ],

            // Fashion
            [
                'name' => 'Classic Denim Jacket',
                'description' => 'Timeless denim jacket perfect for casual wear. Made from premium denim.',
                'short_description' => 'Classic blue denim jacket',
                'price' => 79.99,
                'stock_quantity' => 50,
                'status' => 'active',
                'images' => ['https://via.placeholder.com/600x600/4a90e2/ffffff?text=Denim+Jacket'],
                'attributes' => ['size' => 'M', 'color' => 'Blue', 'material' => 'Denim'],
                'categories' => ['Fashion', 'Men\'s Clothing']
            ],
            [
                'name' => 'Running Sneakers',
                'description' => 'Comfortable running shoes with advanced cushioning technology.',
                'short_description' => 'High-performance running shoes',
                'price' => 129.99,
                'sale_price' => 99.99,
                'stock_quantity' => 30,
                'is_featured' => true,
                'status' => 'active',
                'images' => ['https://via.placeholder.com/600x600/ff6b6b/ffffff?text=Running+Sneakers'],
                'attributes' => ['size' => '10', 'color' => 'Black/Red', 'brand' => 'SportsPro'],
                'categories' => ['Fashion', 'Shoes']
            ],

            // Home & Kitchen
            [
                'name' => 'Smart Coffee Maker',
                'description' => 'WiFi-enabled coffee maker with app control and programmable settings.',
                'short_description' => 'App-controlled smart coffee maker',
                'price' => 199.99,
                'stock_quantity' => 18,
                'status' => 'active',
                'images' => ['https://via.placeholder.com/600x600/8b4513/ffffff?text=Smart+Coffee+Maker'],
                'attributes' => ['capacity' => '12 cups', 'features' => 'WiFi, Timer, Auto-brew'],
                'categories' => ['Home & Kitchen', 'Kitchen Appliances']
            ],

            // Beauty & Personal Care
            [
                'name' => 'Luxury Skincare Set',
                'description' => 'Complete skincare routine with premium ingredients for all skin types.',
                'short_description' => '5-piece luxury skincare collection',
                'price' => 149.99,
                'sale_price' => 119.99,
                'stock_quantity' => 25,
                'is_featured' => true,
                'status' => 'active',
                'images' => ['https://via.placeholder.com/600x600/ff69b4/ffffff?text=Skincare+Set'],
                'attributes' => ['skin_type' => 'All types', 'pieces' => '5', 'brand' => 'LuxBeauty'],
                'categories' => ['Beauty & Personal Care', 'Skincare']
            ],

            // Sports & Outdoors
            [
                'name' => 'Yoga Mat Premium',
                'description' => 'High-quality yoga mat with excellent grip and cushioning.',
                'short_description' => 'Premium non-slip yoga mat',
                'price' => 49.99,
                'stock_quantity' => 40,
                'status' => 'active',
                'images' => ['https://via.placeholder.com/600x600/9370db/ffffff?text=Yoga+Mat'],
                'attributes' => ['thickness' => '6mm', 'material' => 'TPE', 'color' => 'Purple'],
                'categories' => ['Sports & Outdoors', 'Fitness Equipment']
            ],

            // Books & Stationery
            [
                'name' => 'Premium Notebook Set',
                'description' => 'Set of 3 premium notebooks with dotted pages, perfect for journaling.',
                'short_description' => '3-pack dotted notebooks',
                'price' => 24.99,
                'stock_quantity' => 60,
                'status' => 'active',
                'images' => ['https://via.placeholder.com/600x600/4ecdc4/ffffff?text=Notebook+Set'],
                'attributes' => ['pages' => '200', 'size' => 'A5', 'cover' => 'Hardcover'],
                'categories' => ['Books & Stationery', 'Notebooks']
            ],
        ];

        foreach ($products as $productData) {
            $categories = $productData['categories'];
            unset($productData['categories']);

            $product = Product::create($productData);

            // Attach categories
            foreach ($categories as $categoryName) {
                $category = Category::where('name', $categoryName)->first();
                if ($category) {
                    $product->categories()->attach($category->id);
                }
            }
        }
    }
}
