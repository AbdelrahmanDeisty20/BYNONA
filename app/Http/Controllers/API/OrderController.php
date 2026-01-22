<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\OrderRequest;
use App\Models\{Order, Setting};

class OrderController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $orders = Order::where("user_id", $user->id)
            ->with([
                'items' => function ($query) {
                    $query
                        ->join('products', 'products.id', '=', 'properties.product_id')
                        ->select(
                            'properties.*',
                            'products.name_ar',
                            'products.name_en'
                        );
                }
            ])
            ->select(["id", "order_number", "created_at", "status"])
            ->get();

        $data = $orders->map(function ($order) {
            $saleTypes = $order->items->map(fn ($item) => $item->pivot->sale_type);

            return [
                'id'           => $order->id,
                'order_number' => $order->order_number,
                'created_at'   => $order->created_at,
                'status'       => $order->status,
                'sale_type'    => $saleTypes->unique()->implode(', ')
            ];
        });

        return response()->json([
            "success" => true,
            "data"    => $data,
        ]);
    }

    public function show(OrderRequest $request)
{
    $user = Auth::user();
    $data = $request->validated();

    $order = Order::where('user_id', $user->id)
        ->with([
            'items' => function ($query) {
                $query
                    ->with('variantAttributes') // 👈 نفس نظام show المنتج
                    ->join('products', 'products.id', '=', 'properties.product_id')
                    ->select(
                        'properties.*',
                        'products.name_ar',
                        'products.name_en'
                    );
            }
        ])
        ->select([
            'id',
            'order_number',
            'user_name',
            'user_phone',
            'user_address',
            "governorate_id",
            'total_price',
            'status',
            'created_at'
        ])
        ->find($data['order_id']);

    if (!$order) {
        return response()->json([
            'success' => true,
            'message' => 'there is no order with this id'
        ], 200);
    }

    $items = $order->items->map(function ($item) {

        // ✅ attributes زي show المنتج
        $attributes = $item->variantAttributes
            ->mapWithKeys(fn ($attr) => [
                app()->getLocale() === 'ar'
                    ? $attr->key_ar
                    : $attr->key_en
                =>
                app()->getLocale() === 'ar'
                    ? $attr->value_ar
                    : $attr->value_en
            ]);

        return [
            'product_name' => app()->getLocale() === 'ar'
                ? $item->name_ar
                : $item->name_en,

            'quantity'   => $item->pivot->quantity,
            'price'      => $item->pivot->price,
            'total'      => $item->pivot->quantity * $item->pivot->price,
            'image'      => $item->image_path, // من properties
            'attributes' => $attributes,        // 👈 المطلوب
        ];
    });

    $shipping = Setting::where('governorate_id', $order->governorate_id)
    ->value('shipping'); // 👈 هيرجع الرقم مباشرة


    return response()->json([
        'success' => true,
        'data' => [
            'order' => [
                'order_number' => $order->order_number,
                'user_name'    => $order->user_name,
                'user_phone'   => $order->user_phone,
                'user_address' => $order->user_address,
                'status'       => $order->status,
                'total_price'  => $order->total_price,
                'shipping'     => $shipping,
                'created_at'   => $order->created_at,
            ],
            'product' => $items
        ]
    ], 200);
}


}
