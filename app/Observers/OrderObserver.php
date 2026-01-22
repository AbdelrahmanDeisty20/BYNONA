<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\User;
use App\Models\Notification;
use App\Service\FirebaseNotificationService;

class OrderObserver
{
    protected $firebaseService;

    public function __construct(FirebaseNotificationService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    /**
     * Handle the Order "created" event.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function created(Order $order)
    {
        if ($order->status === 'pending') {
            $this->notifyAdmins($order);
        }
    }

    protected function notifyAdmins(Order $order)
    {
        $title = "طلب جديد / New Order";
        $body = "تم استلام طلب جديد رقم ({$order->order_number}) | New order received: {$order->order_number}";

        $admins = User::where('user_type', 'admin')->get();

        foreach ($admins as $admin) {
            // Save to DB
            Notification::create([
                'user_id' => $admin->id,
                'title'   => $title,
                'body'    => $body,
                'type'    => 'order',
                'data'    => ['order_id' => $order->id],
                'is_read' => false,
            ]);

            // Optional: Firebase notifications if needed for admins
            if ($admin->relationLoaded('fcmTokens')) {
                foreach ($admin->fcmTokens as $tokenRecord) {
                    $this->firebaseService->sendToToken($tokenRecord->fcm_token, $title, $body);
                }
            } else {
                $admin->load('fcmTokens');
                foreach ($admin->fcmTokens as $tokenRecord) {
                    $this->firebaseService->sendToToken($tokenRecord->fcm_token, $title, $body);
                }
            }
        }
    }
}
