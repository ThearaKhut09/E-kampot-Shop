<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Setting;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Product::with('categories')->active();

            // Check if inStock method exists to avoid errors
            if (method_exists(Product::class, 'scopeInStock')) {
                $query->inStock();
            }

            // Search functionality
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('description', 'LIKE', "%{$search}%")
                      ->orWhere('short_description', 'LIKE', "%{$search}%");
                });
            }

            // Category filter
            if ($request->has('category') && $request->category) {
                $category = Category::where('slug', $request->category)->first();
                if ($category) {
                    $query->whereHas('categories', function($q) use ($category) {
                        $q->where('categories.id', $category->id);
                    });
                }
            }

            // Price filter
            if ($request->has('min_price') && $request->min_price) {
                $query->where('price', '>=', $request->min_price);
            }
            if ($request->has('max_price') && $request->max_price) {
                $query->where('price', '<=', $request->max_price);
            }

            // Rating filter
            if ($request->has('min_rating') && $request->min_rating) {
                $query->where('average_rating', '>=', $request->min_rating);
            }

            // Sorting
            $sort = $request->get('sort', 'latest');
            switch ($sort) {
                case 'price_low':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_high':
                    $query->orderBy('price', 'desc');
                    break;
                case 'rating':
                    $query->orderBy('average_rating', 'desc');
                    break;
                case 'name':
                    $query->orderBy('name', 'asc');
                    break;
                default:
                    $query->latest();
            }

            // Get per page setting or default
            $perPage = 12;
            try {
                $perPage = Setting::get('products_per_page', 12);
            } catch (\Exception $e) {
                // Use default if Settings model fails
            }

            $products = $query->paginate($perPage);

            // Get categories safely
            $categories = collect([]);
            try {
                $categories = Category::active()->parents()->orderBy('sort_order')->get();
            } catch (\Exception $e) {
                // Use empty collection if Categories model fails
            }

            return view('products.index', compact('products', 'categories'));

        } catch (\Exception $e) {
            // Return error view or redirect with error message
            return view('products.index', [
                'products' => collect([]),
                'categories' => collect([]),
                'error' => 'Unable to load products: ' . $e->getMessage()
            ]);
        }
    }

    public function show($slug)
    {
        $product = Product::with(['categories', 'reviews' => function($query) {
                $query->approved()->with('user');
            }])
            ->where('slug', $slug)
            ->active()
            ->firstOrFail();

        $relatedProducts = Product::with('categories')
            ->active()
            ->inStock()
            ->whereHas('categories', function($query) use ($product) {
                $query->whereIn('categories.id', $product->categories->pluck('id'));
            })
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();

        return view('products.show', compact('product', 'relatedProducts'));
    }

    public function category($slug)
    {
        $category = Category::with('children')
            ->where('slug', $slug)
            ->active()
            ->firstOrFail();

        $query = Product::with('categories')
            ->active()
            ->inStock()
            ->whereHas('categories', function($q) use ($category) {
                $q->where('categories.id', $category->id);
            });

        $products = $query->paginate(Setting::get('products_per_page', 12));

        return view('products.category', compact('category', 'products'));
    }
}
