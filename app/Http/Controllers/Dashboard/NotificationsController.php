<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationsController extends Controller
{
    public function index(Request $request)
    {
        $query = Notification::where('user_id', Auth::id())->latest();

        if ($request->has('type') && $request->type != 'all') {
            if ($request->type == 'product') {
                $query->whereIn('type', ['product', 'low_stock', 'low_stock_admin']);
            } else {
                $query->where('type', $request->type);
            }
        }

        if ($request->has('unread')) {
            $query->where('is_read', false);
        }

        $notifications = $query->paginate(20);

        return view("dashboard.notifications.index", compact("notifications"));
    }

    public function fetch()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->latest()
            ->limit(5)
            ->get();

        $unreadCount = Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    public function markAsRead($id)
    {
        $notification = Notification::where('user_id', Auth::id())->findOrFail($id);
        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }
}