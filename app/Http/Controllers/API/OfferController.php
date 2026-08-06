<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\{Offer, Product};
use Illuminate\Support\Facades\Schema;

class OfferController extends Controller
{
    public function index()
    {
        $priceMode = app('price_mode') ?? 'wholesale';
        $priceColumn = 'price';

        $targetOfferCol = $priceMode === 'wholesale' ? 'discount_wholesale' : 'discount_retail';
        $offerColumn = Schema::hasColumn('offers', $targetOfferCol) 
            ? $targetOfferCol 
            : (Schema::hasColumn('offers', 'discount_price') ? 'discount_price' : 'discount_retail');

        $page = request('page', 1);
        $now = now();
        $locale = app()->getLocale();

        $products = Product::with([
            'brand:id,name_ar,name_en',
            'variants' => function ($q) use ($priceColumn, $offerColumn, $now) {
                $q
                    ->whereNotNull($priceColumn)
                    ->where($priceColumn, '>', 0)
                    ->with(['offers' => function ($oq) use ($offerColumn, $now) {
                        $oq
                            ->where('start', '<=', $now)
                            ->where('end', '>=', $now);
                    }])
                    ->whereHas('offers', function ($oq) use ($now) {
                        $oq
                            ->where('start', '<=', $now)
                            ->where('end', '>=', $now);
                    });
            }
        ])
            ->whereHas('variants.offers', function ($oq) use ($now) {
                $oq
                    ->where('start', '<=', $now)
                    ->where('end', '>=', $now);
            })
            ->orderByDesc('id')
            ->paginate(20, ['*'], 'page', $page);

        if ($products->isEmpty()) {
            $emptyData = new \stdClass();
            $emptyData->data = [];

            return response()->json([
                'success' => true,
                'message' => __('No active offers found'),
                'data' => $emptyData,
            ], 200);
        }

        $products->getCollection()->transform(function ($product) use ($locale) {
            $variant = $product->variants->first();

            return [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->desc,
                'price' => optional($variant)->price,
                'image_path' => optional($variant)->image_path,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => __('Offers retrieved successfully'),
            'data' => $products,
        ]);
    }
}
