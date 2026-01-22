<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
{
    $notifications = Notification::where(function ($q) {
            $q->where('user_id', auth()->id())
              ->orWhereNull('user_id');
        })
        ->select(['id', 'title', 'body', 'type', 'created_at',"data"])
        ->orderBy('created_at', 'desc')->get();

    if ($notifications->isEmpty()) {
        return response()->json([
            'success' => true,
            'message' => 'there is no notifications right now',
            'data'    => []
        ], 200);
    }

    return response()->json([
        'success' => true,
        'message' => 'received notifications successfully.',
        'data'    => $notifications,
    ], 200);
}

}
