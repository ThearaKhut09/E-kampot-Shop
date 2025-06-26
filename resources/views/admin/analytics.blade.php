@extends('admin.layout')

@section('title', 'Analytics & Reports')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Analytics & Reports</h2>
        <div class="flex space-x-2">
            <select id="dateRange" class="bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 text-sm">
                <option value="7">Last 7 days</option>
                <option value="30" selected>Last 30 days</option>
                <option value="90">Last 90 days</option>
                <option value="365">Last year</option>
            </select>
            <button onclick="exportReport()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                Export CSV
            </button>
        </div>
    </div>

    <!-- Key Performance Indicators -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Revenue</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">${{ number_format($analytics['total_revenue'], 2) }}</p>
                    @if($analytics['revenue_growth'] > 0)
                        <p class="text-sm text-green-600">+{{ number_format($analytics['revenue_growth'], 1) }}% from last period</p>
                    @else
                        <p class="text-sm text-red-600">{{ number_format($analytics['revenue_growth'], 1) }}% from last period</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 dark:bg-green-900 rounded-lg">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Orders</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($analytics['total_orders']) }}</p>
                    @if($analytics['orders_growth'] > 0)
                        <p class="text-sm text-green-600">+{{ number_format($analytics['orders_growth'], 1) }}% from last period</p>
                    @else
                        <p class="text-sm text-red-600">{{ number_format($analytics['orders_growth'], 1) }}% from last period</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 dark:bg-purple-900 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">New Customers</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($analytics['new_customers']) }}</p>
                    @if($analytics['customers_growth'] > 0)
                        <p class="text-sm text-green-600">+{{ number_format($analytics['customers_growth'], 1) }}% from last period</p>
                    @else
                        <p class="text-sm text-red-600">{{ number_format($analytics['customers_growth'], 1) }}% from last period</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 dark:bg-yellow-900 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Average Order Value</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">${{ number_format($analytics['avg_order_value'], 2) }}</p>
                    @if($analytics['aov_growth'] > 0)
                        <p class="text-sm text-green-600">+{{ number_format($analytics['aov_growth'], 1) }}% from last period</p>
                    @else
                        <p class="text-sm text-red-600">{{ number_format($analytics['aov_growth'], 1) }}% from last period</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Revenue Chart -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Revenue Trend</h3>
            <div class="h-64">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Orders Chart -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Orders Trend</h3>
            <div class="h-64">
                <canvas id="ordersChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Additional Analytics -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Top Products -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Top Selling Products</h3>
            <div class="space-y-3">
                @foreach($analytics['top_products'] as $product)
                    <div class="flex justify-between items-center">
                        <div class="flex items-center">
                            @if($product->image)
                                <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="w-8 h-8 object-cover rounded mr-3">
                            @endif
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $product->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $product->total_sold ?? 0 }} sold</p>
                            </div>
                        </div>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">${{ number_format($product->total_revenue ?? 0, 2) }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Top Categories -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Top Categories</h3>
            <div class="space-y-3">
                @foreach($analytics['top_categories'] as $category)
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $category->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $category->products_count ?? 0 }} products</p>
                        </div>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $category->total_sales ?? 0 }} sales</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recent Activity</h3>
            <div class="space-y-3">
                @foreach($analytics['recent_activity'] as $activity)
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-gray-900 dark:text-white">{{ $activity['message'] }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $activity['time'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Detailed Reports -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Order Status Breakdown</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                @foreach($analytics['order_statuses'] as $status => $count)
                    <div class="text-center">
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $count }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 capitalize">{{ $status }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Revenue Chart
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($analytics['chart_labels']) !!},
        datasets: [{
            label: 'Revenue',
            data: {!! json_encode($analytics['revenue_data']) !!},
            borderColor: 'rgb(59, 130, 246)',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.1,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '$' + value.toLocaleString();
                    }
                }
            }
        }
    }
});

// Orders Chart
const ordersCtx = document.getElementById('ordersChart').getContext('2d');
new Chart(ordersCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($analytics['chart_labels']) !!},
        datasets: [{
            label: 'Orders',
            data: {!! json_encode($analytics['orders_data']) !!},
            backgroundColor: 'rgba(34, 197, 94, 0.8)',
            borderColor: 'rgb(34, 197, 94)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Export functionality
function exportReport() {
    const dateRange = document.getElementById('dateRange').value;
    window.location.href = `/admin/analytics/export?days=${dateRange}`;
}

// Date range change handler
document.getElementById('dateRange').addEventListener('change', function() {
    const days = this.value;
    window.location.href = `/admin/analytics?days=${days}`;
});
</script>
@endsection
