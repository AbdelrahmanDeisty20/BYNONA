<?php

namespace App\Providers;

use App\Events\PaymentStatusEvent;
use App\Listeners\SendPaymentStatusNotification;
use App\Models\Order;
use App\Models\Product;
use App\Models\Property;
use App\Observers\OrderObserver;
use App\Observers\PropertyObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        \App\Events\ProductCreatedEvent::class => [
            \App\Listeners\SendProductCreatedNotification::class,
        ],
        \App\Events\OfferCreatedEvent::class => [
            \App\Listeners\SendOfferCreatedNotification::class,
        ],
        \App\Events\OrdertStatusEvent::class => [
            \App\Listeners\SendOrderStatusNotification::class,
        ],
        \App\Events\ProductFavourite::class => [
            \App\Listeners\SendProductFavouriteNotification::class,
        ],
        PaymentStatusEvent::class => [
            SendPaymentStatusNotification::class,
        ],
        \App\Events\OfferCreatedForCartItems::class => [
            \App\Listeners\SendOfferCreatedForCartItemsNotification::class,
        ],
        \App\Events\OfferCreatedForFavorites::class => [
            \App\Listeners\SendOfferCreatedForFavoritesNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        \App\Models\Property::observe(\App\Observers\PropertyObserver::class);
        \App\Models\Property::observe(\App\Observers\FavoritePropertyObserver::class);
        \App\Models\Order::observe(\App\Observers\OrderObserver::class);
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
