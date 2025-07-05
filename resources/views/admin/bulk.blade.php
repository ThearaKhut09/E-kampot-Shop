@extends('admin.layout')

@section('title', 'Bulk Actions')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Bulk Actions</h2>
        <div class="text-sm text-gray-500 dark:text-gray-400">
            Perform actions on multiple items at once
        </div>
    </div>

    <!-- Bulk Actions Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Product Bulk Actions -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 bg-blue-50 dark:bg-blue-900/20 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Product Actions</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">Manage multiple products</p>
            </div>
            <div class="p-6 space-y-4">
                <form action="{{ route('admin.bulk.products.status') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Action</label>
                        <select name="action" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white" required>
                            <option value="">Select Action</option>
                            <option value="activate">Activate Products</option>
                            <option value="deactivate">Deactivate Products</option>
                            <option value="feature">Mark as Featured</option>
                            <option value="unfeature">Remove from Featured</option>
                            <option value="delete">Delete Products</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category Filter (Optional)</label>
                        <select name="category_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Product IDs (comma-separated)</label>
                        <textarea name="product_ids" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white" placeholder="1,2,3 or leave empty to apply to all products in category"></textarea>
                    </div>
                    <button type="button" onclick="handleBulkAction(this.form)" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md font-medium">
                        Execute Action
                    </button>
                </form>
            </div>
        </div>

        <!-- Order Bulk Actions -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 bg-green-50 dark:bg-green-900/20 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Order Actions</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">Manage multiple orders</p>
            </div>
            <div class="p-6 space-y-4">
                <form action="{{ route('admin.bulk.orders.status') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Action</label>
                        <select name="action" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white" required>
                            <option value="">Select Action</option>
                            <option value="processing">Mark as Processing</option>
                            <option value="shipped">Mark as Shipped</option>
                            <option value="delivered">Mark as Delivered</option>
                            <option value="cancelled">Cancel Orders</option>
                            <option value="export">Export to CSV</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status Filter (Optional)</label>
                        <select name="current_status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white">
                            <option value="">All Statuses</option>
                            <option value="pending">Pending</option>
                            <option value="processing">Processing</option>
                            <option value="shipped">Shipped</option>
                            <option value="delivered">Delivered</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date Range</label>
                        <div class="grid grid-cols-2 gap-2">
                            <input type="date" name="start_date" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white">
                            <input type="date" name="end_date" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Order IDs (comma-separated)</label>
                        <textarea name="order_ids" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white" placeholder="1,2,3 or leave empty to apply filters"></textarea>
                    </div>
                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md font-medium">
                        Execute Action
                    </button>
                </form>
            </div>
        </div>

        <!-- User Bulk Actions -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 bg-purple-50 dark:bg-purple-900/20 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">User Actions</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">Manage multiple users</p>
            </div>
            <div class="p-6 space-y-4">
                <form action="{{ route('admin.bulk.users.action') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Action</label>
                        <select name="action" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white" required>
                            <option value="">Select Action</option>
                            <option value="activate">Activate Users</option>
                            <option value="deactivate">Deactivate Users</option>
                            <option value="verify_email">Verify Email</option>
                            <option value="send_notification">Send Notification</option>
                            <option value="export">Export to CSV</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Role Filter (Optional)</label>
                        <select name="role" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white">
                            <option value="">All Roles</option>
                            <option value="admin">Admin</option>
                            <option value="customer">Customer</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">User IDs (comma-separated)</label>
                        <textarea name="user_ids" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white" placeholder="1,2,3 or leave empty to apply to all users with role"></textarea>
                    </div>
                    <div id="notification_message" style="display: none;">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notification Message</label>
                        <textarea name="message" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white" placeholder="Enter notification message..."></textarea>
                    </div>
                    <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-md font-medium">
                        Execute Action
                    </button>
                </form>
            </div>
        </div>

        <!-- Category Bulk Actions -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 bg-yellow-50 dark:bg-yellow-900/20 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Category Actions</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">Manage multiple categories</p>
            </div>
            <div class="p-6 space-y-4">
                <form action="{{ route('admin.bulk.categories.action') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Action</label>
                        <select name="action" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white" required>
                            <option value="">Select Action</option>
                            <option value="activate">Activate Categories</option>
                            <option value="deactivate">Deactivate Categories</option>
                            <option value="reorder">Reorder by Name</option>
                            <option value="delete_empty">Delete Empty Categories</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Parent Category Filter</label>
                        <select name="parent_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white">
                            <option value="">All Categories</option>
                            <option value="0">Root Categories Only</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">Subcategories of {{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category IDs (comma-separated)</label>
                        <textarea name="category_ids" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white" placeholder="1,2,3 or leave empty to apply to all"></textarea>
                    </div>
                    <button type="submit" class="w-full bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-md font-medium">
                        Execute Action
                    </button>
                </form>
            </div>
        </div>

        <!-- Database Maintenance -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 bg-red-50 dark:bg-red-900/20 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Database Maintenance</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">System cleanup tasks</p>
            </div>
            <div class="p-6 space-y-4">
                <form action="{{ route('admin.bulk.maintenance') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Maintenance Task</label>
                        <select name="task" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white" required>
                            <option value="">Select Task</option>
                            <option value="clear_cache">Clear Application Cache</option>
                            <option value="cleanup_sessions">Clean Old Sessions</option>
                            <option value="cleanup_logs">Clean Old Logs</option>
                            <option value="optimize_images">Optimize Product Images</option>
                            <option value="backup_database">Backup Database</option>
                        </select>
                    </div>
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-md p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-800 dark:text-yellow-200">
                                    <strong>Warning:</strong> These operations may take time and should be performed during low-traffic periods.
                                </p>
                            </div>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('admin.bulk.maintenance') }}" id="maintenance-form">
                        @csrf
                        <input type="hidden" name="action" value="maintenance">
                        <button type="button" onclick="confirmDelete(document.getElementById('maintenance-form'), 'Maintenance Task', 'Maintenance Task')" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md font-medium">
                            Execute Task
                        </button>
                    </form>
                </form>
            </div>
        </div>

        <!-- Import/Export -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 bg-indigo-50 dark:bg-indigo-900/20 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Import/Export</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">Data import and export tools</p>
            </div>
            <div class="p-6 space-y-4">
                <div class="space-y-4">
                    <!-- Export Section -->
                    <div>
                        <h4 class="font-medium text-gray-900 dark:text-white mb-2">Export Data</h4>
                        <form action="{{ route('admin.bulk.export') }}" method="POST" class="space-y-3">
                            @csrf
                            <select name="export_type" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white" required>
                                <option value="">Select Data Type</option>
                                <option value="products">Products</option>
                                <option value="categories">Categories</option>
                                <option value="orders">Orders</option>
                                <option value="users">Users</option>
                            </select>
                            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md font-medium">
                                Export CSV
                            </button>
                        </form>
                    </div>

                    <!-- Import Section -->
                    <div>
                        <h4 class="font-medium text-gray-900 dark:text-white mb-2">Import Data</h4>
                        <form action="{{ route('admin.bulk.import') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                            @csrf
                            <select name="import_type" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white" required>
                                <option value="">Select Data Type</option>
                                <option value="products">Products</option>
                                <option value="categories">Categories</option>
                                <option value="users">Users</option>
                            </select>
                            <input type="file" name="csv_file" accept=".csv" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white" required>
                            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md font-medium">
                                Import CSV
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Bulk Actions -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Bulk Actions</h3>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Action</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Items Affected</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                        @forelse($recentActions ?? [] as $action)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $action['action'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ ucfirst($action['type']) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $action['count'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $action['date'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $action['status'] == 'completed' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' }}">
                                        {{ ucfirst($action['status']) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                    No recent bulk actions
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
// Show notification message field when send_notification is selected
document.querySelectorAll('select[name="action"]').forEach(select => {
    select.addEventListener('change', function() {
        const messageDiv = this.closest('form').querySelector('#notification_message');
        if (messageDiv) {
            messageDiv.style.display = this.value === 'send_notification' ? 'block' : 'none';
        }
    });
});

// Handle bulk actions with confirmation for destructive operations
function handleBulkAction(form) {
    const actionSelect = form.querySelector('select[name="action"]');
    const action = actionSelect.value;
    
    if (action === 'delete') {
        const itemType = form.querySelector('select[name="action"]').closest('form').querySelector('h3').textContent.includes('Product') ? 'Products' : 'Items';
        confirmDelete(form, 'selected ' + itemType.toLowerCase(), itemType);
    } else if (action === 'cancelled') {
        confirmDelete(form, 'selected orders', 'Orders');
    } else {
        form.submit();
    }
}
</script>
@endsection
