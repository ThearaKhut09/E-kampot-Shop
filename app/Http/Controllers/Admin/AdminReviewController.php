<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;

class AdminReviewController extends Controller
{
    public function index()
    {
        $reviews = Review::with(['user', 'product'])->orderBy('created_at', 'desc')->paginate(15);
        return view('admin.reviews.index', compact('reviews'));
    }

    public function show(Review $review)
    {
        $review->load(['user', 'product']);
        return view('admin.reviews.show', compact('review'));
    }

    public function destroy(Review $review)
    {
        $product = $review->product;
        $review->delete();

        // Update product average rating
        $product->updateAverageRating();

        return redirect()->route('admin.reviews.index')->with('success', 'Review deleted successfully.');
    }
}
