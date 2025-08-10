<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminAnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $days = $request->get('days', 30);
        $startDate = Carbon::now()->subDays($days);
        $endDate = Carbon::now();

        // Previous period for comparison
        $prevStartDate = Carbon::now()->subDays($days * 2);
        $prevEndDate = Carbon::now()->subDays($days);

        // Current period stats
        $currentStats = $this->getPeriodStats($startDate, $endDate);
        $previousStats = $this->getPeriodStats($prevStartDate, $prevEndDate);

        // Calculate growth percentages
        $analytics = [
            'total_revenue' => $currentStats['revenue'],
            'revenue_growth' => $this->calculateGrowth($currentStats['revenue'], $previousStats['revenue']),
            'total_orders' => $currentStats['orders'],
            'orders_growth' => $this->calculateGrowth($currentStats['orders'], $previousStats['orders']),
            'new_customers' => $currentStats['customers'],
            'customers_growth' => $this->calculateGrowth($currentStats['customers'], $previousStats['customers']),
            'avg_order_value' => $currentStats['orders'] > 0 ? $currentStats['revenue'] / $currentStats['orders'] : 0,
            'aov_growth' => $this->calculateGrowth(
                $currentStats['orders'] > 0 ? $currentStats['revenue'] / $currentStats['orders'] : 0,
                $previousStats['orders'] > 0 ? $previousStats['revenue'] / $previousStats['orders'] : 0
            ),
        ];

        // Chart data
        $chartData = $this->getChartData($days);
        $analytics = array_merge($analytics, $chartData);

        // Top products
        $analytics['top_products'] = $this->getTopProducts($startDate, $endDate);

        // Top categories
        $analytics['top_categories'] = $this->getTopCategories($startDate, $endDate);

        // Recent activity
        $analytics['recent_activity'] = $this->getRecentActivity();

        // Order status breakdown
        $analytics['order_statuses'] = Order::whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('status')
            ->selectRaw('status, count(*) as count')
            ->pluck('count', 'status')
            ->toArray();

        return view('admin.analytics', compact('analytics', 'days'));
    }

    public function export(Request $request)
    {
        $days = $request->get('days', 30);
        $startDate = Carbon::now()->subDays($days);
        $endDate = Carbon::now();

        $orders = Order::with(['user', 'orderItems.product'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $filename = 'orders_report_' . $startDate->format('Y-m-d') . '_to_' . $endDate->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($orders) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'Order ID',
                'Customer Name',
                'Customer Email',
                'Order Date',
                'Status',
                'Payment Status',
                'Items Count',
                'Total Amount',
                'Payment Method'
            ]);

            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->id,
                    $order->user->name,
                    $order->user->email,
                    $order->created_at->format('Y-m-d'),
                    ucfirst($order->status),
                    ucfirst($order->payment_status),
                    $order->orderItems->count(),
                    number_format($order->total_amount, 2),
                    ucfirst($order->payment_method)
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function getPeriodStats($startDate, $endDate)
    {
        $orders = Order::whereBetween('created_at', [$startDate, $endDate]);

        return [
            'revenue' => $orders->where('status', '!=', 'cancelled')->sum('total_amount'),
            'orders' => $orders->count(),
            'customers' => User::whereBetween('created_at', [$startDate, $endDate])->count(),
        ];
    }

    private function calculateGrowth($current, $previous)
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }

        return (($current - $previous) / $previous) * 100;
    }

    private function getChartData($days)
    {
        $labels = [];
        $revenueData = [];
        $ordersData = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->format('M d');

            $dayRevenue = Order::whereDate('created_at', $date->format('Y-m-d'))
                ->where('status', '!=', 'cancelled')
                ->sum('total_amount');
            $revenueData[] = $dayRevenue;

            $dayOrders = Order::whereDate('created_at', $date->format('Y-m-d'))
                ->count();
            $ordersData[] = $dayOrders;
        }

        return [
            'chart_labels' => $labels,
            'revenue_data' => $revenueData,
            'orders_data' => $ordersData,
        ];
    }

    private function getTopProducts($startDate, $endDate)
    {
        return Product::select('products.*')
            ->selectRaw('SUM(order_items.quantity) as total_sold')
            ->selectRaw('SUM(order_items.total) as total_revenue')
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.status', '!=', 'cancelled')
            ->groupBy('products.id')
            ->orderBy('total_revenue', 'desc')
            ->take(5)
            ->get();
    }

    private function getTopCategories($startDate, $endDate)
    {
        return Category::select('categories.*')
            ->selectRaw('COUNT(DISTINCT order_items.id) as total_sales')
            ->join('product_categories', 'categories.id', '=', 'product_categories.category_id')
            ->join('products', 'product_categories.product_id', '=', 'products.id')
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.status', '!=', 'cancelled')
            ->groupBy('categories.id')
            ->orderBy('total_sales', 'desc')
            ->take(5)
            ->get();
    }

    private function getRecentActivity()
    {
        $activities = [];

        // Recent orders
        $recentOrders = Order::with('user')
            ->latest()
            ->take(3)
            ->get();

        foreach ($recentOrders as $order) {
            $activities[] = [
                'message' => "New order #{$order->id} from {$order->user->name}",
                'time' => $order->created_at->diffForHumans(),
            ];
        }

        // Recent users
        $recentUsers = User::latest()->take(2)->get();
        foreach ($recentUsers as $user) {
            $activities[] = [
                'message' => "New user registered: {$user->name}",
                'time' => $user->created_at->diffForHumans(),
            ];
        }

        return $activities;
    }
}
