<?php

namespace App\Models;

use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name_ar',
        'name_en',
        'desc_ar',
        'desc_en',
        'brand_id',
        'category_id',
        'type'
    ];

    protected $appends = [
        'name',
        'desc',
    ];

    protected $hidden = [
        'name_ar',
        'name_en',
        'desc_en',
        'desc_ar',
    ];

    protected $casts = [
        'name_ar' => 'string',
        'name_en' => 'string',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function categories()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function getNameAttribute()
    {
        $locale = app()->getLocale();

        return $this->{"name_{$locale}"} ?? $this->name_en;
    }

    public function getDescAttribute()
    {
        $locale = app()->getLocale();

        return $this->{"desc_{$locale}"} ?? $this->desc_en;
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function specifics()
    {
        return $this->hasMany(Specific::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function offers()
    {
        return $this->hasMany(Offer::class);
    }

    public function variants()
    {
        return $this->hasMany(Property::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
