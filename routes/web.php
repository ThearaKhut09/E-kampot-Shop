<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\UserOrderController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminReviewController;
use App\Http\Controllers\Admin\AdminSettingController;
use App\Http\Controllers\Admin\AdminAnalyticsController;
use App\Http\Controllers\Admin\AdminBulkController;
use App\Http\Controllers\Admin\AdminSystemController;
use App\Http\Controllers\Admin\Auth\AdminAuthController;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Admin Authentication Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AdminAuthController::class, 'login']);
    Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');
});

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::post('/contact', [HomeController::class, 'contactStore'])->name('contact.store');

// Product Routes
Route::get('/products-test', function() {
    try {
        $categories = \App\Models\Category::active()->parents()->orderBy('sort_order')->get();
        return view('products.test', [
            'products' => collect([]), // Empty collection for testing
            'categories' => $categories
        ]);
    } catch (\Exception $e) {
        return response('Error: ' . $e->getMessage(), 500);
    }
})->name('products.test');

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/category/{slug}', [ProductController::class, 'category'])->name('category.show');

// Cart Routes (Available to all users, but functionality may require login)
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::get('/cart/count', [CartController::class, 'count'])->name('cart.count');

// Cart modification routes (Customer only)
Route::middleware(['customer.auth'])->group(function () {
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/cart/{cartItem}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{cartItem}', [CartController::class, 'remove'])->name('cart.remove');
    Route::delete('/cart', [CartController::class, 'clear'])->name('cart.clear');

    // Checkout Routes (Customer only) - Enhanced
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/process', [CheckoutController::class, 'processPayment'])->name('checkout.process');
    Route::post('/checkout/quick', [CheckoutController::class, 'quickCheckout'])->name('checkout.quick');
});

// User Dashboard and Orders (Authenticated Users Only)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth:web', 'verified'])->name('dashboard');

// Profile Routes (Customer users only)
Route::middleware('customer.auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Customer-specific routes
Route::middleware(['customer.auth'])->group(function () {
    // User Orders (Customer only)
    Route::get('/orders', [UserOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [UserOrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/cancel', [UserOrderController::class, 'cancel'])->name('orders.cancel');

    // Review Routes (Customer only)
    Route::post('/products/{product}/reviews', [App\Http\Controllers\ReviewController::class, 'store'])->name('reviews.store');
    Route::patch('/reviews/{review}', [App\Http\Controllers\ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/reviews/{review}', [App\Http\Controllers\ReviewController::class, 'destroy'])->name('reviews.destroy');
});

// Admin Routes (Admin Only)
Route::middleware(['admin.auth'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');

    // Product Management
    Route::resource('products', AdminProductController::class);

    // Category Management
    Route::resource('categories', AdminCategoryController::class);

    // Order Management
    Route::resource('orders', AdminOrderController::class);

    // User Management
    Route::resource('users', AdminUserController::class);
    Route::patch('users/{user}/activate', [AdminUserController::class, 'activate'])->name('users.activate');
    Route::patch('users/{user}/deactivate', [AdminUserController::class, 'deactivate'])->name('users.deactivate');
    Route::post('users/{user}/verify-email', [AdminUserController::class, 'verifyEmail'])->name('users.verify-email');

    // Review Management
    Route::resource('reviews', AdminReviewController::class)->except(['create', 'store', 'edit']);
    Route::patch('reviews/{review}/approve', [AdminReviewController::class, 'approve'])->name('reviews.approve');
    Route::patch('reviews/{review}/reject', [AdminReviewController::class, 'reject'])->name('reviews.reject');

    // Bulk Actions
    Route::get('bulk', [AdminBulkController::class, 'index'])->name('bulk.index');
    Route::post('bulk/products/status', [AdminBulkController::class, 'productStatus'])->name('bulk.products.status');
    Route::post('bulk/orders/status', [AdminBulkController::class, 'orderStatus'])->name('bulk.orders.status');
    Route::post('bulk/users/action', [AdminBulkController::class, 'userAction'])->name('bulk.users.action');
    Route::post('bulk/categories/action', [AdminBulkController::class, 'categoryAction'])->name('bulk.categories.action');
    Route::post('bulk/maintenance', [AdminBulkController::class, 'maintenance'])->name('bulk.maintenance');
    Route::post('bulk/export', [AdminBulkController::class, 'export'])->name('bulk.export');
    Route::post('bulk/import', [AdminBulkController::class, 'import'])->name('bulk.import');

    // Analytics
    Route::get('analytics', [AdminAnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('analytics/export', [AdminAnalyticsController::class, 'export'])->name('analytics.export');

    // System Status & Maintenance
    Route::get('system', [AdminSystemController::class, 'index'])->name('system.index');
    Route::post('system/clear-cache', [AdminSystemController::class, 'clearCache'])->name('system.clear-cache');
    Route::post('system/optimize-database', [AdminSystemController::class, 'optimizeDatabase'])->name('system.optimize-database');
    Route::post('system/backup', [AdminSystemController::class, 'backup'])->name('system.backup');
    Route::post('system/maintenance', [AdminSystemController::class, 'maintenance'])->name('system.maintenance');

    // Settings Management
    Route::get('settings', [AdminSettingController::class, 'index'])->name('settings.index');
    Route::put('settings', [AdminSettingController::class, 'update'])->name('settings.update');
});

// Debug route to test AJAX responses
Route::post('/debug/ajax', function(Request $request) {
    return response()->json([
        'success' => true,
        'message' => 'AJAX test successful',
        'user_authenticated' => Auth::check(),
        'request_ajax' => $request->ajax(),
        'request_expects_json' => $request->expectsJson(),
        'headers' => $request->headers->all()
    ]);
})->name('debug.ajax');

// Debug route to test review submission
Route::post('/debug/review/{product}', function(Request $request, Product $product) {
    return response()->json([
        'success' => true,
        'message' => 'Review debug test successful',
        'product_id' => $product->id,
        'product_name' => $product->name,
        'user_authenticated' => Auth::check(),
        'user_id' => Auth::id(),
        'request_data' => $request->all(),
        'request_ajax' => $request->ajax(),
        'request_expects_json' => $request->expectsJson(),
    ]);
})->name('debug.review');

// Debug session route (can be removed after fixing the issue)
Route::get('/debug/session', function() {
    return response()->json([
        'web_guard_check' => Auth::guard('web')->check(),
        'admin_guard_check' => Auth::guard('admin')->check(),
        'web_user' => Auth::guard('web')->user(),
        'admin_user' => Auth::guard('admin')->user(),
        'session_id' => session()->getId(),
        'session_data' => session()->all(),
        'cookies' => request()->cookies->all(),
        'session_config' => [
            'driver' => config('session.driver'),
            'cookie' => config('session.cookie'),
            'admin_cookie' => config('session.admin_cookie'),
        ]
    ]);
})->name('debug.session');

require __DIR__.'/auth.php';
