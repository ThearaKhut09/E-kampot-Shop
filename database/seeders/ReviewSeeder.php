<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some users with customer role
        $customers = User::role('customer')->take(10)->get();

        // Get some products
        $products = Product::active()->take(20)->get();

        if ($customers->isEmpty() || $products->isEmpty()) {
            $this->command->info('No customers or products found. Please run UserSeeder and ProductSeeder first.');
            return;
        }

        $reviews = [
            [
                'rating' => 5,
                'title' => 'Excellent product!',
                'comment' => 'This product exceeded my expectations. Great quality and fast delivery.',
                'is_approved' => true,
            ],
            [
                'rating' => 4,
                'title' => 'Good value for money',
                'comment' => 'Good product overall. Some minor issues but nothing major.',
                'is_approved' => true,
            ],
            [
                'rating' => 5,
                'title' => 'Highly recommend',
                'comment' => 'Amazing product! Will definitely buy again.',
                'is_approved' => true,
            ],
            [
                'rating' => 3,
                'title' => 'Average product',
                'comment' => 'It is okay, not great but not bad either.',
                'is_approved' => true,
            ],
            [
                'rating' => 4,
                'title' => 'Pretty good',
                'comment' => 'Nice product, good quality. Would recommend to others.',
                'is_approved' => true,
            ],
            [
                'rating' => 5,
                'title' => 'Perfect!',
                'comment' => 'Exactly what I was looking for. Perfect condition and fast shipping.',
                'is_approved' => true,
            ],
            [
                'rating' => 2,
                'title' => 'Could be better',
                'comment' => 'Not satisfied with the quality. Had some issues.',
                'is_approved' => false, // This one is pending approval
            ],
            [
                'rating' => 4,
                'title' => 'Good purchase',
                'comment' => 'Happy with my purchase. Good quality and reasonable price.',
                'is_approved' => true,
            ],
            [
                'rating' => 5,
                'title' => 'Love it!',
                'comment' => 'Absolutely love this product! Will order more.',
                'is_approved' => true,
            ],
            [
                'rating' => 3,
                'title' => 'Decent',
                'comment' => 'Decent product for the price. Nothing special but does the job.',
                'is_approved' => true,
            ],
        ];

        foreach ($products as $product) {
            // Add 1-3 reviews per product
            $numReviews = rand(1, 3);
            $usedCustomers = [];

            for ($i = 0; $i < $numReviews; $i++) {
                // Get a random customer that hasn't reviewed this product yet
                $availableCustomers = $customers->reject(function ($customer) use ($usedCustomers) {
                    return in_array($customer->id, $usedCustomers);
                });

                if ($availableCustomers->isEmpty()) {
                    break;
                }

                $customer = $availableCustomers->random();
                $usedCustomers[] = $customer->id;
                $reviewData = $reviews[array_rand($reviews)];

                Review::create([
                    'user_id' => $customer->id,
                    'product_id' => $product->id,
                    'rating' => $reviewData['rating'],
                    'title' => $reviewData['title'],
                    'comment' => $reviewData['comment'],
                    'is_approved' => $reviewData['is_approved'],
                    'created_at' => now()->subDays(rand(1, 30)),
                    'updated_at' => now()->subDays(rand(1, 30)),
                ]);
            }

            // Update product average rating
            $product->updateAverageRating();
        }

        $this->command->info('Reviews seeded successfully!');
    }
}
