<?php

namespace App\Observers;

use App\Models\{Property, User, Notification};
use App\Service\FirebaseNotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CartPropertyObserver
{
    protected $firebaseService;

    public function __construct(FirebaseNotificationService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    /**
     * Handle the Property "updated" event.
     *
     * @param  \App\Models\Property  $property
     * @return void
     */
    public function updated(Property $property)
    {
        // التحقق إذا كان المخزن قل عن 3 ولم يكن كذلك من قبل
        if ($property->isDirty('stock') && $property->stock < 3 && $property->stock >= 0) {
            $this->sendCartLowStockNotifications($property);
        }
    }

    protected function sendCartLowStockNotifications(Property $property)
    {
        $productName = $property->product ? ($property->product->name_ar ?? $property->product->name_en) : 'منتج';
        
        $title = "تنبيه توفر المنتج";
        $body = "متبقي 3 كميات فقط لمنتجك الذي في السلة ($productName)";

        $userIds = DB::table('cart_items')
            ->join('carts', 'cart_items.cart_id', '=', 'carts.id')
            ->where('cart_items.property_id', $property->id)
            ->where('carts.status', 'active')
            ->pluck('carts.user_id')
            ->unique();

        foreach ($userIds as $userId) {
            $user = User::with('fcmTokens')->find($userId);
            if (!$user) continue;

            Notification::create([
                'user_id' => $user->id,
                'title'   => $title,
                'body'    => $body,
                'type'    => 'low_stock_cart',
                'data'    => ['property_id' => $property->id, 'product_id' => $property->product_id],
                'is_read' => false,
            ]);

            foreach ($user->fcmTokens as $tokenRecord) {
                try {
                    $this->firebaseService->sendToToken($tokenRecord->fcm_token, $title, $body);
                } catch (\Throwable $e) {
                    Log::error("FCM Error (Cart Observer): " . $e->getMessage());
                }
            }
        }
    }
}
