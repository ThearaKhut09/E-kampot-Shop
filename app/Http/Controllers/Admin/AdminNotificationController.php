<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;

class AdminNotificationController extends Controller
{
    /**
     * Display admin notifications.
     */
    public function index(Request $request)
    {
        $adminId = Auth::guard('admin')->id() ?? Auth::id();

        $query = Notification::where('admin_id', $adminId)->whereNull('user_id');

        // Filter by type
        if ($request->get('type')) {
            $query->where('type', $request->get('type'));
        }

        // Filter by unread
        if ($request->get('filter') === 'unread') {
            $query->where('is_read', false);
        }

        $notifications = $query->latest()->paginate(20);

        // Get notification types for filter
        $types = Notification::where('admin_id', $adminId)
            ->whereNull('user_id')
            ->distinct()
            ->pluck('type');

        return view('admin.notifications.index', compact('notifications', 'types'));
    }

    /**
     * Mark a notification as read.
     */
    public function markRead(Notification $notification)
    {
        $adminId = Auth::guard('admin')->id() ?? Auth::id();

        // Ensure admin can only mark their own notifications as read
        if ($notification->admin_id !== $adminId) {
            abort(403);
        }

        $notification->markAsRead();

        return back()->with('success', 'Notification marked as read.');
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllRead()
    {
        $adminId = Auth::guard('admin')->id() ?? Auth::id();
        NotificationService::markAllAsReadForAdmin($adminId);

        return back()->with('success', 'All notifications marked as read.');
    }

    /**
     * Delete a notification.
     */
    public function delete(Notification $notification)
    {
        $adminId = Auth::guard('admin')->id() ?? Auth::id();

        // Ensure admin can only delete their own notifications
        if ($notification->admin_id !== $adminId) {
            abort(403);
        }

        $notification->delete();

        return back()->with('success', 'Notification deleted.');
    }
}
