<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ReviewController extends Controller
{
    /**
     * Store a newly created review.
     */
    public function store(Request $request, Product $product)
    {
        try {
            $request->validate([
                'rating' => 'required|integer|min:1|max:5',
                'title' => 'nullable|string|max:255',
                'comment' => 'nullable|string|max:1000',
            ]);

            // Check if user already reviewed this product
            $existingReview = Review::where('user_id', Auth::id())
                                   ->where('product_id', $product->id)
                                   ->first();

            if ($existingReview) {
                if ($request->ajax() || $request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You have already reviewed this product.'
                    ], 422);
                }
                return redirect()->back()->with('error', 'You have already reviewed this product.');
            }

            // Create the review
            $review = Review::create([
                'user_id' => Auth::id(),
                'product_id' => $product->id,
                'rating' => $request->rating,
                'title' => $request->title,
                'comment' => $request->comment,
                'is_approved' => false, // Reviews need admin approval
            ]);

            // Update product average rating
            $product->updateAverageRating();

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Review submitted successfully! It will be visible after admin approval.',
                    'review' => $review->load('user')
                ]);
            }

            return redirect()->back()->with('success', 'Review submitted successfully! It will be visible after admin approval.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $e->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Review submission error: ' . $e->getMessage());

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while submitting your review. Please try again.'
                ], 500);
            }
            return redirect()->back()->with('error', 'An error occurred while submitting your review. Please try again.');
        }
    }

    /**
     * Update the specified review.
     */
    public function update(Request $request, Review $review)
    {
        // Check if user owns this review
        if ($review->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'You can only edit your own reviews.'
            ], 403);
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'comment' => 'nullable|string|max:1000',
        ]);

        $review->update([
            'rating' => $request->rating,
            'title' => $request->title,
            'comment' => $request->comment,
            'is_approved' => false, // Re-approval needed after edit
        ]);

        // Update product average rating
        $review->product->updateAverageRating();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Review updated successfully! It will be visible after admin approval.',
                'review' => $review->load('user')
            ]);
        }

        return redirect()->back()->with('success', 'Review updated successfully! It will be visible after admin approval.');
    }

    /**
     * Remove the specified review.
     */
    public function destroy(Review $review)
    {
        // Check if user owns this review
        if ($review->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'You can only delete your own reviews.'
            ], 403);
        }

        $product = $review->product;
        $review->delete();

        // Update product average rating
        $product->updateAverageRating();

        return response()->json([
            'success' => true,
            'message' => 'Review deleted successfully.'
        ]);
    }
}
