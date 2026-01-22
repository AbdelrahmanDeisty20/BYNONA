<?php

namespace App\Providers;

use App\Models\{Offer, Property};
use App\Observers\{OfferObserver, PropertyObserver, FavoritePropertyObserver};
use Illuminate\Support\Facades\{App, DB};
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        DB::listen(function ($query) {
            logger()->info('SQL', [
                'sql' => $query->sql,
                'bindings' => $query->bindings,
                'time' => $query->time,
            ]);
        });

        $locale = explode(',', request()->header('Accept-Language', config('app.locale')))[0];
        $locale = explode('-', $locale)[0];

        App::setLocale($locale);
        app()->setLocale(session('lang', default: config('app.locale')));
    }
}
