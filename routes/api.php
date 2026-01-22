<?php

use App\Http\Controllers\API\{AddressController, AuthController, CartController, CategoryController, GovernorateController, CheckoutController, ForgetPasswordController, HomeController, NotificationController, OfferController, OrderController, PaymentController, ProductController, ProductFilterController, ReviewController, ContactController};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
 * |--------------------------------------------------------------------------
 * | API Routes
 * |--------------------------------------------------------------------------
 * |
 * | Here is where you can register API routes for your application. These
 * | routes are loaded by the RouteServiceProvider and all of them will
 * | be assigned to the "api" middleware group. Make something great!
 * |
 */

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('products-all', [ProductController::class, 'products']);

Route::group(['prefix' => 'v1', 'middleware' => ['lang']], function () {
    Route::post('type', [HomeController::class, 'shopType']);
    Route::post('register', [AuthController::class, 'register'])->middleware('throttle:20,1');
    Route::post('login', [AuthController::class, 'login'])->middleware(['throttle:20,1', 'EmailVerfived']);
    Route::post('/refresh', [AuthController::class, 'refresh'])->middleware('throttle:10,1');
    Route::post('/verify-otp', [AuthController::class, 'verfivedCode'])->middleware('throttle:10,1');
    Route::post('/resend-otp', [AuthController::class, 'resendCode'])->middleware('throttle:10,1');
    Route::post('/social-login/{provider}', [AuthController::class, 'socialGoogle'])->middleware('throttle:20,1');
    Route::post('/social-register/{provider}', [AuthController::class, 'socialGoogle'])->middleware('throttle:20,1');
    Route::get('categories', [CategoryController::class, 'index']);
    Route::get('categories/{id}', [CategoryController::class, 'subCategory']);
    Route::get('categories/{id}/Tree', [CategoryController::class, 'subTree']);
    Route::get('brands', [HomeController::class, 'brand']);
    Route::get('banners-product', [ProductController::class, 'ProuctBanner']);
    Route::post('sendEmail', [ForgetPasswordController::class, 'sendEmail']);
    Route::post('sendCode', [ForgetPasswordController::class, 'sendCode']);
    Route::post('forgotPassword/resend-otp', [ForgetPasswordController::class, 'resendCode'])->middleware('throttle:10,1');
    Route::post('password', [ForgetPasswordController::class, 'password']);
    Route::get('test-notification', [HomeController::class, 'testNotification']);
    Route::get('governorates', [GovernorateController::class, 'index']);
    Route::get('contacts', [ContactController::class, 'index']);

    Route::get('shippings', [HomeController::class, 'shipping']);
    // routes/api.php
    Route::post('fcm-token', [HomeController::class, 'saveFcmToken']);

    Route::get('offers', [OfferController::class, 'index'])->middleware('price_mode');
    Route::get('banners', [HomeController::class, 'banners']);
    Route::group(['middleware' => ['price_mode']], function () {
        Route::get('products', [ProductController::class, 'index']);
        Route::get('latset-product', [ProductController::class, 'latsetProduct']);
        Route::get('product', [ProductController::class, 'show']);
        Route::get('attributes', [HomeController::class, 'attributes']);
        Route::get('filter/product', [ProductFilterController::class, 'filter']);

        Route::get('/search', [HomeController::class, 'search']);

        Route::get('reviews', [ReviewController::class, 'index']);
    });
    Route::group(['middleware' => ['auth:sanctum', 'EmailVerfived']], function () {
        Route::post('send-to-user', [HomeController::class, 'sendToUser']);
        Route::post('test-specific-token', [HomeController::class, 'testSpecificToken']);
        Route::post('send-to-all', [HomeController::class, 'sendToAll']);
        Route::post('fcm-token-user', [HomeController::class, 'saveFcmToken']);

        Route::post('update-profile', [AuthController::class, 'updateProfile']);
        Route::apiResource('carts', CartController::class)->middleware('price_mode');
        Route::get('favorites', [ProductController::class, 'favorites'])->middleware('price_mode');
        Route::post('add-product', [cartController::class, 'create']);
        // Route::post('reviews', [ReviewController::class, 'index']);
        Route::post('reviews/create', [ReviewController::class, 'store'])->middleware('price_mode');
        Route::post('checkouts', [CheckoutController::class, 'checkout'])->middleware('price_mode');
        Route::post('addFavorite', [ProductController::class, 'addFavorite'])->middleware('price_mode');
        Route::post('removeFavorite', [ProductController::class, 'removeFavorite'])->middleware('price_mode');
        Route::post('orders/{order}/pay', [PaymentController::class, 'createPayment']);
        Route::get('orders', [OrderController::class, 'index']);
        Route::get('order', [OrderController::class, 'show']);
        Route::get('address', [AddressController::class, 'index']);
        Route::post('address/create', [AddressController::class, 'store']);
        Route::put('address/update', [AddressController::class, 'update']);
        Route::delete('address/delete/{id}', [AddressController::class, 'destroy']);
        Route::get('notifications', [NotificationController::class, 'index']);
        Route::get('notifications/orders', [NotificationController::class, 'OrderNoti']);
        Route::post('logout', [AuthController::class, 'logout']);
    });
});

Route::group(['prefix' => 'v2', 'middleware' => 'lang'], function () {
    Route::post('sendEmail', [ForgetPasswordController::class, 'sendEmail']);
    Route::post('sendCode', [ForgetPasswordController::class, 'sendCode']);
    Route::post('forgotPassword/resend-otp', [ForgetPasswordController::class, 'resendCode'])->middleware('throttle:10,1');
    Route::post('password', [ForgetPasswordController::class, 'password']);
});
