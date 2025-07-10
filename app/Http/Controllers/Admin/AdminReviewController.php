<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Product;
use Illuminate\Http\Request;

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

    public function update(Request $request, Review $review)
    {
        $request->validate([
            'is_approved' => 'required|boolean',
        ]);

        $review->update(['is_approved' => $request->is_approved]);

        // Update product average rating
        $review->product->updateAverageRating();

        return redirect()->route('admin.reviews.index')->with('success', 'Review status updated successfully.');
    }

    public function destroy(Review $review)
    {
        $product = $review->product;
        $review->delete();

        // Update product average rating
        $product->updateAverageRating();

        return redirect()->route('admin.reviews.index')->with('success', 'Review deleted successfully.');
    }

    public function approve(Review $review)
    {
        $review->update(['is_approved' => true]);

        // Update product average rating
        $review->product->updateAverageRating();

        return redirect()->back()->with('success', 'Review approved successfully.');
    }

    public function reject(Review $review)
    {
        $review->update(['is_approved' => false]);

        // Update product average rating
        $review->product->updateAverageRating();

        return redirect()->back()->with('success', 'Review rejected successfully.');
    }
}
