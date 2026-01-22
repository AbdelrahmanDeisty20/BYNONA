<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'image',
        'short_desc_ar',
        'short_desc_en',
        'title_ar',
        'title_en',
    ];

    protected $appends = ['title', 'desc', 'image_path'];

    protected $hidden = [
        "short_desc_ar",
        "short_desc_en",
        "title_ar",
        "title_en"
    ];

    public function getImagePathAttribute()
    {
        return asset('storage/app/public/banners/'.$this->image);
    }
    public function getDescAttribute()
    {
        $locale = app()->getLocale();

        return $this->{"short_desc_{$locale}"} ?? $this->desc_en;
    }
    public function getTitleAttribute()
    {
        $locale = app()->getLocale();

        return $this->{"title_{$locale}"} ?? $this->name_en;
    }

}