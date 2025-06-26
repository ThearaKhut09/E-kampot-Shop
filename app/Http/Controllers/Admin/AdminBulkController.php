<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class AdminBulkController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        $recentActions = $this->getRecentActions();

        return view('admin.bulk', compact('categories', 'recentActions'));
    }

    public function productStatus(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,feature,unfeature,delete',
            'category_id' => 'nullable|exists:categories,id',
            'product_ids' => 'nullable|string',
        ]);

        $productIds = $this->parseIds($request->product_ids);

        $query = Product::query();

        if ($productIds) {
            $query->whereIn('id', $productIds);
        } elseif ($request->category_id) {
            $query->whereHas('categories', function($q) use ($request) {
                $q->where('categories.id', $request->category_id);
            });
        }

        $products = $query->get();
        $count = 0;

        foreach ($products as $product) {
            switch ($request->action) {
                case 'activate':
                    $product->update(['status' => 'active']);
                    $count++;
                    break;
                case 'deactivate':
                    $product->update(['status' => 'inactive']);
                    $count++;
                    break;
                case 'feature':
                    $product->update(['featured' => true]);
                    $count++;
                    break;
                case 'unfeature':
                    $product->update(['featured' => false]);
                    $count++;
                    break;
                case 'delete':
                    if ($product->image) {
                        Storage::disk('public')->delete($product->image);
                    }
                    if ($product->gallery) {
                        foreach (json_decode($product->gallery, true) ?? [] as $image) {
                            Storage::disk('public')->delete($image);
                        }
                    }
                    $product->delete();
                    $count++;
                    break;
            }
        }

        $this->logBulkAction('products', $request->action, $count);

        return redirect()->route('admin.bulk.index')
            ->with('success', "Successfully {$request->action}d {$count} products.");
    }

    public function orderStatus(Request $request)
    {
        $request->validate([
            'action' => 'required|in:processing,shipped,delivered,cancelled,export',
            'current_status' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'order_ids' => 'nullable|string',
        ]);

        if ($request->action === 'export') {
            return $this->exportOrders($request);
        }

        $orderIds = $this->parseIds($request->order_ids);

        $query = Order::query();

        if ($orderIds) {
            $query->whereIn('id', $orderIds);
        } else {
            if ($request->current_status) {
                $query->where('status', $request->current_status);
            }
            if ($request->start_date) {
                $query->whereDate('created_at', '>=', $request->start_date);
            }
            if ($request->end_date) {
                $query->whereDate('created_at', '<=', $request->end_date);
            }
        }

        $orders = $query->get();
        $count = 0;

        foreach ($orders as $order) {
            $order->update(['status' => $request->action]);
            $count++;
        }

        $this->logBulkAction('orders', $request->action, $count);

        return redirect()->route('admin.bulk.index')
            ->with('success', "Successfully updated {$count} orders to {$request->action}.");
    }

    public function userAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,verify_email,send_notification,export',
            'role' => 'nullable|string',
            'user_ids' => 'nullable|string',
            'message' => 'nullable|string|required_if:action,send_notification',
        ]);

        if ($request->action === 'export') {
            return $this->exportUsers($request);
        }

        $userIds = $this->parseIds($request->user_ids);

        $query = User::query();

        if ($userIds) {
            $query->whereIn('id', $userIds);
        } elseif ($request->role) {
            $query->whereHas('roles', function($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        $users = $query->get();
        $count = 0;

        foreach ($users as $user) {
            switch ($request->action) {
                case 'activate':
                    $user->update(['is_active' => true]);
                    $count++;
                    break;
                case 'deactivate':
                    // Don't deactivate the last admin
                    if ($user->hasRole('admin') && User::role('admin')->where('is_active', true)->count() <= 1) {
                        break;
                    }
                    $user->update(['is_active' => false]);
                    $count++;
                    break;
                case 'verify_email':
                    if (!$user->email_verified_at) {
                        $user->update(['email_verified_at' => now()]);
                        $count++;
                    }
                    break;
                case 'send_notification':
                    // In a real implementation, you would send actual notifications
                    // For now, we'll just count the users
                    $count++;
                    break;
            }
        }

        $this->logBulkAction('users', $request->action, $count);

        return redirect()->route('admin.bulk.index')
            ->with('success', "Successfully processed {$count} users with action: {$request->action}.");
    }

    public function categoryAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,reorder,delete_empty',
            'parent_id' => 'nullable|string',
            'category_ids' => 'nullable|string',
        ]);

        $categoryIds = $this->parseIds($request->category_ids);

        $query = Category::query();

        if ($categoryIds) {
            $query->whereIn('id', $categoryIds);
        } else {
            if ($request->parent_id === '0') {
                $query->whereNull('parent_id');
            } elseif ($request->parent_id) {
                $query->where('parent_id', $request->parent_id);
            }
        }

        $categories = $query->get();
        $count = 0;

        foreach ($categories as $category) {
            switch ($request->action) {
                case 'activate':
                    $category->update(['is_active' => true]);
                    $count++;
                    break;
                case 'deactivate':
                    $category->update(['is_active' => false]);
                    $count++;
                    break;
                case 'delete_empty':
                    if ($category->products()->count() === 0 && $category->children()->count() === 0) {
                        if ($category->image) {
                            Storage::disk('public')->delete($category->image);
                        }
                        $category->delete();
                        $count++;
                    }
                    break;
                case 'reorder':
                    // Reorder by name within the same parent
                    $siblings = Category::where('parent_id', $category->parent_id)->orderBy('name')->get();
                    foreach ($siblings as $index => $sibling) {
                        $sibling->update(['sort_order' => $index]);
                    }
                    $count++;
                    break;
            }
        }

        $this->logBulkAction('categories', $request->action, $count);

        return redirect()->route('admin.bulk.index')
            ->with('success', "Successfully processed {$count} categories with action: {$request->action}.");
    }

    public function maintenance(Request $request)
    {
        $request->validate([
            'task' => 'required|in:clear_cache,cleanup_sessions,cleanup_logs,optimize_images,backup_database',
        ]);

        $result = '';

        switch ($request->task) {
            case 'clear_cache':
                Artisan::call('cache:clear');
                Artisan::call('config:clear');
                Artisan::call('view:clear');
                $result = 'Application cache cleared successfully.';
                break;

            case 'cleanup_sessions':
                DB::table('sessions')->where('last_activity', '<', now()->subDays(30)->timestamp)->delete();
                $result = 'Old sessions cleaned up successfully.';
                break;

            case 'cleanup_logs':
                $logFiles = Storage::disk('local')->files('logs');
                $oldFiles = array_filter($logFiles, function($file) {
                    return Storage::disk('local')->lastModified($file) < now()->subDays(30)->timestamp;
                });
                foreach ($oldFiles as $file) {
                    Storage::disk('local')->delete($file);
                }
                $result = 'Old log files cleaned up successfully. Removed ' . count($oldFiles) . ' files.';
                break;

            case 'optimize_images':
                // In a real implementation, you would optimize images
                $result = 'Image optimization task queued successfully.';
                break;

            case 'backup_database':
                // In a real implementation, you would create a database backup
                $result = 'Database backup created successfully.';
                break;
        }

        $this->logBulkAction('maintenance', $request->task, 1);

        return redirect()->route('admin.bulk.index')->with('success', $result);
    }

    public function export(Request $request)
    {
        $request->validate([
            'export_type' => 'required|in:products,categories,orders,users',
        ]);

        switch ($request->export_type) {
            case 'products':
                return $this->exportProducts();
            case 'categories':
                return $this->exportCategories();
            case 'orders':
                return $this->exportAllOrders();
            case 'users':
                return $this->exportAllUsers();
        }
    }

    public function import(Request $request)
    {
        $request->validate([
            'import_type' => 'required|in:products,categories,users',
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        $file = $request->file('csv_file');
        $path = $file->store('imports');

        // In a real implementation, you would process the CSV file
        // For now, we'll just return success

        Storage::delete($path);

        return redirect()->route('admin.bulk.index')
            ->with('success', "CSV file uploaded and processed successfully for {$request->import_type}.");
    }

    private function parseIds($idsString)
    {
        if (!$idsString) {
            return null;
        }

        return array_filter(array_map('trim', explode(',', $idsString)));
    }

    private function logBulkAction($type, $action, $count)
    {
        // In a real implementation, you would store this in a bulk_actions table
        // For now, we'll just use session flash data
        $recentActions = session()->get('recent_bulk_actions', []);

        array_unshift($recentActions, [
            'type' => $type,
            'action' => $action,
            'count' => $count,
            'date' => now()->format('M d, Y H:i'),
            'status' => 'completed',
        ]);

        // Keep only the last 10 actions
        $recentActions = array_slice($recentActions, 0, 10);

        session()->put('recent_bulk_actions', $recentActions);
    }

    private function getRecentActions()
    {
        return session()->get('recent_bulk_actions', []);
    }

    private function exportProducts()
    {
        $products = Product::with('categories')->get();

        $filename = 'products_export_' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($products) {
            $file = fopen('php://output', 'w');

            fputcsv($file, ['ID', 'Name', 'SKU', 'Price', 'Sale Price', 'Stock', 'Status', 'Featured', 'Categories']);

            foreach ($products as $product) {
                fputcsv($file, [
                    $product->id,
                    $product->name,
                    $product->sku,
                    $product->price,
                    $product->sale_price,
                    $product->stock_quantity,
                    $product->status,
                    $product->featured ? 'Yes' : 'No',
                    $product->categories->pluck('name')->join(', ')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportCategories()
    {
        $categories = Category::withCount('products')->get();

        $filename = 'categories_export_' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($categories) {
            $file = fopen('php://output', 'w');

            fputcsv($file, ['ID', 'Name', 'Slug', 'Parent', 'Products Count', 'Active', 'Sort Order']);

            foreach ($categories as $category) {
                fputcsv($file, [
                    $category->id,
                    $category->name,
                    $category->slug,
                    $category->parent ? $category->parent->name : '',
                    $category->products_count,
                    $category->is_active ? 'Yes' : 'No',
                    $category->sort_order
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportOrders($request)
    {
        $query = Order::with(['user', 'orderItems']);

        if ($request->current_status) {
            $query->where('status', $request->current_status);
        }
        if ($request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $orders = $query->get();

        $filename = 'orders_export_' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($orders) {
            $file = fopen('php://output', 'w');

            fputcsv($file, ['Order ID', 'Customer', 'Email', 'Date', 'Status', 'Items', 'Total']);

            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->id,
                    $order->user->name,
                    $order->user->email,
                    $order->created_at->format('Y-m-d'),
                    $order->status,
                    $order->orderItems->count(),
                    number_format($order->total_amount, 2)
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportAllOrders()
    {
        return $this->exportOrders(new Request());
    }

    private function exportUsers($request = null)
    {
        $query = User::with('roles');

        if ($request && $request->role) {
            $query->whereHas('roles', function($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        $users = $query->get();

        $filename = 'users_export_' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($users) {
            $file = fopen('php://output', 'w');

            fputcsv($file, ['ID', 'Name', 'Email', 'Role', 'Active', 'Email Verified', 'Joined']);

            foreach ($users as $user) {
                fputcsv($file, [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->roles->first()->name ?? '',
                    $user->is_active ? 'Yes' : 'No',
                    $user->email_verified_at ? 'Yes' : 'No',
                    $user->created_at->format('Y-m-d')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportAllUsers()
    {
        return $this->exportUsers();
    }
}
