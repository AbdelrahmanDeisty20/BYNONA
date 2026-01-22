<?php

namespace App\Listeners;

use App\Events\OfferCreatedForCartItems;
use App\Models\{Cart, Notification, UserFcmToken};
use App\Service\FirebaseNotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class SendOfferCreatedForCartItemsNotification
{
    protected $firebaseService;

    /**
     * Create the event listener.
     */
    public function __construct(FirebaseNotificationService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    /**
     * Handle the event.
     */
    public function handle(OfferCreatedForCartItems $event): void
    {
        $offer = $event->offer;
        $property = $offer->variant; // العلاقة في موديل Offer هي variant
        
        if (!$property) {
            return;
        }

        $product = $property->product;

        // البحث عن العربات التي تحتوي على هذا المنتج (Variant)
        // العلاقة في Property هي cart (belongsToMany)
        $carts = $property->cart()->where('status', 'active')->with('user')->get();

        foreach ($carts as $cart) {
    $user = $cart->user;
    if (!$user) continue;

    $title = "عرض جديد على منتج في سلتك!";
    $body  = "المنتج '{$product->name_ar}' الموجود في سلتك حصل على عرض جديد.";

    // 1️⃣ حفظ الإشعار في DB
    Notification::create([
        'user_id' => $user->id,
        'title'   => $title,
        'body'    => $body,
        'type'    => 'cart_offer',
        'data'    => [
            'offer_id'    => $offer->id,
            'product_id'  => $product->id,
            'property_id' => $property->id,
        ],
        'is_read' => false,
    ]);

    // 2️⃣ جلب المستخدمين اللي عندهم توكن فقط
    $userWithTokens = User::where('id', $user->id)
        ->whereHas('fcmTokens')
        ->with('fcmTokens')
        ->first();

    if (!$userWithTokens) {
        continue; // المستخدم ده مفيهوش توكن → نتجاهل
    }

    foreach ($userWithTokens->fcmTokens as $token) {
        try {
            $this->firebaseService->sendToToken($token->fcm_token, $title, $body);
        } catch (\Throwable $e) {
            Log::error("FCM Error for user {$user->id}: " . $e->getMessage());
        }
    }
}

    }
}
