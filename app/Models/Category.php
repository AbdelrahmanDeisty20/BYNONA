<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_ar',
        'name_en',
        'parent_id',
        'sort_order',
        'image',
    ];

    protected $appends = ['name', 'image_path'];

    protected $hidden = ['name_ar', 'name_en', 'image', 'parent_id'];

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id')->with('children');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    public function getNameAttribute()
    {
        $locale = app()->getLocale();

        return $this->{"name_{$locale}"} ?? $this->name_en;
    }

    public function getImagePathAttribute()
    {
        return asset('storage/categories/' . $this->image);
    }
}
