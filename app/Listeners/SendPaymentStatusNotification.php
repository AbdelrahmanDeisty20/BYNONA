<?php

namespace App\Listeners;

use App\Events\PaymentStatusEvent;
use App\Models\{Notification, User};use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendPaymentStatusNotification
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
    public function handle(PaymentStatusEvent $event): void
    {
        $payment = $event->payment;
        $order   = $payment->order;
        $user    = $order->user;

        // الإدمن فقط
        $admins = User::where('user_type', 'admin')->get();

        foreach ($admins as $admin) {

            if ($payment->status === 'completed') {

                Notification::create([
                    'user_id'    => $admin->id,
                    'payment_id' => $payment->id,
                    'order_id'   => $order->id,

                    'title_ar'   => 'تم الدفع بنجاح',
                    'title_en'   => 'Payment Completed',

                    'message_ar' => "تم استلام دفعة بقيمة {$payment->amount} من المستخدم {$user->name} للطلب رقم {$order->order_number}",
                    'message_en' => "Payment of {$payment->amount} received from user {$user-> first_name} {$user-> last_name}  for order {$order->order_number}",
                ]);
            }

            if ($payment->status === 'failed') {

                Notification::create([
                    'user_id'    => $admin->id,
                    'payment_id' => $payment->id,
                    'order_id'   => $order->id,

                    'title_ar'   => 'فشل عملية الدفع',
                    'title_en'   => 'Payment Failed',

                    'message_ar' => "فشلت عملية دفع بقيمة {$payment->amount} من المستخدم {$user->name} للطلب رقم {$order->order_number}",
                    'message_en' => "Payment of {$payment->amount} failed from user {$user->name} for order {$order->order_number}",
                ]);
            }
        }
    }
}
