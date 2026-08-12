<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Load products with their variants, attributes, categories, and brands
        return Product::with(['variants.variantAttributes', 'categories', 'brand'])->get();
    }

    /**
     * @var Product $product
     */
    public function map($product): array
    {
        $rows = [];

        // If product has no variants, export the product info once
        if ($product->variants->isEmpty()) {
            $rows[] = $this->formatRow($product, null, null);
            return $rows;
        }

        // Each variant gets its own row(s)
        foreach ($product->variants as $variant) {
            // If variant has no attributes, export one row
            if ($variant->variantAttributes->isEmpty()) {
                $rows[] = $this->formatRow($product, $variant, null);
            } else {
                // Export one row for EACH attribute (User requested repeating rows)
                foreach ($variant->variantAttributes as $attr) {
                    $rows[] = $this->formatRow($product, $variant, $attr);
                }
            }
        }

        return $rows;
    }

    private function formatRow($product, $variant, $attr)
    {
        $category = $product->category ?? $product->categories;
        $brand = $product->brand;

        // Determine the relevant price based on product type
        $price = 0;
        if ($variant) {
            $price = ($product->type === 'retail') ? $variant->retail_price : $variant->wholesale_price;
        }

        return [
            $product->name_ar,
            $product->name_en,
            $product->desc_ar,
            $product->desc_en,
            $product->type,
            $category ? $category->name_ar : '',
            $category ? $category->name_en : '',
            $brand ? $brand->name_ar : '',
            $brand ? $brand->name_en : '',
            $price,
            $variant ? $variant->stock : 0,
            $variant ? $variant->min_quantity : 1,
            // Attribute columns (single set, repeated rows)
            $attr ? $attr->key_ar : '',
            $attr ? $attr->key_en : '',
            $attr ? $attr->value_ar : '',
            $attr ? $attr->value_en : '',
            $variant ? $variant->main_image : '',
            $variant && is_array($variant->images) ? implode(',', $variant->images) : ($variant->images ?? ''),
        ];
    }

    public function headings(): array
    {
        return [
            'product_name_ar',
            'product_name_en',
            'description_ar',
            'description_en',
            'type',
            'category_name_ar',
            'category_name_en',
            'brand_name_ar',
            'brand_name_en',
            'price',
            'stock',
            'min_quantity',
            'key_ar',
            'key_en',
            'value_ar',
            'value_en',
            'main_image_url',
            'other_images_urls',
        ];
    }
}
