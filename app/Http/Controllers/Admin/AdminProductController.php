<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['categories', 'reviews'])
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            'stock_quantity' => 'required|integer|min:0',
            'sku' => 'required|string|max:100|unique:products',
            'categories' => 'required|array|min:1',
            'categories.*' => 'exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'gallery' => 'nullable|array',
            'gallery.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:active,inactive',
            'featured' => 'boolean',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string|max:100',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ]);

        $product = new Product();
        $product->name = $request->name;
        $product->slug = Str::slug($request->name);
        $product->description = $request->description;
        $product->price = $request->price;
        $product->sale_price = $request->sale_price;
        $product->stock_quantity = $request->stock_quantity;
        $product->sku = $request->sku;
        $product->status = $request->status;
        $product->featured = $request->boolean('featured');
        $product->weight = $request->weight;
        $product->dimensions = $request->dimensions;
        $product->meta_title = $request->meta_title;
        $product->meta_description = $request->meta_description;

        // Handle main image
        if ($request->hasFile('image')) {
            $product->image = $request->file('image')->store('products', 'public');
        }

        // Handle gallery images
        if ($request->hasFile('gallery')) {
            $galleryImages = [];
            foreach ($request->file('gallery') as $file) {
                $galleryImages[] = $file->store('products/gallery', 'public');
            }
            $product->gallery = json_encode($galleryImages);
        }

        $product->save();

        // Attach categories
        $product->categories()->attach($request->categories);

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    public function show(Product $product)
    {
        $product->load(['categories', 'reviews.user', 'orderItems']);
        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        $product->load('categories');
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            'stock_quantity' => 'required|integer|min:0',
            'sku' => 'required|string|max:100|unique:products,sku,' . $product->id,
            'categories' => 'required|array|min:1',
            'categories.*' => 'exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'gallery' => 'nullable|array',
            'gallery.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:active,inactive',
            'featured' => 'boolean',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string|max:100',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ]);

        $product->name = $request->name;
        $product->slug = Str::slug($request->name);
        $product->description = $request->description;
        $product->price = $request->price;
        $product->sale_price = $request->sale_price;
        $product->stock_quantity = $request->stock_quantity;
        $product->sku = $request->sku;
        $product->status = $request->status;
        $product->featured = $request->boolean('featured');
        $product->weight = $request->weight;
        $product->dimensions = $request->dimensions;
        $product->meta_title = $request->meta_title;
        $product->meta_description = $request->meta_description;

        // Handle main image
        if ($request->hasFile('image')) {
            // Delete old image
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $product->image = $request->file('image')->store('products', 'public');
        }

        // Handle gallery images
        if ($request->hasFile('gallery')) {
            // Delete old gallery images
            if ($product->gallery) {
                $oldGallery = json_decode($product->gallery, true);
                foreach ($oldGallery as $oldImage) {
                    Storage::disk('public')->delete($oldImage);
                }
            }

            $galleryImages = [];
            foreach ($request->file('gallery') as $file) {
                $galleryImages[] = $file->store('products/gallery', 'public');
            }
            $product->gallery = json_encode($galleryImages);
        }

        $product->save();

        // Sync categories
        $product->categories()->sync($request->categories);

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        // Delete images
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        if ($product->gallery) {
            $gallery = json_decode($product->gallery, true);
            foreach ($gallery as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
    }
}
