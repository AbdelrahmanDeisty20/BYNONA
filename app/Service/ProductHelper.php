<?php

namespace App\Service;

use App\Models\{Notification, User};

class ProductHelper
{
    public static function getAttributesString($property, $lang = 'ar')
    {
        $property->loadMissing('variantAttributes');
        if ($property->variantAttributes->isEmpty()) {
            return '';
        }

        return $property->variantAttributes->map(function ($attr) use ($lang) {
            $key = $attr->{"key_{$lang}"} ?? $attr->key_en;
            $value = $attr->{"value_{$lang}"} ?? $attr->value_en;
            return "{$key}: {$value}";
        })->implode(', ');
    }

    public static function checkProductQuantity($property)
    {
        // Trigger only at specific thresholds: 10 (Low) or 0 (Out of stock)
        if ($property->stock != 10 && $property->stock != 0) return;

        // Ensure relations are loaded
        $property->loadMissing(['product', 'variantAttributes']);
        if (!$property->product) return;

        $attributesAr = self::getAttributesString($property, 'ar');
        $attributesEn = self::getAttributesString($property, 'en');
        $attrStringAr = $attributesAr ? " ($attributesAr)" : "";
        $attrStringEn = $attributesEn ? " ($attributesEn)" : "";

        if ($property->stock == 10) {
            $title = 'كمية المنتج منخفضة / Product Low Stock';
            $body = "المنتج {$property->product->name_ar}{$attrStringAr} أوشكت كميته على النفاد (المتبقي 10) | Product {$property->product->name_en}{$attrStringEn} is low on stock (10 remaining)";
        } else {
            $title = 'نفدت كمية المنتج / Product Out of Stock';
            $body = "نفدت كمية المنتج {$property->product->name_ar}{$attrStringAr} تماماً | Product {$property->product->name_en}{$attrStringEn} is now out of stock";
        }

        // Get all admins
        $admins = User::where('user_type', 'admin')->get();

        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'title'   => $title,
                'body'    => $body,
                'type'    => $property->stock == 0 ? 'out_of_stock_admin' : 'low_stock_admin',
                'data'    => [
                    'property_id' => $property->id,
                    'product_id'  => $property->product_id,
                    'stock'       => $property->stock
                ],
                'is_read' => false,
            ]);
        }
    }
}