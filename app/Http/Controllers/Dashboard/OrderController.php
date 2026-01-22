<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\orders\CancelOrder;
use App\Models\{Order, Notification, Setting, Property};
use App\Service\FirebaseNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        $query = Order::with('user');

        if ($search = request('q')) {
            $query->where(function ($q) use ($search) {
                $q
                    ->where('id', 'like', "%{$search}%")
                    ->orWhere('order_number', 'like', "%{$search}%")
                    ->orWhere('user_name', 'like', "%{$search}%")
                    ->orWhere('user_phone', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq
                            ->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    });
            });
        }

        $orders = $query->latest()->paginate(10)->withQueryString();
        return view('dashboard.orders.index', compact('orders'));
    }

    public function show($id)
    {
        $order = Order::with([
            'user',
            'items.product',
            'items.variantAttributes',
            'governorate'
        ])->findOrFail($id);

        $shippingSetting = Setting::where('governorate_id', $order->governorate_id)->first();
        $shippingCost = $shippingSetting ? $shippingSetting->shipping : 0;

        return view('dashboard.orders.show', compact('order', 'shippingCost'));
    }

    public function cancelOrder(CancelOrder $request, $id)
    {
        $order = Order::findOrFail($id);

        if ($order->status === 'cancelled') {
            return redirect()->back();
        }

        $order->status = 'cancelled';
        $order->notice = request('notice');
        $order->save();

        foreach ($order->items as $item) {
            $item->stock += $item->pivot->quantity;
            $item->save();

            if ($item->trashed()) {
                $item->restore();
            }
        }

        $user = $order->user;
        if ($user) {
            $title = 'تحديث حالة الطلب';
            $body = "تم إلغاء الطلب رقم {$order->order_number} ";

            Notification::create([
                'user_id' => $user->id,
                'title' => $title,
                'body' => $body,
                'type' => 'order_status',
                'data' => ['order_id' => $order->id, 'status' => 'cancelled'],
            ]);

            $service = app(FirebaseNotificationService::class);
            $tokens = $user->fcmTokens()->pluck('fcm_token');

            foreach ($tokens as $token) {
                try {
                    $service->sendToToken($token, $title, $body);
                } catch (\Exception $e) {
                    // ignore
                }
            }
        }

        return redirect()->back()->with('success', __('Order cancelled successfully'));
    }

    public function compeleteOrder($id)
    {
        $order = Order::findOrFail($id);
        $order->status = 'completed';
        $order->save();
        return redirect()->back();
    }

    public function processOrder($id)
    {
        $order = Order::findOrFail($id);
        $order->status = 'processing';
        $order->save();

        // إرسال إشعار للمستخدم
        $user = $order->user;
        if ($user) {
            $title = 'تحديث حالة الطلب';
            $body = "تم شحن الطلب رقم {$order->order_number} وسيصل إليك قريبًا";

            // 1. حفظ في قاعدة البيانات
            Notification::create([
                'user_id' => $user->id,
                'title' => $title,
                'body' => $body,
                'type' => 'order_status',
                'data' => ['order_id' => $order->id, 'status' => 'processing'],
            ]);

            // 2. الإرسال عبر Firebase لكل توكنات المستخدم
            $service = app(FirebaseNotificationService::class);
            $tokens = $user->fcmTokens()->pluck('fcm_token');

            foreach ($tokens as $token) {
                try {
                    $service->sendToToken($token, $title, $body);
                } catch (\Exception $e) {
                    // تجاهل الأخطاء لتوكنات معينة
                }
            }
        }

        return redirect()->back();
    }

    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();
        return redirect()->back();
    }
}
