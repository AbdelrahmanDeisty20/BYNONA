<?php

namespace App\Service;

use App\Models\Product;
use Illuminate\Support\Facades\Response;

class ProductExportService
{
    /**
     * Export products to a CSV string
     *
     * @return string
     */
    public function exportToCsv()
    {
        $products = Product::with(['brand', 'categories', 'variants.variantAttributes'])->get();

        $handle = fopen('php://temp', 'w+');

        // Add UTF-8 BOM for Excel compatibility with Arabic
        fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Header
        fputcsv($handle, [
            'name_ar', 'name_en', 'desc_ar', 'desc_en', 'type',
            'category', 'brand', 'retail_price', 'wholesale_price',
            'stock', 'min_quantity', 'main_image', 'attributes'
        ]);

        foreach ($products as $product) {
            foreach ($product->variants as $variant) {
                // Categories
                $categories = $product->categories->pluck('name_en')->implode(',');

                // Attributes: Color:Red;Size:XL
                $attributes = $variant->variantAttributes->map(function ($attr) {
                    return $attr->key_en . ':' . $attr->value_en;
                })->implode(';');

                fputcsv($handle, [
                    $product->name_ar,
                    $product->name_en,
                    $product->desc_ar,
                    $product->desc_en,
                    $product->type,
                    $categories,
                    $product->brand->name_en ?? '',
                    $variant->retail_price,
                    $variant->wholesale_price,
                    $variant->stock,
                    $variant->min_quantity,
                    $variant->main_image,
                    $attributes
                ]);
            }
        }

        rewind($handle);
        $csvContent = stream_get_contents($handle);
        fclose($handle);

        return $csvContent;
    }
}
