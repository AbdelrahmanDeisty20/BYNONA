<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    use HasFactory;

    // الحقول المسموح بالملأ الجماعي
    protected $fillable = [
        'start',
        'end',
        'property_id',
        'discount_retail',
        'discount_wholesale',
        'discount_price',
        'disscount_price',
    ];

    protected $appends = ['disscount_price'];

    protected $hidden = [
        "discount_retail",
        "discount_wholesale",
    ];
    protected $casts = [
        "discount_retail" => "integer",
        "discount_wholesale" => "integer",
    ];
    public function getDisscountPriceAttribute()
    {
        if (!empty($this->attributes['disscount_price'])) {
            return $this->attributes['disscount_price'];
        }
        if (!empty($this->attributes['discount_price'])) {
            return $this->attributes['discount_price'];
        }

        $type = app()->has('price_mode')
            ? app('price_mode')
            : ($this->relationLoaded('variant') && $this->variant && $this->variant->relationLoaded('product') && $this->variant->product
                ? $this->variant->product->type
                : null);

        if ($type === 'wholesale') {
            return $this->discount_wholesale ?? $this->discount_retail ?? 0;
        }
        if ($type === 'retail') {
            return $this->discount_retail ?? $this->discount_wholesale ?? 0;
        }

        return $this->discount_retail ?? $this->discount_wholesale ?? 0;
    }

    /**
     * العلاقة مع المنتج
     * كل عرض ينتمي لمنتج واحد
     */
    public function variant()
    {
        return $this->belongsTo(Property::class, 'property_id');
    }

    /**
     * إرجاع عنوان العرض حسب اللغة الحالية
     */
    public function getTitleAttribute()
    {
        $locale = app()->getLocale();

        return $this->{"title_{$locale}"} ?? $this->title_en;
    }

    /**
     * إرجاع وصف العرض حسب اللغة الحالية
     */
    public function getDescAttribute()
    {
        $locale = app()->getLocale();

        return $this->{"desc_{$locale}"} ?? $this->desc_en;
    }
    
}