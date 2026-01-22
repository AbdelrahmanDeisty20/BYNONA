<?php

namespace App\Listeners;

use App\Events\OfferCreatedForFavorites;
use App\Models\{Notification, UserFcmToken, Favorite};
use App\Service\FirebaseNotificationService;
use Illuminate\Support\Facades\Log;

class SendOfferCreatedForFavoritesNotification
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
    public function handle(OfferCreatedForFavorites $event): void
    {
        $offer = $event->offer;
        $property = $offer->variant;
        
        if (!$property) {
            return;
        }

        $product = $property->product;
        if (!$product) {
            return;
        }

        // البحث عن المستخدمين الذين أضافوا هذا المنتج للمفضلة
        $favorites = Favorite::where('product_id', $product->id)
            ->where('is_favorite', true)
            ->with('user')
            ->get();

        foreach ($favorites as $favorite) {
            $user = $favorite->user;
            if (!$user) continue;

            $title = "عرض جديد على منتج في مفضلتك!";
            $body = "المنتج '{$product->name_ar}' الموجود في مفضلتك حصل على عرض جديد.";
            
            // 1. حفظ في قاعدة البيانات
            Notification::create([
                'user_id' => $user->id,
                'title'   => $title,
                'body'    => $body,
                'type'    => 'favorite_offer',
                'data'    => [
                    'offer_id'    => $offer->id,
                    'product_id'  => $product->id,
                    'property_id' => $property->id,
                ],
                'is_read' => false,
            ]);

            // 2. إرسال عبر Firebase
            $tokens = UserFcmToken::where('user_id', $user->id)->pluck('fcm_token');
            foreach ($tokens as $token) {
                try {
                    $this->firebaseService->sendToToken($token, $title, $body);
                } catch (\Throwable $e) {
                    Log::error("FCM Error (Favorites) for user {$user->id}: " . $e->getMessage());
                }
            }
        }
    }
}
