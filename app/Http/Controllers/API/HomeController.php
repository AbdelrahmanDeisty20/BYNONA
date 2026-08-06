<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\shopTypeRequest;
use App\Models\{Banner, Brand, Product, Setting, User, UserFcmToken, Attribute};
use App\Service\FirebaseNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function banners()
    {
        $banners = Banner::paginate(10);
        if ($banners->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => __('there is no benner right now'),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => __('banner recived successfully'),
            'data' => $banners,
        ]);
    }

    public function sendToAll(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'body' => 'required|string',
        ]);

        $service = app(FirebaseNotificationService::class);

        // كل التوكنات المرتبطة بمستخدمين
        $tokens = UserFcmToken::whereNotNull('user_id')
            ->pluck('fcm_token')
            ->unique();

        if ($tokens->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No FCM tokens found'
            ]);
        }

        // الإرسال
        foreach ($tokens as $token) {
            try {
                $service->sendToToken(
                    $token,
                    $request->title,
                    $request->body
                );
            } catch (\Throwable $e) {
                // تجاهل
            }
        }

        // ✅ حفظ إشعار عام بدون user_id
        \App\Models\Notification::create([
            'user_id' => null,
            'title' => $request->title,
            'body' => $request->body,
            'type' => 'broadcast',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Broadcast notification sent successfully'
        ]);
    }

    public function shopType(ShopTypeRequest $request)
    {
        $type = $request->shop_type;
        Cache::flush();
        // غير مسجل دخول: نرسل بس القيمة، بدون session
        return response()->json([
            'status' => true,
            'message' => 'تم تحديد نوع التسوق بنجاح .',
            'shop_type' => $type,
        ]);
    }

    public function brand()
    {
        $brands = Brand::all();
        if ($brands->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'no brands right now .',
            ]);
        }
        return response()->json([
            'status' => true,
            'message' => 'brands recives successfully .',
            'data' => $brands,
        ]);
    }

    public function search(Request $request)
    {
        $q = trim($request->input('search'));
        $priceMode = app('price_mode');  // retail | wholesale
        $now = now();
        $priceColumn = 'price';
        $targetOfferCol = $priceMode === 'wholesale' ? 'discount_wholesale' : 'discount_retail';
        $offerColumn = \Illuminate\Support\Facades\Schema::hasColumn('offers', $targetOfferCol) 
            ? $targetOfferCol 
            : (\Illuminate\Support\Facades\Schema::hasColumn('offers', 'discount_price') ? 'discount_price' : 'discount_retail');
        $page = request('page', 1);

        if (!$q) {
            return response()->json([
                'status' => true,
                'message' => 'Please enter search text',
                'data' => (object) []
            ], 200);
        }

        $products = Product::with([
            'brand',
            'variants' => function ($v) use ($priceColumn) {
                $v
                    ->whereNotNull($priceColumn)
                    ->where($priceColumn, '>', 0);
            },
            'variants.variantAttributes',
            'variants.offers' => function ($o) use ($now, $offerColumn) {
                $o
                    ->whereNotNull($offerColumn)
                    ->where($offerColumn, '>', 0)
                    ->where('start', '<=', $now)
                    ->where('end', '>=', $now);
            }
        ])
            ->where(function ($query) use ($q) {
                $query
                    ->where(function ($sq) use ($q) {
                        $sq
                            ->where('products.name_ar', 'LIKE', "%$q%")
                            ->orWhere('products.name_en', 'LIKE', "%$q%")
                            ->orWhere('products.desc_ar', 'LIKE', "%$q%")
                            ->orWhere('products.desc_en', 'LIKE', "%$q%");
                    })
                    ->orWhereHas('brand', function ($brand) use ($q) {
                        $brand
                            ->where('brands.name_ar', 'LIKE', "%$q%")
                            ->orWhere('brands.name_en', 'LIKE', "%$q%");
                    })
                    ->orWhereHas('categories', function ($cat) use ($q) {
                        $cat
                            ->where('name_ar', 'LIKE', "%$q%")
                            ->orWhere('name_en', 'LIKE', "%$q%");
                    })
                    ->orWhereHas('variants.variantAttributes', function ($attr) use ($q) {
                        $attr
                            ->where('key_ar', 'LIKE', "%$q%")
                            ->orWhere('key_en', 'LIKE', "%$q%")
                            ->orWhere('value_ar', 'LIKE', "%$q%")
                            ->orWhere('value_en', 'LIKE', "%$q%");
                    });
            })
            ->whereHas('variants', function ($v) use ($priceColumn) {
                $v
                    ->whereNotNull($priceColumn)
                    ->where($priceColumn, '>', 0);
            })
            ->orderByDesc('id')
            ->paginate(10, ['*'], 'page', $page);

        // تحويل النتائج مثل index
        $products->getCollection()->transform(function ($product) use (
            $priceColumn,
            $offerColumn,
            $locale,
            $now
        ) {
            // 👈 نفس منطق filter
            $variant = $product->variants->first(function ($v) use ($offerColumn, $now) {
                return $v->offers->filter(function ($offer) use ($offerColumn, $now) {
                    return $offer->{$offerColumn} > 0 &&
                        $offer->start <= $now &&
                        $offer->end >= $now;
                })->isNotEmpty();
            }) ?? $product->variants->first();

            return [
                'id' => $product->id,
                'name' => $locale === 'ar' ? $product->name_ar : $product->name_en,
                'description' => $locale === 'ar' ? $product->desc_ar : $product->desc_en,
                'price' => optional($variant)->{$priceColumn},
                'image_path' => optional($variant)->image_path,
                // 👇 نفس شكل العروض في الفلترة
                'offers' => $variant
                    ? $variant->offers->map(function ($offer) use ($offerColumn) {
                        return [
                            'id' => $offer->id,
                            'disscount_price' => $offer->{$offerColumn},
                        ];
                    })
                    : [],
            ];
        });

        return response()->json([
            'success' => true,
            'message' => __('Products retrieved successfully'),
            'data' => $products
        ]);
    }

    public function shipping()
    {
        // جلب شحن + governorate_id
        $shipping = Setting::select('shipping', 'governorate_id')->get();

        if ($shipping->isEmpty()) {
            return response()->json([
                'success' => true,
                'message' => 'there is no shipping right now',
                'data' => []
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $shipping
        ]);
    }

    public function sendToUser(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'body' => 'required|string',
        ]);

        $service = app(FirebaseNotificationService::class);

        // كل المستخدمين اللي عندهم توكن واحد على الأقل
        $users = User::whereHas('fcmTokens')->with('fcmTokens')->get();

        if ($users->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No users with FCM tokens found'
            ]);
        }

        foreach ($users as $user) {
            // 1️⃣ حفظ إشعار لكل مستخدم
            \App\Models\Notification::create([
                'user_id' => $user->id,
                'title' => $request->title,
                'body' => $request->body,
                'type' => 'registered_users',
            ]);

            // 2️⃣ الإرسال لكل توكن تابع للمستخدم
            foreach ($user->fcmTokens as $token) {
                try {
                    $service->sendToToken(
                        $token->fcm_token,
                        $request->title,
                        $request->body
                    );
                } catch (\Throwable $e) {
                    // تجاهل التوكنات البايظة
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Notification sent to all registered users'
        ]);
    }

    public function testSpecificToken(Request $request)
    {
        // Validate input
        $request->validate([
            'token' => 'nullable|string',  // اختياري لو هنبعت لتوكن مباشر
            'user_id' => 'nullable|integer',  // اختياري لو هنبعت لتوكن موجود في DB
            'title' => 'required|string',
            'body' => 'required|string',
        ]);

        $service = app(\App\Service\FirebaseNotificationService::class);

        try {
            // تحديد التوكن
            if ($request->token) {
                $token = $request->token;
            } elseif ($request->user_id) {
                $token = DB::table('user_fcm_tokens')
                    ->where('user_id', $request->user_id)
                    ->value('fcm_token');

                if (!$token) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No FCM token found for this user'
                    ], 404);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Either token or user_id is required'
                ], 422);
            }

            // إرسال الإشعار فقط بالـ title والـ body
            $service->sendToToken(
                $token,
                $request->title,
                $request->body
            );

            return response()->json([
                'success' => true,
                'message' => 'Notification sent successfully'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function saveFcmToken(Request $request)
    {
        try {
            $request->validate([
                'fcm_token' => 'required|string',
                'device_id' => 'nullable|string',
            ]);

            $token = UserFcmToken::updateOrCreate(
                ['fcm_token' => $request->fcm_token],
                [
                    'user_id' => auth()->id(),
                    'device_id' => $request->device_id,
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'FCM Token stored successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function attributes()
    {
        $attributes = Attribute::get()
            ->groupBy('key')  // أو key_en / key_ar حسب اللي انت مظبطه في الموديل
            ->map(function ($items, $key) {
                return [
                    'key' => $key,
                    'values' => $items->unique('value')->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'value' => $item->value
                        ];
                    })->values()
                ];
            })
            ->values();

        $brands = Brand::select('id', 'name_ar', 'name_en')->get()->map(function ($brand) {
            return [
                'id' => $brand->id,
                'name' => $brand->name
            ];
        });

        $priceColumn = 'price';

        $minPrice = \App\Models\Property::where($priceColumn, '>', 0)->min($priceColumn) ?? 0;
        $maxPrice = \App\Models\Property::where($priceColumn, '>', 0)->max($priceColumn) ?? 0;

        return response()->json([
            'success' => true,
            'message' => 'retrieved attributes successfully',
            'data' => [
                'attributes' => $attributes,
                'brands' => $brands,
                'min_price' => $minPrice,
                'max_price' => $maxPrice,
            ]
        ]);
    }
}
