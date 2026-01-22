<?php

namespace App\Listeners;

use App\Events\OfferCreatedEvent;
use App\Models\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendOfferCreatedNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OfferCreatedEvent $event): void
    {
        $offer = $event->offer;

        $users = \App\Models\User::where('user_type', 'user')->get();

        $today = now(); // الوقت الحالي

        foreach ($users as $user) {
            if ($today->between($offer->start, $offer->end)) {
                // العرض صالح الآن
                $message_ar = "العرض {$offer->title_ar} صالح إلى " . $offer->end->format('d/m/Y');
                $message_en = "Offer {$offer->title_en} is valid until " . $offer->end->format('d/m/Y');
            } elseif ($today->lt($offer->start)) {
                // العرض لم يبدأ بعد
                $message_ar = "العرض {$offer->title_ar} سيبدأ من " . $offer->start->format('d/m/Y') .
                    " وينتهي في " . $offer->end->format('d/m/Y');
                $message_en = "Offer {$offer->title_en} will start on " . $offer->start->format('d/m/Y') .
                    " and ends on " . $offer->end->format('d/m/Y');
            } else {
                // العرض انتهى بالفعل، ممكن ما نبعتش إشعار أو نكتب انتهى
                $message_ar = "العرض {$offer->title_ar} انتهى بالفعل";
                $message_en = "Offer {$offer->title_en} has already ended";
            }

            Notification::create([
                'title_ar' => 'تم إنشاء عرض جديد',
                'title_en' => 'New Offer Created',
                'message_ar' => $message_ar,
                'message_en' => $message_en,
                'user_id' => $user->id,
                'product_id' => null,
                'order_id' => null,
                'offer_id' => $offer->id,
                'payment_id' => null,
            ]);
        }
    }
}