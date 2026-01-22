<?php

namespace App\Service;

use Illuminate\Support\Facades\{Cache, DB};
use App\Models\Product;

class ProductFilterService
{
    protected int $cacheTime = 1800; // 30 دقيقة

    protected int $perPage = 20;

    public function filter(array $filters, int $page = 1)
    {
        $query = Product::query()->with(['properties', 'brand', 'categories']);

        // فلترة حسب category
        if (! empty($filters['category'])) {
            $category = strtolower(trim($filters['category']));
            $query->whereHas('categories', function ($q) use ($category) {
                $q->whereRaw('LOWER(name_ar) LIKE ?', ["%{$category}%"])
                    ->orWhereRaw('LOWER(name_en) LIKE ?', ["%{$category}%"]);
            });
        }

        // فلترة حسب brand
        if (! empty($filters['brand'])) {
            $brand = strtolower(trim($filters['brand']));
            $query->whereRaw('LOWER(brand) LIKE ?', ["%{$brand}%"]);
        }

        // فلترة حسب السعر
        $priceColumn = app('price_mode') === 'wholesale' ? 'wholesale_price' : 'retail_price';
        if (! empty($filters['min_price'])) {
            $query->where($priceColumn, '>=', $filters['min_price']);
        }
        if (! empty($filters['max_price'])) {
            $query->where($priceColumn, '<=', $filters['max_price']);
        }

        // فلترة حسب اللون
        if (! empty($filters['properties']) && is_array($filters['properties'])) {

            foreach ($filters['properties'] as $key => $values) {

                $query->whereHas('properties', function ($q) use ($key, $values) {

                    // الخصائص عندك فيها key_ar و key_en + value_ar و value_en
                    $q->where(function ($sub) use ($key) {
                        $sub->whereRaw('LOWER(key_ar) = ?', [strtolower($key)])
                            ->orWhereRaw('LOWER(key_en) = ?', [strtolower($key)]);
                    });

                    $q->where(function ($sub) use ($values) {
                        $values = array_map('strtolower', $values);

                        $sub->whereIn(DB::raw('LOWER(value_ar)'), $values)
                            ->orWhereIn(DB::raw('LOWER(value_en)'), $values);
                    });

                });
            }
        }

        // ترتيب Sort
        if (! empty($filters['sort'])) {
            switch ($filters['sort']) {
                case 'popular': $query->orderBy('sales_count', 'desc');
                    break;
                case 'a_z': $query->orderBy('name_en', 'asc');
                    break;
                case 'z_a': $query->orderBy('name_en', 'desc');
                    break;
                case 'low_high': $query->orderBy($priceColumn, 'asc');
                    break;
                case 'high_low': $query->orderBy($priceColumn, 'desc');
                    break;
                case 'latest': $query->orderBy('created_at', 'desc');
                    break;
                case 'featured_discount':
                    $query->whereNotNull('disscount_price')->orderBy('disscount_price', 'desc');
                    break;
            }
        }

        // Cache Key لكل combination + page
        $cacheKey = 'product_filter_'.md5(json_encode($filters)."_page_{$page}");

        // جلب النتائج مع Cache + Pagination
        return Cache::remember($cacheKey, $this->cacheTime, fn () => $query->paginate($this->perPage, ['*'], 'page', $page)
        );
    }
}