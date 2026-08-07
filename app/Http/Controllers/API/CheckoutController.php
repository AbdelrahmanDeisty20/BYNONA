<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\CheckoutRequest;
use App\Models\{Cart, Order, Payment, Setting, Property};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB};
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function checkout(CheckoutRequest $request)
    {
        $data = $request->validated();
        $user = Auth::user();
        $priceMode = app('price_mode');  // retail | wholesale

        // جلب السلة النشطة + المنتجات والمواصفات والعروض
        $cart = Cart::with(['items' => function ($q) use ($priceMode) {
            $q->withTrashed()->where('sale_type', $priceMode)->with(['offers', 'product', 'variantAttributes']);
        }])
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return response()->json([
                'message' => 'Cart is empty',
                'data' => []
            ], 200);
        }

        // تحقق مبكر من المنتجات التي نفد مخزونها (0) أو المحذوفة
        foreach ($cart->items as $item) {
            if ($item->stock <= 0 || $item->trashed()) {
                $attributes = $item->variantAttributes->map(fn($attr) => $attr->value)->implode(' - ');
                return response()->json([
                    'success' => true,
                    'message' => __('The product :name (:attributes) is out of stock (0)', [
                        'name' => $item->product->name,
                        'attributes' => $attributes
                    ]),
                ], 200);
            }
        }

        $settings = Setting::first();
        $taxRate = $settings->tax ?? 0;
        $shipping = $settings->shipping ?? 0;

        $subtotal = 0;
        $productsToAdd = [];
        $now = now();

        foreach ($cart->items as $item) {
            // السعر الأساسي
            $originalPrice = $item->price ?? ($priceMode === 'wholesale'
                ? ($item->wholesale_price ?? $item->retail_price ?? 0)
                : ($item->retail_price ?? $item->wholesale_price ?? 0));

            // حساب السعر بعد العرض
            $discountedPrice = $originalPrice;
            $offer = $item->offers->where('start', '<=', $now)->where('end', '>=', $now)->first();

            if ($offer) {
                $discountPercent = 0;
                if ($priceMode === 'wholesale' && $offer->discount_wholesale > 0) {
                    $discountPercent = $offer->discount_wholesale;
                } elseif ($priceMode === 'retail' && $offer->discount_retail > 0) {
                    $discountPercent = $offer->discount_retail;
                } elseif ($offer->discount && $offer->discount > 0) {
                    $discountPercent = $offer->discount;
                }
                $discountedPrice = round($originalPrice * (1 - $discountPercent / 100), 2);
            }

            // Check stock
            if ($item->stock < $item->pivot->quantity) {
                $attributes = $item->variantAttributes->map(fn($attr) => $attr->value)->implode(' - ');
                return response()->json([
                    'success' => true,
                    'message' => __('The product :name (:attributes) is out of stock', ['name' => $item->product->name, 'attributes' => $attributes]),
                ], 200);
            }

            if ($priceMode === 'wholesale' && $item->pivot->quantity < $item->min_quantity) {
                $attributes = $item->variantAttributes->map(fn($attr) => $attr->value)->implode(' - ');
                return response()->json([
                    'success' => true,
                    'message' => __('The product :name (:attributes) does not meet the minimum quantity for wholesale', ['name' => $item->product->name, 'attributes' => $attributes]),
                ], 200);
            }

            $productsToAdd[] = [
                'id' => $item->id,
                'quantity' => $item->pivot->quantity,
                'price' => $discountedPrice
            ];
            $subtotal += $discountedPrice * $item->pivot->quantity;
        }

        if (empty($productsToAdd)) {
            return response()->json([
                'message' => 'No products meet the requirements for this sale type.'
            ], 200);
        }

        $taxAmount = ($taxRate / 100) * $subtotal;
        $total = $subtotal + $taxAmount + $shipping;

        DB::beginTransaction();

        try {
            $order = Order::create([
                'order_number' => 'ORD-' . now()->format('YmdHis') . '-' . strtoupper(Str::random(4)),
                'user_id' => $user->id,
                'user_name' => $data['user_name'],
                'user_phone' => $data['user_phone'],
                'user_address' => $data['user_address'],
                'subtotal' => $subtotal,
                'total_price' => $total,
                'status' => 'pending',
                'governorate_id' => $data['governorate_id']
            ]);

            foreach ($productsToAdd as $product) {
                $order->items()->syncWithoutDetaching([
                    $product['id'] => [
                        'quantity' => $product['quantity'],
                        'price' => $product['price'],
                        'sale_type' => $priceMode,
                    ]
                ]);

                Property::withTrashed()->find($product['id'])->decrement('stock', $product['quantity']);
            }

            // حذف المنتجات من السلة حسب نوع البيع
            DB::table('cart_items')
                ->where('cart_id', $cart->id)
                ->where('sale_type', $priceMode)
                ->delete();

            DB::commit();

            return response()->json([
                'message' => 'Order created successfully',
                'order' => $order->load('items'),
            ], 200);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => $e->getMessage(),
            ], 200);
        }
    }
}
