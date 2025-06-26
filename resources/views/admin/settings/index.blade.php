@extends('admin.layout')

@section('title', 'Settings')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Site Settings</h2>
    </div>

    <!-- Settings Form -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
        <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="p-6 space-y-8">
            @csrf
            @method('PUT')

            <!-- Site Information -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Site Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="site_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Site Name</label>
                        <input type="text" name="site_name" id="site_name" value="{{ old('site_name', $settings['site_name'] ?? '') }}" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                        @error('site_name')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="site_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Site Email</label>
                        <input type="email" name="site_email" id="site_email" value="{{ old('site_email', $settings['site_email'] ?? '') }}" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                        @error('site_email')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="site_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Site Description</label>
                        <textarea name="site_description" id="site_description" rows="3" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('site_description', $settings['site_description'] ?? '') }}</textarea>
                        @error('site_description')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="site_phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone Number</label>
                        <input type="text" name="site_phone" id="site_phone" value="{{ old('site_phone', $settings['site_phone'] ?? '') }}" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('site_phone')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="site_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Address</label>
                        <input type="text" name="site_address" id="site_address" value="{{ old('site_address', $settings['site_address'] ?? '') }}" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('site_address')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <!-- Store Settings -->
            <div class="border-t border-gray-200 dark:border-gray-600 pt-8">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Store Settings</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div>
                        <label for="currency" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Currency</label>
                        <select name="currency" id="currency" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                            <option value="USD" {{ (old('currency', $settings['currency'] ?? '') === 'USD') ? 'selected' : '' }}>USD ($)</option>
                            <option value="EUR" {{ (old('currency', $settings['currency'] ?? '') === 'EUR') ? 'selected' : '' }}>EUR (€)</option>
                            <option value="GBP" {{ (old('currency', $settings['currency'] ?? '') === 'GBP') ? 'selected' : '' }}>GBP (£)</option>
                            <option value="KHR" {{ (old('currency', $settings['currency'] ?? '') === 'KHR') ? 'selected' : '' }}>KHR (៛)</option>
                        </select>
                        @error('currency')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="tax_rate" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tax Rate (%)</label>
                        <input type="number" name="tax_rate" id="tax_rate" step="0.01" min="0" max="100" value="{{ old('tax_rate', $settings['tax_rate'] ?? '0') }}" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                        @error('tax_rate')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="shipping_cost" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Shipping Cost</label>
                        <input type="number" name="shipping_cost" id="shipping_cost" step="0.01" min="0" value="{{ old('shipping_cost', $settings['shipping_cost'] ?? '0') }}" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                        @error('shipping_cost')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="free_shipping_threshold" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Free Shipping Threshold</label>
                        <input type="number" name="free_shipping_threshold" id="free_shipping_threshold" step="0.01" min="0" value="{{ old('free_shipping_threshold', $settings['free_shipping_threshold'] ?? '0') }}" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                        @error('free_shipping_threshold')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <!-- Branding -->
            <div class="border-t border-gray-200 dark:border-gray-600 pt-8">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Branding</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="site_logo" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Site Logo</label>
                        @if(isset($settings['site_logo']) && $settings['site_logo'])
                            <div class="mt-2 mb-3">
                                <img src="{{ Storage::url($settings['site_logo']) }}" alt="Current Logo" class="h-16 w-auto object-contain">
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Current logo</p>
                            </div>
                        @endif
                        <input type="file" name="site_logo" id="site_logo" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">PNG, JPG, GIF up to 2MB</p>
                        @error('site_logo')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="site_favicon" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Favicon</label>
                        @if(isset($settings['site_favicon']) && $settings['site_favicon'])
                            <div class="mt-2 mb-3">
                                <img src="{{ Storage::url($settings['site_favicon']) }}" alt="Current Favicon" class="h-8 w-8 object-contain">
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Current favicon</p>
                            </div>
                        @endif
                        <input type="file" name="site_favicon" id="site_favicon" accept=".ico,.png" class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">ICO, PNG up to 1MB</p>
                        @error('site_favicon')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="border-t border-gray-200 dark:border-gray-600 pt-6">
                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md text-sm font-medium">
                        Save Settings
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
