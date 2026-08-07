<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_ar',
        'name_en',
        'image',
    ];

    protected $appends = ['name','image_path'];

    protected $hidden = ['name_ar', 'name_en','image'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
    public function getNameAttribute()
    {
        $locale = app()->getLocale();

        return $this->{"name_{$locale}"} ?? $this->name_en;
    }
    public function getImagePathAttribute()
    {
        return asset('storage/brands/' . $this->image);
    }
}