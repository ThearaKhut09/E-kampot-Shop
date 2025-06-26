@extends('admin.layout')

@section('title', 'Category Details')

@section('content')
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Category Details</h2>
            <div class="flex space-x-2">
                <a href="{{ route('admin.categories.edit', $category) }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                    Edit Category
                </a>
                <a href="{{ route('admin.categories.index') }}"
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                    Back to Categories
                </a>
            </div>
        </div>
    </div>

    <div class="p-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Category Information -->
            <div class="lg:col-span-2">
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Category Information</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Name</label>
                            <p class="text-gray-900 dark:text-white font-medium">{{ $category->name }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Slug</label>
                            <p class="text-gray-900 dark:text-white">{{ $category->slug }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Status</label>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $category->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                {{ $category->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Sort Order</label>
                            <p class="text-gray-900 dark:text-white">{{ $category->sort_order ?? 'Not set' }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Parent Category</label>
                            <p class="text-gray-900 dark:text-white">
                                @if($category->parent)
                                    <a href="{{ route('admin.categories.show', $category->parent) }}"
                                       class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                        {{ $category->parent->name }}
                                    </a>
                                @else
                                    Root Category
                                @endif
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Products Count</label>
                            <p class="text-gray-900 dark:text-white font-medium">{{ $category->products_count }}</p>
                        </div>
                    </div>

                    @if($category->description)
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Description</label>
                            <p class="text-gray-900 dark:text-white">{{ $category->description }}</p>
                        </div>
                    @endif

                    <div class="mt-4 grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Created</label>
                            <p class="text-gray-900 dark:text-white">{{ $category->created_at->format('M d, Y H:i') }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Last Updated</label>
                            <p class="text-gray-900 dark:text-white">{{ $category->updated_at->format('M d, Y H:i') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Subcategories -->
                @if($category->children->count() > 0)
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 mt-6">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Subcategories ({{ $category->children->count() }})</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($category->children as $child)
                                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="font-medium text-gray-900 dark:text-white">
                                                <a href="{{ route('admin.categories.show', $child) }}"
                                                   class="hover:text-blue-600 dark:hover:text-blue-400">
                                                    {{ $child->name }}
                                                </a>
                                            </h4>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $child->products_count }} products</p>
                                        </div>
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $child->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                            {{ $child->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Category Image and Actions -->
            <div class="lg:col-span-1">
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Category Image</h3>

                    @if($category->image)
                        <img src="{{ Storage::url($category->image) }}"
                             alt="{{ $category->name }}"
                             class="w-full h-48 object-cover rounded-lg">
                    @else
                        <div class="w-full h-48 bg-gray-200 dark:bg-gray-600 rounded-lg flex items-center justify-center">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 text-center mt-2">No image uploaded</p>
                    @endif
                </div>

                <!-- Quick Actions -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 mt-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Quick Actions</h3>

                    <div class="space-y-3">
                        <a href="{{ route('admin.categories.edit', $category) }}"
                           class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors inline-block text-center">
                            Edit Category
                        </a>

                        <a href="{{ route('admin.products.index', ['category' => $category->id]) }}"
                           class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors inline-block text-center">
                            View Products ({{ $category->products_count }})
                        </a>

                        @if($category->children->count() === 0 && $category->products_count === 0)
                            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="w-full">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        onclick="return confirm('Are you sure you want to delete this category?')"
                                        class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                                    Delete Category
                                </button>
                            </form>
                        @else
                            <div class="text-sm text-gray-500 dark:text-gray-400 p-2 bg-yellow-50 dark:bg-yellow-900/20 rounded-md">
                                Cannot delete: Category has {{ $category->children->count() }} subcategories and {{ $category->products_count }} products
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
