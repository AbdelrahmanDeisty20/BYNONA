<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProductCreated extends Notification
{
    use Queueable;

    protected $product;

    public function __construct($product)
    {
        $this->product = $product;
    }

    public function via($notifiable)
    {
        return ['database']; // حفظ في جدول notifications
    }

    public function toDatabase($notifiable)
    {
        return [
            'product_id' => $this->product->id,
            'title_ar' => 'تم إنشاء منتج جديد',
            'title_en' => 'New Product Created',
            'message_ar' => "تم إنشاء المنتج {$this->product->name}",
            'message_en' => "Product {$this->product->name} has been created",
            // باقي الحقول تسيبها null لأنها مش مرتبطة هنا
            'order_id' => null,
            'offer_id' => null,
            'payment_id' => null,
        ];
    }
}