<?php

namespace App\Observers;

use App\Models\Property;
use App\Models\User;
use App\Models\Notification;
use App\Service\FirebaseNotificationService;
use App\Service\ProductHelper;
use Illuminate\Support\Facades\DB;

class PropertyObserver
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
        // Trigger only when stock hits exactly 10 or 0, and it was different before
        if ($property->isDirty('stock') && in_array($property->stock, [10, 0])) {
            $this->sendLowStockNotifications($property);
            
            // Notify Admins
            ProductHelper::checkProductQuantity($property);
        }
    }

    protected function sendLowStockNotifications(Property $property)
    {
        $attributes = ProductHelper::getAttributesString($property, 'ar');
        $attrString = $attributes ? " ($attributes)" : "";

        $productName = $property->product ? $property->product->name_ar : 'منتج';
        
        if ($property->stock == 10) {
            $title = "تنبيه توفر المنتج";
            $body = "متبقي 10 كميات فقط لمنتجك الذي في السلة ($productName$attrString)";
        } else {
            $title = "نفاد كمية المنتج";
            $body = "لقد نفدت كمية المنتج الذي في سلتك ($productName$attrString)";
        }

        // جلب معرفات المستخدمين الذين لديهم هذا الـ variant في سلتهم النشطة
        $query = DB::table('cart_items')
            ->join('carts', 'cart_items.cart_id', '=', 'carts.id')
            ->where('cart_items.property_id', $property->id)
            ->where('carts.status', 'active');
        
        // استبعاد المستخدم الحالي (المشتري) إذا كان هو من يقوم بالتحديث حالياً
        if (auth()->check()) {
            $query->where('carts.user_id', '!=', auth()->id());
        }

        $userIds = $query->pluck('carts.user_id')->unique();

        if ($userIds->isEmpty()) {
            return;
        }

        foreach ($userIds as $userId) {
            $user = User::with('fcmTokens')->find($userId);
            if (!$user) continue;

            // حفظ الإشعار في قاعدة البيانات
            Notification::create([
                'user_id' => $user->id,
                'title'   => $title,
                'body'    => $body,
                'type'    => 'low_stock',
                'data'    => ['property_id' => $property->id, 'product_id' => $property->product_id],
                'is_read' => false,
            ]);

            // إرسال عبر Firebase
            foreach ($user->fcmTokens as $tokenRecord) {
                $this->firebaseService->sendToToken($tokenRecord->fcm_token, $title, $body);
            }
        }
    }
}
