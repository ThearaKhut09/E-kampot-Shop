<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['user', 'orderItems'])
            ->withCount('orderItems')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'orderItems.product']);
        return view('admin.orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        return view('admin.orders.edit', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled,refunded',
            'notes' => 'nullable|string|max:1000',
        ]);

        $oldStatus = $order->status;
        $order->status = $request->status;
        $order->notes = $request->notes;
        $order->save();

        return redirect()->route('admin.orders.show', $order)
            ->with('success', "Order status updated from {$oldStatus} to {$request->status}.");
    }

    public function destroy(Order $order)
    {
        // Only allow deletion of cancelled orders
        if (!in_array($order->status, ['cancelled', 'refunded'])) {
            return redirect()->route('admin.orders.index')
                ->with('error', 'Only cancelled or refunded orders can be deleted.');
        }

        $order->delete();

        return redirect()->route('admin.orders.index')
            ->with('success', 'Order deleted successfully.');
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled,refunded',
        ]);

        $oldStatus = $order->status;
        $order->status = $request->status;
        $order->save();

        // Send notification to customer (if implemented)
        // event(new OrderStatusChanged($order, $oldStatus));

        return response()->json([
            'success' => true,
            'message' => "Order status updated to {$request->status}",
            'status' => $request->status
        ]);
    }

    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'order_ids' => 'required|array',
            'order_ids.*' => 'exists:orders,id',
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled,refunded',
        ]);

        $updated = Order::whereIn('id', $request->order_ids)
            ->update(['status' => $request->status]);

        return redirect()->route('admin.orders.index')
            ->with('success', "{$updated} orders updated successfully.");
    }
}
