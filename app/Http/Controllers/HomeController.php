<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Setting;

class HomeController extends Controller
{
    public function index()
    {
        $featuredProducts = Product::with('categories')
            ->active()
            ->featured()
            ->inStock()
            ->take(Setting::get('featured_products_count', 8))
            ->get();

        $categories = Category::active()
            ->parents()
            ->orderBy('sort_order')
            ->take(8)
            ->get();

        $newProducts = Product::with('categories')
            ->active()
            ->inStock()
            ->latest()
            ->take(8)
            ->get();

        $onSaleProducts = Product::with('categories')
            ->active()
            ->inStock()
            ->whereNotNull('sale_price')
            ->take(8)
            ->get();

        return view('home', compact(
            'featuredProducts',
            'categories',
            'newProducts',
            'onSaleProducts'
        ));
    }

    public function about()
    {
        return view('about');
    }

    public function contact()
    {
        return view('contact');
    }

    public function contactStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string|max:2000',
        ]);

        // Here you would typically send an email or store in database
        // For now, we'll just return with success message

        return redirect()->route('contact')
            ->with('success', 'Thank you for your message! We\'ll get back to you soon.');
    }
}
