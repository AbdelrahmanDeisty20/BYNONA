<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductBanner extends Model
{
    use HasFactory;
    protected $fillable =[
        'title_ar',
        'title_en',
        'desc_ar',
        'desc_en',
        "price",
        "image"
    ];
    protected $appends = ['title', 'desc', 'image_path'];
        protected $hidden = ['title_ar', 'title_en', 'desc_en', 'desc_ar'];

    public function getImagePathAttribute()
    {
        return asset('storage/app/public/advertisements/'.$this->image);
    }
    public function getTitleAttribute()
    {
        $locale = app()->getLocale();

        return $this->{"title_{$locale}"} ?? $this->title_en;
    }

    public function getDescAttribute()
    {
        $locale = app()->getLocale();

        return $this->{"desc_{$locale}"} ?? $this->desc_en;
    }
}