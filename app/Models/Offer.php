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
        "discount_retail",
        "discount_wholesale"
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
        if (!$this->variant || !$this->variant->product) {
            return 0;
        }

        $type = $this->variant->product->type; // 'wholesale' or 'retail'

        return $type === 'wholesale'
            ? $this->discount_wholesale
            : $this->discount_retail;
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