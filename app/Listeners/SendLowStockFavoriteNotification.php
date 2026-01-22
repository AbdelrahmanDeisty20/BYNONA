<?php

namespace App\Listeners;

use App\Events\LowStockDetected;
use App\Models\{Notification, UserFcmToken, User};
use App\Service\FirebaseNotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SendLowStockFavoriteNotification
{
    protected $firebaseService;

    public function __construct(FirebaseNotificationService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    public function handle(LowStockDetected $event): void
    {
        $property = $event->property;
        $productName = $property->product ? ($property->product->name_ar ?? $property->product->name_en) : 'منتج';
        
        $title = "تنبيه توفر المنتج";
        $body = "متبقي 3 كميات فقط لمنتجك الذي في المفضلة ($productName)";

        // مستخدمي السلة (لتجنب تكرار الإشعار) - اختياري حسب رغبة العميل، لكن سأجعله مستقلاً هنا كما في الطلب
        $cartUserIds = DB::table('cart_items')
            ->join('carts', 'cart_items.cart_id', '=', 'carts.id')
            ->where('cart_items.property_id', $property->id)
            ->where('carts.status', 'active')
            ->pluck('carts.user_id');

        $userIds = DB::table('favorites')
            ->where('product_id', $property->product_id)
            ->where('is_favorite', true)
            ->whereNotIn('user_id', $cartUserIds) // تجنب التكرار
            ->pluck('user_id')
            ->unique();

        foreach ($userIds as $userId) {
            $user = User::with('fcmTokens')->find($userId);
            if (!$user) continue;

            Notification::create([
                'user_id' => $user->id,
                'title'   => $title,
                'body'    => $body,
                'type'    => 'low_stock_favorite',
                'data'    => ['property_id' => $property->id, 'product_id' => $property->product_id],
                'is_read' => false,
            ]);

            foreach ($user->fcmTokens as $tokenRecord) {
                try {
                    $this->firebaseService->sendToToken($tokenRecord->fcm_token, $title, $body);
                } catch (\Throwable $e) {
                    Log::error("FCM Error (Low Stock Favorite): " . $e->getMessage());
                }
            }
        }
    }
}
