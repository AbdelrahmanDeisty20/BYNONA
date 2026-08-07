<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\createRequest;
use App\Models\{Cart, Offer, Product, Property};
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\{Auth, DB};

class CartController extends Controller
{
    // عرض السلة النشطة للمستخدم
    public function index()
    {
        $user = Auth::user();
        $priceMode = app('price_mode');  // retail | wholesale
        $now = now();
        $locale = app()->getLocale();

        $cart = Cart::with(['items' => function ($query) use ($priceMode, $now) {
            $query
                ->wherePivot('sale_type', $priceMode)
                ->withTrashed()
                ->with([
                    'product',  // علاقة المنتج
                    'variantAttributes',
                    'offers' => function ($q) use ($priceMode, $now) {
                        $offerColumn = $priceMode === 'wholesale' ? 'discount_wholesale' : 'discount_retail';
                        // Assuming offers are linked to product_id usually.
                        // If property has offers, verify relationship name.
                        // Using 'product_id' here as safe bet or based on user's code provided earlier.
                        $q
                            ->whereNotNull($offerColumn)
                            ->where($offerColumn, '>', 0)
                            ->where('start', '<=', $now)
                            ->where('end', '>=', $now);
                    }
                ]);
        }])
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->first();

        // تنظيف السلة تلقائياً: حذف المنتجات التي تم حذفها (Soft Delete) نهائياً من السلة عند التحديث
        if ($cart && $cart->items->isNotEmpty()) {
            $trashedItems = $cart->items->filter(fn($item) => $item->trashed());

            if ($trashedItems->isNotEmpty()) {
                $cart->items()->wherePivot('sale_type', $priceMode)->detach($trashedItems->pluck('id'));
                // تحديث القائمة في الذاكرة لتجاهل العناصر المحذوفة حتى لا تظهر في الاستجابة الحالية
                $cart->setRelation('items', $cart->items->diff($trashedItems));
            }
        }

        if (!$cart || $cart->items->isEmpty()) {
            return response()->json([
                'success' => true,
                'message' => 'No active cart with products found',
                'data' => []
            ]);
        }

        // ===== حساب الستوك لكل property =====
        $propertyIds = $cart->items->pluck('id');
        $stocks = DB::table('properties')
            ->select('id', 'stock')
            ->whereIn('id', $propertyIds)
            ->pluck('stock', 'id');

        $total = 0;

        // تحويل العناصر للشكل المطلوب يدوياً
        $transformedItems = $cart->items->map(function ($item) use ($stocks, $priceMode, $locale) {
            // السعر الأصلي
            $originalPrice = $item->price ?? ($priceMode === 'wholesale' ? ($item->wholesale_price ?? $item->retail_price ?? 0) : ($item->retail_price ?? $item->wholesale_price ?? 0));

            // حساب سعر العرض
            $unitPriceWithOffer = $originalPrice;
            $offer = $item->offers->first();

            if ($offer) {
                $discountPercent = 0;
                if ($priceMode === 'wholesale' && $offer->discount_wholesale > 0) {
                    $discountPercent = $offer->discount_wholesale;
                } elseif ($priceMode === 'retail' && $offer->discount_retail > 0) {
                    $discountPercent = $offer->discount_retail;
                } elseif ($offer->discount && $offer->discount > 0) {
                    $discountPercent = $offer->discount;
                }
                $unitPriceWithOffer = round($originalPrice * (1 - $discountPercent / 100), 2);
            }

            // تجهيز Attributes
            $attributes = $item->variantAttributes->mapWithKeys(function ($attr) use ($locale) {
                return [
                    ($locale === 'ar' ? $attr->key_ar : $attr->key_en) => ($locale === 'ar' ? $attr->value_ar : $attr->value_en)
                ];
            });

            return [
                'id' => $item->id,  // Property ID
                'product_id' => $item->product_id,
                'name' => $locale === 'ar' ? $item->product->name_ar : $item->product->name_en,
                'desc' => $locale === 'ar' ? $item->product->desc_ar : $item->product->desc_en,
                'min_quantity' => $priceMode === 'retail' ? 1 : $item->min_quantity,
                'stock' => (int) ($stocks[$item->id] ?? 0),
                'quantity' => $item->pivot->quantity,
                'line_total' => $unitPriceWithOffer * $item->pivot->quantity,
                'price' => (string) $originalPrice,
                'image_path' => $item->image_path,
                'attributes' => $attributes,
                'offers' => $item->offers->map(function ($o) {
                    return [
                        'id' => $o->id,
                        'disscount_price' => $o->disscount_price
                    ];
                }),
            ];
        });

        // يمكن حساب المجموع هنا للتأكيد، أو الاعتماد على الحساب السابق
        $cart->total = $transformedItems->sum('line_total');
        $cart->save();

        return response()->json([
            'success' => true,
            'cart' => [
                'id' => $cart->id,
                'user_id' => $cart->user_id,
                'total' => $cart->total,
                'items' => $transformedItems,
            ],
        ]);
    }

    // إضافة أو تحديث منتج في السلة
    public function store(createRequest $request)
    {
        $data = $request->validated();
        $user = Auth::user();
        $type = app('price_mode');  // retail | wholesale

        $property = Property::findOrFail($data['property_id']);

        // جلب الستوك الحالي للـ property
        $totalStock = $property->stock;

        // تحقق من الحد الأدنى للجملة
        if ($type === 'wholesale' && $data['quantity'] < $property->min_quantity) {
            return response()->json([
                'success' => true,
                'message' => "Minimum quantity for wholesale is {$property->min_quantity}",
            ], 200);
        }

        if ($totalStock < $data['quantity']) {
            return response()->json([
                'success' => true,
                'message' => 'Not enough stock',
            ], 200);
        }

        $unitPrice = $property->price ?? ($type === 'wholesale' ? ($property->wholesale_price ?? $property->retail_price ?? 0) : ($property->retail_price ?? $property->wholesale_price ?? 0));

        $offer = Offer::where('property_id', $property->id)
            ->where('start', '<=', Carbon::now())
            ->where('end', '>=', Carbon::now())
            ->first();

        if ($offer) {
            if ($type === 'wholesale' && $offer->wholesale_price > 0) {
                $unitPrice = $offer->wholesale_price;
            } elseif ($type === 'retail' && $offer->retail_price > 0) {
                $unitPrice = $offer->retail_price;
            } elseif ($offer->discount && $offer->discount > 0) {
                $unitPrice = round($unitPrice * (1 - $offer->discount / 100), 2);
            }
        }

        $lineTotal = $unitPrice * $data['quantity'];

        DB::transaction(function () use ($user, $property, $data, $unitPrice, $lineTotal, $type) {
            $cart = Cart::firstOrCreate(
                ['user_id' => $user->id, 'status' => 'active'],
                ['total' => 0]
            );

            $existing = $cart
                ->items()
                ->where('property_id', $property->id)
                ->wherePivot('sale_type', $type)
                ->first();

            if ($existing) {
                $newQty = $existing->pivot->quantity + $data['quantity'];
                $cart->items()->updateExistingPivot($property->id, [
                    'quantity' => $newQty,
                    'unit_price' => $unitPrice,
                    'line_total' => $newQty * $unitPrice,
                    'sale_type' => $type,
                ]);
            } else {
                $cart->items()->attach($property->id, [
                    'quantity' => $data['quantity'],
                    'unit_price' => $unitPrice,
                    'line_total' => $lineTotal,
                    'sale_type' => $type,
                ]);
            }

            $cart->load('items');
            $cart->total = $cart->items->sum(fn($i) => $i->pivot->line_total);
            $cart->save();
        });

        return response()->json([
            'success' => true,
            'message' => 'Property added to cart',
        ], 200);
    }

    // تحديث كمية المنتج

    public function update(Request $request, $propertyId)
    {
        $data = $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $user = Auth::user();
        $type = app('price_mode');  // retail | wholesale

        DB::transaction(function () use ($user, $propertyId, $data, $type) {
            $cart = Cart::where('user_id', $user->id)
                ->where('status', 'active')
                ->firstOrFail();

            // ===== property / variant =====
            $property = Property::withTrashed()->with('product')->findOrFail($propertyId);

            // موجود في السلة؟
            $cartItem = $cart
                ->items()
                ->where('properties.id', $propertyId)
                ->wherePivot('sale_type', $type)
                ->first();

            if (!$cartItem) {
                abort(404, 'Item not in cart');
            }

            // تحقق من الستوك
            if ($property->stock < $data['quantity']) {
                abort(422, 'Not enough stock');
            }

            // تحقق من الحد الأدنى للجملة
            if ($type === 'wholesale' && $data['quantity'] < $property->min_quantity) {
                abort(422, "Minimum quantity is {$property->min_quantity}");
            }

            // السعر من property
            $unitPrice = $property->price ?? ($type === 'wholesale'
                ? ($property->wholesale_price ?? $property->retail_price ?? 0)
                : ($property->retail_price ?? $property->wholesale_price ?? 0));

            // العروض
            $offer = Offer::where('property_id', $property->id)
                ->where('start', '<=', now())
                ->where('end', '>=', now())
                ->first();

            if ($offer) {
                if ($type === 'wholesale' && $offer->wholesale_price > 0) {
                    $unitPrice = $offer->wholesale_price;
                } elseif ($type === 'retail' && $offer->retail_price > 0) {
                    $unitPrice = $offer->retail_price;
                } elseif ($offer->discount && $offer->discount > 0) {
                    $unitPrice = round($unitPrice * (1 - $offer->discount / 100), 2);
                }
            }

            $lineTotal = $unitPrice * $data['quantity'];

            // تحديث pivot
            $cart
                ->items()
                ->wherePivot('sale_type', $type)
                ->updateExistingPivot($property->id, [
                    'quantity' => $data['quantity'],
                    'unit_price' => $unitPrice,
                    'line_total' => $lineTotal,
                    'sale_type' => $type,
                ]);

            // تحديث إجمالي السلة
            $cart->load('items');
            $cart->total = $cart->items->sum(fn($i) => $i->pivot->line_total);
            $cart->save();
        });

        return response()->json([
            'success' => true,
            'message' => 'Cart item updated successfully',
        ]);
    }

    // حذف منتج من السلة
    public function destroy($propertyId)
    {
        $user = Auth::user();
        $type = app('price_mode');  // retail | wholesale

        $cart = Cart::where('user_id', $user->id)
            ->where('status', 'active')
            ->firstOrFail();

        $cartItem = $cart
            ->items()
            ->withTrashed()  // Check trashed items too
            ->where('properties.id', $propertyId)
            ->wherePivot('sale_type', $type)
            ->first();

        if (!$cartItem) {
            return response()->json([
                'success' => true,
                'message' => 'Item not found in cart',
            ]);
        }

        // حذف من السلة
        $cart
            ->items()
            ->wherePivot('sale_type', $type)
            ->detach($propertyId);

        // تحديث إجمالي السلة
        $cart->load('items');
        $cart->total = $cart->items->sum(fn($i) => $i->pivot->line_total);
        $cart->save();

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart',
        ]);
    }
}
