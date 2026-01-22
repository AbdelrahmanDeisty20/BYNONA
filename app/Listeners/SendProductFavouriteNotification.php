<?php

namespace App\Listeners;

use App\Events\ProductFavourite;
use App\Models\{Favorite, Notification};
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendProductFavouriteNotification
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
    public function handle(ProductFavourite $event): void
    {
        $offer   = $event->offer;
        $product = $offer->product;

        // هات كل الناس اللي عاملة المنتج Favorite
        $favorites = Favorite::where('product_id', $product->id)
            ->where('is_favorite', true)
            ->with('user')
            ->get();

        foreach ($favorites as $favorite) {
            Notification::create([
                'user_id'    => $favorite->user_id,
                'product_id' => $product->id,
                'offer_id'   => $offer->id,

                'title_ar'   => 'عرض على منتجك المفضل',
                'title_en'   => 'Offer on your favorite product',

                'message_ar' => "منتجك المفضل {$product->name_ar} عليه عرض حاليًا",
                'message_en' => "Your favorite product {$product->name_en} is now on offer",
            ]);
        }
    }
}
