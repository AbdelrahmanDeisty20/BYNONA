<?php

use App\Http\Controllers\Dashboard\{AddressController, AuthController, BannerController, BrandController, CategoryController, FavoriteController, HomeController, NotificationsController, OfferController, OrderController, PaymentController, ProductBannerController, ProductsController, ProductDetailsController, PropertyController, ReviewController, SettingController, UserController, AttributeDefinitionController, ContactController, ProductExcelController};
use Illuminate\Support\Facades\Route;

/*
 * |--------------------------------------------------------------------------
 * | Web Routes
 * |--------------------------------------------------------------------------
 * |
 * | Here is where you can register web routes for your application. These
 * | routes are loaded by the RouteServiceProvider within a group which
 * | contains the "web" middleware group. Now create something great!
 * |
 */

Route::get('/', function () {
    return view('welcome');
});

Route::group(['prefix' => 'admin', 'middleware' => 'lang'], function () {
    Route::get('login', [AuthController::class, 'loginForm'])->name('dashboard.login');
    Route::post('login/send', [AuthController::class, 'login'])->name('dashboard.loginSend');
    Route::group(['middleware' => ['authCheck', 'AdminCheck']], function () {
        Route::get('/', [HomeController::class, 'index'])->name('dashbord.dashboard');
        Route::get('user-profile/', [AuthController::class, 'userProfile'])->name('dashboard.userProfile');
        Route::put('user-profile/update', [AuthController::class, 'profileUpdate'])->name('dashboard.profileUpdate');
        Route::resource('categories', CategoryController::class);
        Route::get('sort/categories', [HomeController::class, 'sortCategory'])->name('dashboard.categories.sort');
        Route::post('sort/categories/update', [HomeController::class, 'updateSort'])->name('dashboard.categories.sort.update');
        Route::post('products/import', [ProductExcelController::class, 'import'])->name('products.import');
        Route::get('products/export', [ProductExcelController::class, 'export'])->name('products.export');
        Route::resource('products', ProductsController::class);
        // Route::get('attributes/list', [AttributeDefinitionController::class, 'list'])->name('attributes.list');
        Route::resource('attributes', AttributeDefinitionController::class);
        Route::get('products/{id}/properties', [PropertyController::class, 'index'])->name('dashboard.properties.index');
        Route::delete('properties/{id}', [PropertyController::class, 'destroy'])->name('dashboard.properties.destroy');
        Route::resource('favorites', FavoriteController::class);
        Route::resource('offers', OfferController::class);
        Route::resource('banners', BannerController::class);
        Route::resource('settings', SettingController::class);
        Route::resource('productBanners', ProductBannerController::class);
        Route::resource('contacts', ContactController::class)->only(['index', 'edit', 'update']);

        Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::get('payments/delete{id}', [PaymentController::class, 'index'])->name('payments.destroy');
        Route::get('reviews', [ReviewController::class, 'index'])->name('reviews.index');
        Route::delete('reviews/delete{id}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
        Route::resource('brands', BrandController::class);
        Route::resource('users', UserController::class);
        Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{id}', [OrderController::class, 'show'])->name('orders.show');
        Route::get('addresses', [AddressController::class, 'index'])->name('addresses.index');
        Route::put('orders/{id}/cancel', [OrderController::class, 'cancelOrder'])->name('orders.cancelOrder');
        Route::put('orders/{id}/complete', [OrderController::class, 'compeleteOrder'])->name('orders.completeOrder');
        Route::put('orders/{id}/processing', [OrderController::class, 'processOrder'])->name('orders.processingOrder');
        Route::delete('orders/{id}/delete', [OrderController::class, 'destroy'])->name('orders.destroy');
        Route::get('/charts/orders', [HomeController::class, 'ordersChart']);
        Route::get('/charts/revenue', [HomeController::class, 'revenueChart']);
        Route::get('notifications', [NotificationsController::class, 'index'])->name('dashboard.notifications.index');
        Route::get('notifications/fetch', [NotificationsController::class, 'fetch'])->name('dashboard.notifications.fetch');
        Route::post('notifications/{id}/read', [NotificationsController::class, 'markAsRead'])->name('dashboard.notifications.markAsRead');
        Route::post('notifications/read-all', [NotificationsController::class, 'markAllAsRead'])->name('dashboard.notifications.markAllAsRead');
        Route::get('logout', [AuthController::class, 'logout'])->name('dashboard.logout');
    });
    Route::get('lang/{lang}', [HomeController::class, 'switchLang'])->name('dashboard.lang.switch');
});
