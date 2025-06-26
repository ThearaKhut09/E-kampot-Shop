<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\User;
use App\Models\Review;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index()
    {
        // Dashboard statistics
        $stats = [
            'total_products' => Product::count(),
            'total_categories' => Category::count(),
            'total_orders' => Order::count(),
            'total_users' => User::count(),
            'total_revenue' => Order::where('status', 'completed')->sum('total_amount'),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'total_reviews' => Review::count(),
            'low_stock_products' => Product::where('stock_quantity', '<', 10)->count(),
        ];

        // Recent orders
        $recent_orders = Order::with(['user', 'orderItems.product'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Top selling products
        $top_products = Product::withCount('orderItems')
            ->orderBy('order_items_count', 'desc')
            ->take(5)
            ->get();

        // Monthly revenue chart data
        $monthly_revenue = Order::select(
            DB::raw('strftime("%Y-%m", created_at) as month'),
            DB::raw('SUM(total_amount) as revenue')
        )
        ->where('status', 'completed')
        ->where('created_at', '>=', now()->subMonths(12))
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        return view('admin.dashboard', compact('stats', 'recent_orders', 'top_products', 'monthly_revenue'));
    }
}
