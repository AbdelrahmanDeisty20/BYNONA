<?php

namespace App\Observers;

use App\Models\{Property, User, Notification};
use App\Service\FirebaseNotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FavoritePropertyObserver
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
        // التحقق إذا كان المخزن قل عن 3 ولم يكن كذلك من قبل لضمان عدم تكرار الإشعار في كل تحديث بسيط
        if ($property->isDirty('stock') && $property->stock < 10 && $property->stock >= 0) {
            $this->sendFavoriteLowStockNotifications($property);
        }
    }

    protected function sendFavoriteLowStockNotifications(Property $property)
    {
        // جلب اسم المنتج (المعرب أو الإنجليزي)
        $productName = $property->product ? ($property->product->name_ar ?? $property->product->name_en) : 'منتج';
        
        $title = "تنبيه توفر المنتج";
         $body = "متبقي 10 كميات فقط لمنتجك الذي في المفضلة ($productName)";

        // جلب معرفات المستخدمين الذين لديهم هذا المنتج في مفضلتهم
        $userIds = DB::table('favorites')
            ->where('product_id', $property->product_id)
            ->where('is_favorite', true)
            ->pluck('user_id')
            ->unique();

        if ($userIds->isEmpty()) {
            return;
        }

        foreach ($userIds as $userId) {
            $user = User::with('fcmTokens')->find($userId);
            if (!$user) continue;

            // 1. حفظ الإشعار في قاعدة البيانات
            Notification::create([
                'user_id' => $user->id,
                'title'   => $title,
                'body'    => $body,
                'type'    => 'low_stock_favorite',
                'data'    => [
                    'property_id' => $property->id, 
                    'product_id'  => $property->product_id
                ],
                'is_read' => false,
            ]);

            // 2. إرسال عبر Firebase
            foreach ($user->fcmTokens as $tokenRecord) {
                try {
                    $this->firebaseService->sendToToken($tokenRecord->fcm_token, $title, $body);
                } catch (\Throwable $e) {
                    Log::error("FCM Error (Favorite Observer) for user {$user->id}: " . $e->getMessage());
                }
            }
        }
    }
}
