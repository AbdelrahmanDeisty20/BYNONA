<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\FilterRequest;
use App\Models\Product;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ProductFilterController extends Controller
{
    protected int $perPage = 20;

    private function getAllCategoryIds($categoryId, array &$visited = [])
    {
        if (in_array($categoryId, $visited)) {
            return [];
        }
        $visited[] = $categoryId;

        $categoryIds = [$categoryId];
        $children = \App\Models\Category::where('parent_id', $categoryId)
            ->where('id', '!=', $categoryId)
            ->pluck('id')
            ->toArray();

        foreach ($children as $childId) {
            if (!in_array($childId, $visited)) {
                $categoryIds = array_merge($categoryIds, $this->getAllCategoryIds($childId, $visited));
            }
        }

        return array_values(array_unique($categoryIds));
    }

    public function filter(FilterRequest $request)
    {
        $filters = $request->validated();

        $priceMode = app('price_mode') ?? 'wholesale';
        
        // التحقق الديناميكي من وجود حقول السعر والعروض لمنع خطأ Column not found على الهوست المباشر
        $targetPriceCol = $priceMode === 'wholesale' ? 'wholesale_price' : 'retail_price';
        $priceColumn = Schema::hasColumn('properties', $targetPriceCol) 
            ? $targetPriceCol 
            : (Schema::hasColumn('properties', 'price') ? 'price' : $targetPriceCol);

        $targetOfferCol = $priceMode === 'wholesale' ? 'discount_wholesale' : 'discount_retail';
        $offerColumn = Schema::hasColumn('offers', $targetOfferCol) 
            ? $targetOfferCol 
            : (Schema::hasColumn('offers', 'discount_price') ? 'discount_price' : (Schema::hasColumn('offers', 'disscount_price') ? 'disscount_price' : $targetOfferCol));

        $locale = app()->getLocale();
        $page = $filters['page'] ?? 1;
        $now = now();
        $perPage = 10;

        // ---- بدون كاش مع Eager Loading آمن ----
        $query = Product::with([
            'brand',
            'variants' => function ($q) use ($priceColumn, $offerColumn, $now) {
                $q->whereNotNull($priceColumn)
                  ->where($priceColumn, '>', 0)
                  ->with([
                      'variantAttributes',
                      'offers' => function ($oq) use ($offerColumn, $now) {
                          $oq->whereNotNull($offerColumn)
                             ->where($offerColumn, '>', 0)
                             ->where('start', '<=', $now)
                             ->whereDate('end', '>=', $now);
                      }
                  ]);
            }
        ])->whereHas('variants', function ($q) use ($priceColumn) {
            $q->whereNotNull($priceColumn)->where($priceColumn, '>', 0);
        });

        // فلترة الأقسام بشكل متداخل وآمن من الـ Infinite Loop
        if (!empty($filters['category_id'])) {
            $allCategoryIds = $this->getAllCategoryIds($filters['category_id']);
            $query->where(function ($q) use ($allCategoryIds) {
                if (Schema::hasColumn('products', 'category_id')) {
                    $q->whereIn('category_id', $allCategoryIds);
                }
                if (Schema::hasTable('category_product')) {
                    $q->orWhereHas('categories', function ($sq) use ($allCategoryIds) {
                        $sq->whereIn('categories.id', $allCategoryIds);
                    });
                }
            });
        }

        // فلترة البراند
        if (!empty($filters['brand_id'])) {
            $query->where('brand_id', $filters['brand_id']);
        }

        // الترتيب
        $sort = $filters['sort'] ?? 'latest';

        // دالة موحدة لفلترة الموديلات (تستخدم في الترتيب وفي الاستعلام الأساسي)
        $applyVariantFilters = function ($q) use ($filters, $priceColumn, $locale) {
            $q->whereNotNull($priceColumn)->where($priceColumn, '>', 0);

            if (!empty($filters['min_price'])) {
                $q->where($priceColumn, '>=', $filters['min_price']);
            }
            if (!empty($filters['max_price'])) {
                $q->where($priceColumn, '<=', $filters['max_price']);
            }

            if (!empty($filters['attribute_ids'])) {
                $selectedAttributes = \App\Models\Attribute::whereIn('id', $filters['attribute_ids'])->get();
                if ($selectedAttributes->isNotEmpty()) {
                    $groupedAttributes = $selectedAttributes->groupBy('key_en');

                    foreach ($groupedAttributes as $keyEn => $group) {
                        $q->whereHas('variantAttributes', function ($sq) use ($group, $locale) {
                            $keyColumn = $locale === 'ar' ? 'key_ar' : 'key_en';
                            $valueColumn = $locale === 'ar' ? 'value_ar' : 'value_en';

                            $sq->where($keyColumn, $group[0][$keyColumn])
                              ->whereIn($valueColumn, $group->pluck($valueColumn)->toArray());
                        });
                    }
                }
            }
        };

        // ---- فلترة الموديلات (السعر والخصائص معاً في نفس الموديل) ----
        if (!empty($filters['min_price']) || !empty($filters['max_price']) || !empty($filters['attribute_ids'])) {
            $query->whereHas('variants', $applyVariantFilters);
        }

        switch ($sort) {
            case 'offers':
                $query->whereHas('variants.offers', function ($q) use ($now, $offerColumn) {
                    $q->whereNotNull($offerColumn)
                      ->where($offerColumn, '>', 0)
                      ->where('start', '<=', $now)
                      ->where('end', '>=', $now);
                })->orderByDesc('created_at')->orderByDesc('id');
                break;

            case 'popular':
                $query->withCount('reviews')->orderByDesc('reviews_count');
                break;

            case 'low_high':
                $query->withMin(['variants' => $applyVariantFilters], $priceColumn)
                      ->orderBy('variants_min_' . $priceColumn)
                      ->orderByDesc('id');
                break;

            case 'high_low':
                $query->withMax(['variants' => $applyVariantFilters], $priceColumn)
                      ->orderByDesc('variants_max_' . $priceColumn)
                      ->orderByDesc('id');
                break;

            case 'a_z':
                $nameCol = $locale === 'ar' ? 'name_ar' : 'name_en';
                $query->orderBy($nameCol);
                break;

            case 'z_a':
                $nameCol = $locale === 'ar' ? 'name_ar' : 'name_en';
                $query->orderByDesc($nameCol);
                break;

            case 'latest':
                // جلب المنتجات التي تم إنشاؤها مؤخراً
                $query->where('created_at', '>=', now()->subDays(30));
                $query->orderByDesc('created_at')->orderByDesc('id');
                break;

            default:
                $query->orderByDesc('created_at')->orderByDesc('id');
                break;
        }

        $products = $query->paginate($perPage, ['*'], 'page', $page);

        // تجهيز الخصائص المختارة مسبقاً لمنع استعلامات متكررة (N+1)
        $selectedAttrsGrouped = collect();
        if (!empty($filters['attribute_ids'])) {
            $selectedAttrsGrouped = \App\Models\Attribute::whereIn('id', $filters['attribute_ids'])
                ->get()
                ->groupBy('key_en');
        }

        // تعديل الريسبونس لضمان عرض الموديل (Variant) الذي يطابق الفلتر
        $products->getCollection()->transform(function ($product) use ($locale, $offerColumn, $now, $filters, $priceColumn, $selectedAttrsGrouped, $sort) {
            // 1. تحديد الموديلات المطابقة للفلتر (سعر و خصائص)
            $matchingVariants = $product->variants->filter(function ($v) use ($filters, $priceColumn, $selectedAttrsGrouped, $locale) {
                if (!empty($filters['min_price']) && $v->{$priceColumn} < $filters['min_price'])
                    return false;
                if (!empty($filters['max_price']) && $v->{$priceColumn} > $filters['max_price'])
                    return false;

                foreach ($selectedAttrsGrouped as $keyEn => $group) {
                    $values = $group->pluck($locale === 'ar' ? 'value_ar' : 'value_en')->toArray();
                    $hasAttr = $v->variantAttributes->contains(function ($attr) use ($keyEn, $values) {
                        return $attr->key_en === $keyEn && in_array($attr->value, $values);
                    });
                    if (!$hasAttr)
                        return false;
                }

                return true;
            });

            // 2. اختيار الموديل للعرض (أولوية للعروض من الموديلات المطابقة)
            $variant = $matchingVariants->first(function ($v) use ($offerColumn, $now) {
                return $v->offers->where($offerColumn, '>', 0)->where('start', '<=', $now)->where('end', '>=', $now)->isNotEmpty();
            });

            if (!$variant) {
                if ($sort === 'low_high') {
                    $variant = $matchingVariants->sortBy($priceColumn)->first();
                } elseif ($sort === 'high_low') {
                    $variant = $matchingVariants->sortByDesc($priceColumn)->first();
                } else {
                    $variant = $matchingVariants->first();
                }
            }

            $variant = $variant ?? $product->variants->first();

            $attributes = [];
            $seenValues = [];
            foreach ($product->variants as $v) {
                foreach ($v->variantAttributes as $attr) {
                    if (!empty($filters['only_colors']) && $attr->key_en !== 'color')
                        continue;
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
                'offers' => optional($variant)
                    ?->offers
                    ->filter(function ($offer) use ($offerColumn, $now) {
                        return $offer->{$offerColumn} > 0 && $offer->start <= $now && $offer->end >= $now;
                    })
                    ->map(function ($offer) use ($offerColumn) {
                        return [
                            'id' => $offer->id,
                            'disscount_price' => $offer->{$offerColumn} ?? $offer->disscount_price,
                        ];
                    })
                    ->values(),
                'brand' => $product->brand ? [
                    'id' => $product->brand->id,
                    'name' => $product->brand->name
                ] : (object) [],
                'attributes' => $attributes,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => $products->isEmpty() ? 'لا توجد منتجات مطابقة' : 'تم جلب المنتجات بنجاح',
            'data' => $products,
        ]);
    }
}
