<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\{favoriteRequest, removeFavoriteRequest};
use App\Models\{Favorite, Product, ProductBanner, Property};
use Illuminate\Support\Facades\{Auth, Cache, DB, Request};

class ProductController extends Controller
{
    public function index()
    {
        $priceMode = app('price_mode');  // retail | wholesale
        $locale = app()->getLocale();
        $page = request('page', 1);
        $now = now();
        $priceColumn = 'price';

        $products = Product::with([
            'variants' => function ($q) use ($priceColumn) {
                $q->whereNotNull($priceColumn)
                  ->where($priceColumn, '>', 0)
                  ->with('offers');
            }
        ])
            ->whereHas('variants', function ($q) use ($priceColumn) {
                $q->whereNotNull($priceColumn)
                  ->where($priceColumn, '>', 0);
            })
            ->orderByDesc('id')
            ->paginate(10);

        $products->getCollection()->transform(function ($product) use ($locale, $now) {
            $variant = $product->variants->first();

            $firstOffer = optional($variant)
                ->offers
                ->first(function ($offer) use ($now) {
                    $discountPrice = $offer->disscount_price ?? $offer->discount_price ?? 0;
                    return $discountPrice > 0 && $offer->start <= $now && $offer->end >= $now;
                });

            return [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->desc,
                'price' => optional($variant)->price,
                'image_path' => optional($variant)->image_path,
                'offers' => $firstOffer ? [
                    [
                        'id' => $firstOffer->id,
                        'disscount_price' => $firstOffer->disscount_price ?? $firstOffer->discount_price ?? 0,
                    ]
                ] : [],
            ];
        });

        return response()->json([
            'success' => true,
            'message' => __('Products retrieved successfully'),
            'data' => $products,
        ]);
    }

    public function show()
    {
        $id = request()->input('product_id');
        if (!$id) {
            return response()->json([
                'success' => false,
                'message' => __('product_id is required'),
            ]);
        }

        $priceMode = app('price_mode');
        $locale = app()->getLocale();
        $now = now();
        $priceColumn = 'price';

        $product = Product::with([
            'variants' => function ($q) use ($priceColumn, $now) {
                $q->withTrashed()
                  ->whereNotNull($priceColumn)
                  ->where($priceColumn, '>', 0)
                  ->with([
                      'variantAttributes',
                      'offers' => function ($oq) use ($now) {
                          $oq->where('start', '<=', $now)
                             ->where('end', '>=', $now);
                      }
                  ]);
            },
            'reviews.user',
            'brand'
        ])
            ->where('id', $id)
            ->whereHas('variants', function ($q) use ($priceColumn) {
                $q->withTrashed()
                  ->whereNotNull($priceColumn)
                  ->where($priceColumn, '>', 0);
            })
            ->first();

        if (!$product) {
            return response()->json([
                'success' => true,
                'message' => __('this product does not exist'),
                'data' => (object) []
            ]);
        }

        $stock = $product->variants->sum('stock');

        $variants = $product->variants->map(function ($variant) use ($locale) {
            $attributes = $variant
                ->variantAttributes
                ->mapWithKeys(fn($item) => [
                    ($locale === 'ar' ? $item->key_ar : $item->key_en) => ($locale === 'ar' ? $item->value_ar : $item->value_en)
                ]);

            return [
                'id' => $variant->id,
                'price' => $variant->price,
                'min_quantity' => $variant->min_quantity,
                'stock' => $variant->stock,
                'image_path' => $variant->image_path,
                'images_path' => $variant->images_path,
                'attributes' => $attributes,
                'offers' => $variant->offers->map(function ($offer) {
                    return [
                        'id' => $offer->id,
                        'disscount_price' => $offer->disscount_price ?? $offer->discount_price ?? 0,
                    ];
                }),
            ];
        });

        return response()->json([
            'success' => true,
            'message' => __('product received successfully'),
            'data' => [
                'id' => $product->id,
                'min_quantity' => $priceMode === 'retail' ? 0 : ($product->variants->min('min_quantity') ?? 1),
                'stock' => (int) $stock,
                'name' => $product->name,
                'desc' => $product->desc,
                'variants' => $variants,
                'brand' => $product->brand
                    ? ['name' => $product->brand->name, 'image_path' => $product->brand->image_path]
                    : new \stdClass(),
                'reviews' => $product->reviews->map(function ($review) {
                    return [
                        'id' => $review->id,
                        'comment' => $review->comment,
                        'rate' => $review->rate,
                        'created_at' => $review->created_at,
                        'first_name' => $review->user->first_name ?? '',
                        'last_name' => $review->user->last_name ?? ''
                    ];
                }),
            ]
        ]);
    }

    public function favorites()
    {
        $priceMode = app('price_mode');
        $perPage = 10;
        $priceColumn = 'price';

        $favorites = Favorite::where('user_id', auth()->id())
            ->where('is_favorite', true)
            ->where('type', $priceMode)
            ->whereHas('product.variants', function ($q) use ($priceColumn) {
                $q->whereNotNull($priceColumn)
                  ->where($priceColumn, '>', 0);
            })
            ->with(['product.variants' => function ($q) use ($priceColumn) {
                $q->whereNotNull($priceColumn)
                  ->where($priceColumn, '>', 0);
            }])
            ->orderByDesc('id')
            ->paginate($perPage);

        $favorites->getCollection()->transform(function ($favorite) {
            $product = $favorite->product;
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
            'message' => __('Products retrieved successfully'),
            'data' => $favorites,
        ]);
    }

    public function addFavorite(favoriteRequest $request)
    {
        $user = Auth::user();
        $data = $request->validated();
        $priceMode = app('price_mode');
        $priceColumn = 'price';

        $product = Product::where('id', $data['product_id'])
            ->whereHas('variants', function ($q) use ($priceColumn) {
                $q->whereNotNull($priceColumn)
                  ->where($priceColumn, '>', 0);
            })
            ->first();

        if (!$product) {
            return response()->json([
                'success' => true,
                'message' => __('This product is not available in current price mode.'),
            ]);
        }

        $favorite = Favorite::where('product_id', $product->id)
            ->where('user_id', $user->id)
            ->where('type', $priceMode)
            ->first();

        if ($favorite && $favorite->is_favorite) {
            return response()->json([
                'success' => true,
                'message' => __('This product is already in your favorites.'),
            ]);
        }

        if ($favorite && !$favorite->is_favorite) {
            $favorite->update(['is_favorite' => true]);

            return response()->json([
                'success' => true,
                'message' => __('Product added to favorites successfully.'),
                'data' => $favorite->makeHidden(['type']),
            ]);
        }

        $favorite = Favorite::create([
            'product_id' => $product->id,
            'user_id' => $user->id,
            'is_favorite' => true,
            'type' => $priceMode,
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Product added to favorites successfully.'),
            'data' => $favorite->makeHidden(['type']),
        ]);
    }

    public function removeFavorite(removeFavoriteRequest $request)
    {
        $user = Auth::user();
        $data = $request->validated();
        $priceMode = $data['price_mode'] ?? app('price_mode');

        $favorite = Favorite::where('user_id', $user->id)
            ->where('product_id', $data['product_id'])
            ->where('type', $priceMode)
            ->first();

        if (!$favorite) {
            return response()->json([
                'success' => true,
                'message' => __('This property is not in your favorites.'),
            ]);
        }

        if (!$favorite->is_favorite) {
            return response()->json([
                'success' => true,
                'message' => __('This property is already not in your favorites.'),
            ]);
        }

        $favorite->update([
            'is_favorite' => false
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Property removed from favorites successfully.'),
            'data' => $favorite->makeHidden(['type']),
        ]);
    }

    public function latsetProduct()
    {
        $page = request('page', 1);
        $locale = app()->getLocale();
        $now = now();
        $dateLimit = now()->subDays(30);
        $priceColumn = 'price';

        $products = Product::with([
            'variants' => function ($q) use ($priceColumn, $now) {
                $q->whereNotNull($priceColumn)
                  ->where($priceColumn, '>', 0)
                  ->with([
                      'offers' => function ($oq) use ($now) {
                          $oq->where('start', '<=', $now)
                             ->where('end', '>=', $now);
                      },
                      'variantAttributes'
                  ]);
            },
            'brand',
        ])
            ->where('created_at', '>=', $dateLimit)
            ->whereHas('variants', function ($q) use ($priceColumn) {
                $q->whereNotNull($priceColumn)->where($priceColumn, '>', 0);
            })
            ->orderByDesc('created_at')
            ->paginate(10);

        $products->getCollection()->transform(function ($product) use ($locale, $now, $priceColumn) {
            $variant = $product->variants->first(function ($v) use ($now) {
                return $v->offers->filter(function ($offer) use ($now) {
                    $discountPrice = $offer->disscount_price ?? $offer->discount_price ?? 0;
                    return $discountPrice > 0 && $offer->start <= $now && $offer->end >= $now;
                })->isNotEmpty();
            }) ?? $product->variants->first();

            $attributes = [];
            $seenValues = [];
            foreach ($product->variants as $v) {
                foreach ($v->variantAttributes as $attr) {
                    $key = $attr->key_en;
                    $value = $locale === 'ar' ? $attr->value_ar : $attr->value_en;

                    if (!isset($attributes[$key])) {
                        $attributes[$key] = [];
                        $seenValues[$key] = [];
                    }

                    $lowerValue = mb_strtolower($value);
                    if (!in_array($lowerValue, $seenValues[$key])) {
                        $attributes[$key][] = $value;
                        $seenValues[$key][] = $lowerValue;
                    }
                }
            }

            return [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->desc,
                'price' => optional($variant)->price,
                'image_path' => optional($variant)->image_path,
                'offers' => $variant
                    ? $variant->offers->map(function ($offer) {
                        return [
                            'id' => $offer->id,
                            'disscount_price' => $offer->disscount_price ?? $offer->discount_price ?? 0,
                        ];
                    })
                    : [],
                'brand' => $product->brand ? [
                    'id' => $product->brand->id,
                    'name' => $locale === 'ar' ? $product->brand->name_ar : $product->brand->name_en
                ] : (object) [],
                'attributes' => $attributes,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => __('Latest products retrieved successfully'),
            'data' => $products,
        ]);
    }

    public function ProuctBanner()
    {
        $banners = ProductBanner::paginate(5);
        if ($banners->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'no banners right now'
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'recived product Banner successfully',
            'banners' => $banners
        ]);
    }
}
