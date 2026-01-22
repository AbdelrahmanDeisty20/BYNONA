<?php

namespace App\Observers;

use App\Models\{Offer, UserFcmToken};
use App\Service\FirebaseNotificationService;

class OfferObserver
{
    protected $firebaseService;

    public function __construct(FirebaseNotificationService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    /**
     * Handle the Offer "created" event.
     *
     * @param  \App\Models\Offer  $offer
     * @return void
     */
    public function created(Offer $offer)
    {
        $title = "عرض جديد متاح!";
        $body = "تمت إضافة عرض جديد على أحد منتجاتنا، تفقده الآن!";

        // جلب جميع التوكنز المسجلة (بما فيها التي ليس لها user_id)
        $tokens = UserFcmToken::pluck('fcm_token')->unique();

        foreach ($tokens as $token) {
            $this->firebaseService->sendToToken($token, $title, $body);
        }
    }
}
