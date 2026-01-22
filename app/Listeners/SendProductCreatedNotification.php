<?php

namespace App\Listeners;

use App\Events\ProductCreatedEvent;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendProductCreatedNotification
{
    /**
     * Handle the event.
     */
    public function handle(ProductCreatedEvent $event): void
    {
        $product = $event->product;

        $users = User::where('user_type', 'user')->where("is_verfived",true)->get();

        foreach ($users as $user) {
            Notification::create([
                'title_ar'   => 'تم إنشاء منتج جديد',
                'title_en'   => 'New Product Created',
                'message_ar' => "تم إنشاء المنتج {$product->name_ar}",
        'message_en' => "Product {$product->name_en} has been created",
                'user_id'    => $user->id,
                'product_id' => $product->id,
                'order_id'   => null,
                'offer_id'   => null,
                'payment_id' => null,
            ]);
        }
    }
}