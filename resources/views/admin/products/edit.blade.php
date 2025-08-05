@extends('admin.layout')

@section('title', 'Edit Product')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Product: {{ $product->name }}</h2>
        <div class="space-x-2">
            <a href="{{ route('admin.products.show', $product) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                View Product
            </a>
            <a href="{{ route('admin.products.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                Back to Products
            </a>
        </div>
    </div>

    <!-- Product Form -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
        <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <!-- Basic Information -->
            <div class="grid grid-cols-1 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Product Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                    @error('name')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Product Title</label>
                    <input type="text" name="title" id="title" value="{{ old('title', $product->title) }}" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Optional product title">
                    @error('title')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                </div>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                <textarea name="description" id="description" rows="4" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>{{ old('description', $product->description) }}</textarea>
                @error('description')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
            </div>

            <!-- Pricing -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Price ($)</label>
                    <input type="number" name="price" id="price" step="0.01" min="0" value="{{ old('price', $product->price) }}" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                    @error('price')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="stock_quantity" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Stock Quantity</label>
                    <input type="number" name="stock_quantity" id="stock_quantity" min="0" value="{{ old('stock_quantity', $product->stock_quantity) }}" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                    @error('stock_quantity')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                </div>
            </div>

            <!-- Categories -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Categories</label>
                <div class="mt-2 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach($categories as $category)
                        <div class="flex items-center">
                            <input type="checkbox" name="categories[]" value="{{ $category->id }}" id="category_{{ $category->id }}"
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-600 rounded"
                                {{ in_array($category->id, old('categories', $product->categories->pluck('id')->toArray())) ? 'checked' : '' }}>
                            <label for="category_{{ $category->id }}" class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ $category->name }}</label>
                        </div>
                    @endforeach
                </div>
                @error('categories')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
            </div>

            <!-- Images -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Main Image</label>
                    @if($product->image)
                        <div class="mt-2 mb-3">
                            <img src="{{ Storage::url($product->image) }}" alt="Current Image" class="h-20 w-20 object-cover rounded">
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Current image</p>
                        </div>
                    @endif
                    <input type="file" name="image" id="image" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    @error('image')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="gallery" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Gallery Images</label>
                    @if($product->gallery)
                        <div class="mt-2 mb-3 flex flex-wrap gap-2">
                            @foreach(json_decode($product->gallery, true) as $galleryImage)
                                <img src="{{ Storage::url($galleryImage) }}" alt="Gallery Image" class="h-16 w-16 object-cover rounded">
                            @endforeach
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 w-full">Current gallery images</p>
                        </div>
                    @endif
                    <input type="file" name="gallery[]" id="gallery" accept="image/*" multiple class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    @error('gallery')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                </div>
            </div>

            <!-- Additional Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="weight" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Weight (kg)</label>
                    <input type="number" name="weight" id="weight" step="0.01" min="0" value="{{ old('weight', $product->weight) }}" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('weight')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="dimensions" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Dimensions</label>
                    <input type="text" name="dimensions" id="dimensions" placeholder="L x W x H" value="{{ old('dimensions', $product->dimensions) }}" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('dimensions')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                </div>
            </div>

            <!-- Status and Featured -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                    <select name="status" id="status" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="active" {{ old('status', $product->status) === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $product->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                </div>

                <div class="flex items-center pt-6">
                    <input type="checkbox" name="featured" id="featured" value="1" {{ old('featured', $product->featured) ? 'checked' : '' }} class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-600 rounded">
                    <label for="featured" class="ml-2 text-sm text-gray-700 dark:text-gray-300">Featured Product</label>
                </div>
            </div>

            <!-- SEO -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="meta_title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Meta Title</label>
                    <input type="text" name="meta_title" id="meta_title" value="{{ old('meta_title', $product->meta_title) }}" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('meta_title')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="meta_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Meta Description</label>
                    <textarea name="meta_description" id="meta_description" rows="3" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('meta_description', $product->meta_description) }}</textarea>
                    @error('meta_description')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end space-x-3">
                <a href="{{ route('admin.products.index') }}" class="bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-200 px-4 py-2 rounded-md text-sm font-medium">
                    Cancel
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                    Update Product
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
