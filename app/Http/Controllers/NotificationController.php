<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display user notifications.
     */
    public function index(Request $request)
    {
        $query = Notification::where('user_id', Auth::id())->whereNull('admin_id');

        // Filter by unread
        if ($request->get('filter') === 'unread') {
            $query->where('is_read', false);
        }

        $notifications = $query->latest()->paginate(15);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Mark a notification as read.
     */
    public function markRead(Notification $notification)
    {
        // Ensure user can only mark their own notifications as read
        if ($notification->user_id !== Auth::id()) {
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
        NotificationService::markAllAsReadForUser(Auth::id());

        return back()->with('success', 'All notifications marked as read.');
    }

    /**
     * Delete a notification.
     */
    public function delete(Notification $notification)
    {
        // Ensure user can only delete their own notifications
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        $notification->delete();

        return back()->with('success', 'Notification deleted.');
    }
}
