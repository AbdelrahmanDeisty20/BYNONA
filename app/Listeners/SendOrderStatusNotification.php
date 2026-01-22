<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Notification;
use App\Events\OrdertStatusEvent;

class SendOrderStatusNotification
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
    public function handle(OrdertStatusEvent $event): void
    {
        $order = $event->order;

        // ✅ نتأكد إن الحالة processing فقط
        if ($order->status !== 'processing') {
            return;
        }

        // ❌ منع التكرار
        $alreadySent = Notification::where('order_id', $order->id)
            ->where('title_en', 'Order Processing')
            ->exists();

        if ($alreadySent) {
            return;
        }

        Notification::create([
            'user_id'    => $order->user_id,
            'order_id'   => $order->id,

            'title_ar'   => 'تم تأكيد طلبك',
            'title_en'   => 'Order Processing',

            'message_ar' => "تم تأكيد طلبك رقم {$order->order_number} وجاري الشحن",
            'message_en' => "Your order #{$order->order_number} has been confirmed and is being shipped",
        ]);
    }
}
