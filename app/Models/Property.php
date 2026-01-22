<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Property extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_id',
        'wholesale_price',
        'retail_price',
        'main_image',
        'images',
        'min_quantity',
        'stock'
    ];

    protected $appends = ['price', 'image_path',
        'images_path'];

    protected $hidden = ['retail_price', 'wholesale_price', 'main_image', 'images'];

    protected $casts = [
        'images' => 'array',
        'min_quantity' => 'integer',
        'stock' => 'integer',
    ];

    protected static function booted()
    {
        $autoDelete = function ($property) {
            // Ensure product is loaded to check type (crucial for checkout/decrement)
            if (!$property->relationLoaded('product')) {
                $property->load('product');
            }

            if (!$property->product)
                return;

            $type = $property->product->type;
            $minQty = $property->min_quantity ?? 1;

            if ($type === 'retail' && $property->stock <= 0) {
                $property->delete();
            } elseif ($type === 'wholesale' && $property->stock < $minQty) {
                $property->delete();
            }
        };

        static::updated($autoDelete);
        static::created($autoDelete);
    }

    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function offers()
    {
        return $this->hasMany(Offer::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function getPriceAttribute()
    {
        $mode = app()->has('price_mode') ? app('price_mode') : 'wholesale';

        return $mode === 'wholesale'
            ? $this->wholesale_price
            : $this->retail_price;
    }

    public function getImagePathAttribute()
    {
        if (!$this->main_image)
            return asset('dashboard/assets/img/placeholder.png');

        if (filter_var($this->main_image, FILTER_VALIDATE_URL)) {
            return $this->main_image;
        }

        return asset('storage/products/' . $this->main_image);
    }

    public function getImagesPathAttribute()
    {
        if (!$this->images || !is_array($this->images)) {
            return [];
        }

        return collect($this->images)->map(function ($img) {
            if (filter_var($img, FILTER_VALIDATE_URL)) {
                return $img;
            }
            return asset('storage/products/' . $img);
        })->toArray();
    }

    public function variantAttributes()
    {
        return $this->hasMany(Attribute::class);
    }

    public function cart()
    {
        return $this->belongsToMany(Cart::class, 'cart_items')->withPivot('quantity', 'line_total', 'unit_price', 'sale_type')->withTimestamps();
    }

    public function orders()
    {
        return $this
            ->belongsToMany(Order::class, 'orders_items', 'property_id', 'order_id')
            ->withPivot(['quantity', 'price', 'sale_type']);
    }
}
